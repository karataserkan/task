<?php

namespace app\models;

/**
 * Operation Model.
 */
class OperationModel extends BaseModel
{
    const TYPE_CASH_IN = 'cash_in';
    const TYPE_CASH_OUT = 'cash_out';

    private $date;
    private $type;
    private $amount;
    private $currency;

    /**
     * Sets operation date.
     *
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Sets operation type.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Sets operation amount.
     *
     * @param string $amount
     */
    public function setAmount($amount)
    {
        $this->amount = (float) $amount;
    }

    /**
     * Sets operation currency.
     *
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Returns operation date.
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Returns operation type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns operation amount.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Returns operation currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}
