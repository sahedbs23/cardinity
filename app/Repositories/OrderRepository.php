<?php


namespace App\Repositories;


use App\Models\Order;

class OrderRepository extends AbstractEloquentRepository
{
    protected $modelName = Order::class;
}
