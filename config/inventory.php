<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Low Stock Threshold
    |--------------------------------------------------------------------------
    |
    | Products with stock_on_hand below this threshold will be flagged as low
    | stock. This can be overridden per-request via the ?threshold query param.
    |
    */
    'low_stock_threshold' => env('LOW_STOCK_THRESHOLD', 10),
];
