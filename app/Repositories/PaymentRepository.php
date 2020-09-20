<?php


namespace App\Repositories;


use App\Models\Payment;

class PaymentRepository extends AbstractEloquentRepository
{
    protected $modelName = Payment::class;

}
