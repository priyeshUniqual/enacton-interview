<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Prize;
use App\Http\Requests\PrizeRequest;
use Illuminate\Http\Request;



class PrizesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $prizes = Prize::all();

        $titles = Prize::pluck('title')->toArray();
        $probability = Prize::pluck('probability')->toArray();

        $sumAwarded = Prize::sum('awarded');
        $awardedPercentage = [];
        foreach ($prizes as $value) {
            $percentage = ($value->awarded / 100) * $sumAwarded;
            array_push($awardedPercentage, round($percentage, 2));
        }
        $actualRewards = [
            'labels' => $titles,
            'data' => $awardedPercentage,
        ];


        $probabilityData = [
            'labels' => $titles,
            'data' => $probability,
        ];
        // dd($actualRewards);
        return view('prizes.index', ['prizes' => $prizes, 'probabilityData' => $probabilityData, 'actualRewards' => $actualRewards]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('prizes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PrizeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PrizeRequest $request)
    {
        $current_probability = floatval(Prize::sum('probability'));
        $remaining_probability = 100 - $current_probability;
        if (round($request->input('probability'), 2) > round($remaining_probability, 2)) {
            return back()->withErrors('The probability field must not be greater than ' . $remaining_probability)->withInput();
        }
        $prize = new Prize;
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        return to_route('prizes.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $prize = Prize::findOrFail($id);
        return view('prizes.edit', ['prize' => $prize]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PrizeRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PrizeRequest $request, $id)
    {
        $prize = Prize::findOrFail($id);
        $current_probability = floatval(Prize::sum('probability'));
        if ($current_probability == 100  && round($request->input('probability'), 2)  > round($prize->probability, 2)) {
            return back()->withErrors('The probability field must not be greater than ' . $prize->probability)->withInput();
        }
        $remaining_probability = 100 - ($current_probability - round($prize->probability, 2));
        if (round($request->input('probability'), 2) > round($remaining_probability, 2)) {
            return back()->withErrors('The probability field must not be greater than ' . $remaining_probability)->withInput();
        }
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        return to_route('prizes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $prize = Prize::findOrFail($id);
        $prize->delete();

        return to_route('prizes.index');
    }


    public function simulate(Request $request)
    {

        $prizes =  Prize::orderBy('id', 'ASC')->get();
        $probabilities = [];
        $data = [];
        foreach ($prizes as $value) {
            array_push($probabilities, round($value->probability, 2) / 100);
            array_push($data, 0);
        }

        for ($i = 0; $i < $request->number_of_prizes ?? 10; $i++) {
            $productIndex  = Prize::nextPrize($probabilities);
            $value = $data[$productIndex];
            $data[$productIndex] = $value + 1;
        }

        $i = 0;
        foreach ($prizes as $value) {
            Prize::where('id', $value->id)->update(['awarded' => $value->awarded + $data[$i]]);
            $i++;
        }

        return to_route('prizes.index');
    }

    public function reset()
    {
        Prize::where('id', '!=', 0)->update(['awarded' => 0]);

        return to_route('prizes.index');
    }
}
