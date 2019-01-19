<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/18/2019
 * Time: 12:51 AM
 */

namespace App\Http\Controllers\Operations;

use App\Libraries\Http\RequestCriteria\Operations\TransferPutRequestCriteria;
use App\Libraries\Http\RequestCriteria\Operations\WithdrawPutRequestCriteria;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Libraries\Cashier\CashierManager;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Libraries\Http\RequestCriteria\Operations\DepositPutRequestCriteria;

class CashierOperationsController extends BaseController
{
    public function deposit(Request $request)
    {
        $criteria = new DepositPutRequestCriteria($request->toArray());

        $results  = CashierManager::deposit($criteria);

        $response = new Response();
        $response->setContent($results);
        $response->withHeaders(['Content-Type' => 'application/json']);

        return $response;
    }

    public function withdraw(Request $request)
    {
        $criteria = new WithdrawPutRequestCriteria($request->toArray());

        $results  = CashierManager::withdraw($criteria);

        $response = new Response();
        $response->setContent($results);
        $response->withHeaders(['Content-Type' => 'application/json']);

        return $response;
    }

    public function transactions()
    {
        $results  = CashierManager::transactions();

        $response = new Response();
        $response->setContent($results);
        $response->withHeaders(['Content-Type' => 'application/json']);

        return $response;
    }

    public function transfer(Request $request)
    {
        $criteria = new TransferPutRequestCriteria($request->toArray());

        $results  = CashierManager::transfer($criteria);

        $response = new Response();
        $response->setContent($results);
        $response->withHeaders(['Content-Type' => 'application/json']);

        return $response;
    }
}