<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/18/2019
 * Time: 12:53 AM
 */

namespace App\Libraries\Cashier;

use App\Libraries\Http\RequestCriteria\Operations\TransferPutRequestCriteria;
use App\Libraries\Http\RequestCriteria\Operations\WithdrawPutRequestCriteria;
use App\Transactions;
use Illuminate\Support\Facades\Auth;
use App\Cards;
use App\Libraries\Http\RequestCriteria\Operations\DepositPutRequestCriteria;

class CashierManager
{
    const DEPOSIT = 'DP';
    const WITHDRAW = 'WD';
    const TRANSFER = 'TF';

    public static function deposit(DepositPutRequestCriteria $criteria)
    {
        $user = Auth::user();

        if(!empty($user))
        {
            $inserted = Transactions::insert([
                'card_id' => $user->card_number,
                'amount' => (float)$criteria->getAmount(),
                'oper_code' => self::DEPOSIT,
                'comment' => $criteria->getComment(),
            ]);

            if($inserted)
            {
                Cards::where('card_number', $user->card_number)
                    ->update(['balance' => (float)$user->balance + (float)$criteria->getAmount()]);
            }
        }

        return [
            'status' => $inserted ? 'SUCCESS' : 'FAILURE',
            'data' => Cards::where('card_number', '=', $user->card_number)->first()->toArray()
        ];
    }

    public static function withdraw(WithdrawPutRequestCriteria $criteria)
    {
        $user = Auth::user();

        if(!empty($user))
        {
            $currentData = Cards::where('card_number', '=', $user->card_number)->first()->toArray();

            if(((float)$currentData['balance'] - (float)$criteria->getAmount()) < -100)
            {
                return [
                    'status' => 'FAILURE',
                    'error' => 'You are not allowed to have negative balance less the -100'
                ];
            }

            $inserted = Transactions::insert([
                'card_id' => $user->card_number,
                'amount' => (float)$criteria->getAmount(),
                'oper_code' => self::WITHDRAW,
                'comment' => $criteria->getComment(),
            ]);

            if($inserted)
            {
                Cards::where('card_number', $user->card_number)
                    ->update(['balance' => (float)$user->balance - ( float)$criteria->getAmount()]);
            }
        }

        return [
            'status' => $inserted ? 'SUCCESS' : 'FAILURE',
            'data' => Cards::where('card_number', '=', $user->card_number)->first()->toArray()
        ];
    }

    public static function transactions()
    {
        $user = Auth::user();

        if(!empty($user))
        {
            $transactions = Transactions::where('card_id', '=', $user->card_number)->get();
        }

        return [
            'data' => $transactions
        ];
    }

    public static function transfer(TransferPutRequestCriteria $criteria)
    {
        $user = Auth::user();

        if(!empty($user))
        {
            $currentData = Cards::where('card_number', '=', $user->card_number)->first()->toArray();

            if($criteria->getCardNumber() === $currentData['card_number'])
            {
                return [
                    'status' => 'FAILURE',
                    'error' => 'You are not allowed to transfer money to your own account!'
                ];
            }

            if(((float)$currentData['balance'] - (float)$criteria->getAmount()) < -100)
            {
                return [
                    'status' => 'FAILURE',
                    'error' => 'You are not allowed to have negative balance less the -100'
                ];
            }

            $cardTransferDetails = Cards::where('card_number', '=', $criteria->getCardNumber())->first();

            if(empty($cardTransferDetails))
            {
                return ['status' => 'FAILURE', 'error' => 'There is no account with card number: ' . $criteria->getCardNumber()];
            }

            if(!empty($cardTransferDetails))
            {
                $cardTransferDetails->toArray();

                if((bool)$cardTransferDetails['blocked'])
                {
                    return ['status' => 'FAILURE', 'error' => 'User\'s card was blocked! This operation is not allowed'];
                }

                Cards::where('card_number', $user->card_number)
                    ->update(['balance' => (float)$user->balance - (float)$criteria->getAmount()]);

                Cards::where('card_number', $criteria->getCardNumber())
                    ->update(['balance' => (float)$cardTransferDetails['balance'] + (float)$criteria->getAmount()]);

                Transactions::insert([
                    'card_id' => $user->card_number,
                    'amount' => (float)$criteria->getAmount(),
                    'oper_code' => self::TRANSFER
                ]);
            }
        }

        return [
            'status' => 'SUCCESS',
            'data' => Cards::where('card_number', '=', $user->card_number)->first()->toArray()
        ];
    }
}