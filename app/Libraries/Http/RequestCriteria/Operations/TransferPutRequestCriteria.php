<?php

namespace App\Libraries\Http\RequestCriteria\Operations;

/**
 * Class TransferPutRequestCriteria
 * @package App\Libraries\Http\RequestCriteria\Transactions
 */
class TransferPutRequestCriteria
{
    public $amount;
    public $cardNumber;

    /**
     * DepositPutRequestCriteria constructor.
     * @param array $requestArray
     */
    public function __construct(array $requestArray)
    {
        $this->amount   = $requestArray['amount'];
        $this->cardNumber  = $requestArray['card_number'];
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @param mixed $cardNumber
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
    }
}