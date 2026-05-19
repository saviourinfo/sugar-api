<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Trip extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'trips';

    protected $fillable = [
        'sr_no', 'trip_date', 'broker_name', 'mill_name',
        'purchase_qty_qtl', 'purchase_rate_qtl', 'gst_percent',
        'purchase_amount', 'gst_amount', 'net_purchase',
        'fare_total', 'fare_per_qtl', 'labour_total', 'labour_per_bag',
        'tempo_total', 'tempo_per_qtl', 'total_cost',
        'total_sell', 'net_profit', 'avg_purchase_price',
        'supply',
    ];

    protected $casts = [
        'trip_date'          => 'date:Y-m-d',
        'purchase_qty_qtl'   => 'float',
        'purchase_rate_qtl'  => 'float',
        'gst_percent'        => 'float',
        'purchase_amount'    => 'float',
        'gst_amount'         => 'float',
        'net_purchase'       => 'float',
        'fare_total'         => 'float',
        'fare_per_qtl'       => 'float',
        'labour_total'       => 'float',
        'labour_per_bag'     => 'float',
        'tempo_total'        => 'float',
        'tempo_per_qtl'      => 'float',
        'total_cost'         => 'float',
        'total_sell'         => 'float',
        'net_profit'         => 'float',
        'avg_purchase_price' => 'float',
        'supply'             => 'array',
    ];
}