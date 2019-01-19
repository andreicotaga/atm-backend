<?php

namespace App\Libraries\Http\RequestCriteria\Operations;

/**
 * Class DepositPutRequestCriteria
 * @package App\Libraries\Http\RequestCriteria\Transactions
 */
class DepositPutRequestCriteria
{
    public $amount;
    public $comment;

    /**
     * DepositPutRequestCriteria constructor.
     * @param array $requestArray
     */
    public function __construct(array $requestArray)
    {
        $this->amount   = $requestArray['amount'];
        $this->comment  = isset($requestArray['comment']) ? $requestArray['comment'] : null;
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
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }
}