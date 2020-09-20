<?php


namespace App\Repositories;


use App\Models\OrderDetail;

class OrderDetailsRepository extends AbstractEloquentRepository
{
    protected $modelName = OrderDetail::class;
}
