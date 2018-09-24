<?php

namespace app\helpers;

use app\models\OperationModel;
use app\models\UserModel;

/**
 * CommissionHelper includes commission operations.
 */
class CommissionHelper
{
    /**
     * Calculates commission for an operation.
     *
     * @param object $operation          OperationModel object
     * @param array  $previousOperations Array of OperationModel objects
     * @param string $userType           Type of User, one of TYPE_NATURAL | TYPE_LEGAL
     *
     * @return string
     */
    public static function calculate($operation, $previousOperations = [], $userType)
    {
        //for cash in operations call calculateCashInCommission()
        //for cash out operations call calculateCashOutCommission()
        if ($operation->getType() == OperationModel::TYPE_CASH_IN) {
            return self::calculateCashInCommission($operation);
        } elseif ($operation->getType() == OperationModel::TYPE_CASH_OUT) {
            return self::calculateCashOutCommission($operation, $previousOperations, $userType);
        } else {
            throw new \InvalidArgumentException('Invalid operation type: '.$operation->getType(), 1);
        }
    }

    /**
     * Calculates cash in commission.
     *
     * @param object $operation OperationModel object
     *
     * @return string
     */
    public static function calculateCashInCommission($operation)
    {
        //get cash in commission fee
        $config = $GLOBALS['config'];
        if (!isset($config['cashInCommissionFee'])) {
            throw new \OutOfRangeException('Please specify cash in commission fee rate', 1);
        }

        //calculate fee, operation amount / 100 * provided commission rate 
        $calculated = ($operation->getAmount() / 100) * $config['cashInCommissionFee'];

        //apply max cash in fee if provided 
        $fee = self::applyMaxCashInFee($calculated, $operation->getCurrency());

        return CurrencyHelper::format($fee, $operation->getCurrency());
    }

    /**
     * Calculates cash out commission.
     *
     * @param object $operation          OperationModel object
     * @param array  $previousOperations Array of OperationModel objects
     * @param string $userType           Type of User, one of TYPE_NATURAL | TYPE_LEGAL
     *
     * @return string
     */
    public static function calculateCashOutCommission($operation, $previousOperations = [], $userType)
    {
        //get cash out commission fee
        $config = $GLOBALS['config'];
        if (!isset($config['cashOutCommissionFee'])) {
            throw new \OutOfRangeException('Please specify cash out commission fee', 1);
        }

        //calculate for natural or legal user
        if ($userType == UserModel::TYPE_NATURAL) {
            //amount to be charged after discount applied
            $amount = self::applyDiscount($operation, $previousOperations);

            //calculate fee, amount / 100 * provided commission rate 
            $fee = $amount / 100 * $config['cashOutCommissionFee'];
        } elseif ($userType == UserModel::TYPE_LEGAL) {
            //calculate fee, operation amount / 100 * provided commission rate 
            $fee = $operation->getAmount() / 100 * $config['cashOutCommissionFee'];

            //if min cash out commission fee specified, apply rule
            if (isset($config['minCashOutCommissionFeeCurrency']) || isset($config['minCashOutCommissionFeeAmount'])) {
                $baseCurrency = $config['minCashOutCommissionFeeCurrency'];

                //convert calculated fee from operation curreny to specified curreny to compare amounts
                $feeInBaseCurrency = CurrencyHelper::convert($operation->getCurrency(), $baseCurrency, $fee);

                //if fee is under min fee amount, apply rule
                if ($feeInBaseCurrency < $config['minCashOutCommissionFeeAmount']) {
                    //fee must be in operation currency
                    $fee = CurrencyHelper::convert($baseCurrency, $operation->getCurrency(), $config['minCashOutCommissionFeeAmount']);
                }
            }
        } else {
            throw new \InvalidArgumentException('Please specify correct user type', 1);
        }

        return CurrencyHelper::format($fee, $operation->getCurrency());
    }

    /**
     * Applies week discounts for cash out amount if specified.
     *
     * @param object $currentOperation OperationModel object
     * @param array  $operations       Array of OperationModel objects
     *
     * @return float
     */
    public static function applyDiscount($currentOperation, $operations = [])
    {
        $config = $GLOBALS['config'];

        //if week cash out operations amount and/or it's currency is not provided, skip discount
        if (!isset($config['weeklyFreeCashOutAmount']) || !isset($config['weeklyFreeCashOutCurrency'])) {
            return $currentOperation->getAmount();
        }

        $specifiedCurrency = $config['weeklyFreeCashOutCurrency'];
        $specifiedDiscountAmount = $config['weeklyFreeCashOutAmount'];
        $operationDate = new \DateTime($currentOperation->getDate());

        //finds monday of operation week
        if ($operationDate->format('w') === 1) {
            $monday = clone $operationDate;
        } else {
            $monday = clone $operationDate->modify('previous monday');
        }

        //finds sunday of operation week
        $sunday = clone $operationDate->modify('sunday');

        //week amount in specified currency
        $weekAmount = 0;
        $appliedOperationCount = 0;
        foreach ($operations as $key => $operation) {
            $date = new \DateTime($operation->getDate());

            //calculate if operation is cash out and operation date is between current operation week
            if ($operation->getType() == OperationModel::TYPE_CASH_OUT && $date <=  $sunday && $date >= $monday) {
                ++$appliedOperationCount;
                //week amount currency must be specified currency
                $weekAmount += CurrencyHelper::convert($operation->getCurrency(), $specifiedCurrency, $operation->getAmount());
            }
        }

        //This discount is applied only for first 3 cash out operations per week for each user
        if ($appliedOperationCount >= 3) {
            return $currentOperation->getAmount();
        }

        //check if specified discount amount exceeded
        if ($weekAmount > $specifiedDiscountAmount) {
            return $currentOperation->getAmount();
        }

        //convert amount to specified currency to compare
        $amountWithSpecifiedCurrency = CurrencyHelper::convert($currentOperation->getCurrency(), $specifiedCurrency, $currentOperation->getAmount());

        //gets remained discount amount
        $amountRemained = $specifiedDiscountAmount - $weekAmount;

        //If amount is equals or smaller than remained discount amount, apply discount, fee is 0
        //Else commission is calculated only from exceeded amount . Ex: Cash out amount is 1500 EUR and remained amount is 900 EUR. Calculate commission for 600 (1500-900) EUR
        if ($amountWithSpecifiedCurrency <= $amountRemained) {
            return 0;
        } else {
            //fee must be in operation currency
            return CurrencyHelper::convert($specifiedCurrency, $currentOperation->getCurrency(), ($amountWithSpecifiedCurrency - $amountRemained));
        }
    }

    /**
     * Applies max cash in fee if provided.
     *
     * @param float  $amount   Operation amount
     * @param string $currency Operation currency
     *
     * @return float
     */
    public static function applyMaxCashInFee($amount, $currency)
    {
        //if max cash in fee amount and/or it's currency is not provided, skip rule
        $config = $GLOBALS['config'];
        if (!isset($config['maxCashInCommissionFeeCurrency']) || !isset($config['maxCashInCommissionFeeAmount'])) {
            return $amount;
        }

        //convert amount to specified currency
        $converted = CurrencyHelper::convert($currency, $config['maxCashInCommissionFeeCurrency'], $amount);

        //check for max cash in fee
        if ($converted > $config['maxCashInCommissionFeeAmount']) {
            return CurrencyHelper::convert($config['maxCashInCommissionFeeCurrency'], $currency ,$config['maxCashInCommissionFeeAmount']);
        }

        return $amount;
    }
}
