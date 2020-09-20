<?php
/**
 * Created by IntelliJ IDEA.
 * User: jahangir
 * Date: 10/19/17
 * Time: 3:38 PM
 */

namespace App\Repositories;


use App\Models\Product;

class ProductRepository extends AbstractEloquentRepository
{
    protected $modelName = Product::class;

}
