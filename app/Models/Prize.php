<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Prize extends Model
{

    protected $guarded = ['id'];

    protected $filable = ['title', 'probability', 'awarded'];

    public  static function nextPrize($probabilities)
    {
        $rand = mt_rand() / mt_getrandmax();
        $cumulativeProbability = 0;
        for ($i = 0; $i < count($probabilities); $i++) {
            $cumulativeProbability += $probabilities[$i];
            if ($rand <= $cumulativeProbability) {
                return $i;
            }
        }
        return count($probabilities) - 1;
    }
}
