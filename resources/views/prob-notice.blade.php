<?php

use App\Models\Prize;

$current_probability = floatval(Prize::sum('probability'));
$remaining_probability = 100 - $current_probability;
?>
{{-- TODO: add Message logic here --}}
@if ($remaining_probability != 0)
    <div class="alert alert-danger">
        Sum of all prizes probability must be 100%. currently its {{ $current_probability }}% You have yet to add
        {{ $remaining_probability }}% of
        the prize.
    </div>
@endif
