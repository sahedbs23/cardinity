<?php
/**
 * Created by IntelliJ IDEA.
 * User: jahangir
 * Date: 10/19/17
 * Time: 3:38 PM
 */

namespace App\Repositories;


use App\Models\Product;

class CartRepository extends AbstractSessionRepository
{
    public $sessionKey = 'cart';

}
