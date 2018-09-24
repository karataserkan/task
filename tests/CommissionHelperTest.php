<?php

declare (strict_types = 1);

namespace app\tests;

use PHPUnit\Framework\TestCase;
use app\helpers\CommissionHelper;
use app\models\OperationModel;

final class CommissionHelperTest extends TestCase
{
    public function testCanCalculateCashIn()
    {
        $operation = new OperationModel();
        $operation->setDate('2018-09-11');
        $operation->setType('cash_in');
        $operation->setAmount('1000.00');
        $operation->setCurrency('EUR');

        $this->assertEquals('0.30', CommissionHelper::calculate($operation, [], 'legal'));
    }

    public function testCanCalculateMaxCashIn()
    {
        $operation = new OperationModel();
        $operation->setDate('2018-09-11');
        $operation->setType('cash_in');
        $operation->setAmount('10000000000000.00');
        $operation->setCurrency('JPY');

        $this->assertEquals('648', CommissionHelper::calculate($operation, [], 'legal'));
    }

    public function testCanCalculateCashInCommission()
    {
        $operation = new OperationModel();
        $operation->setDate('2018-09-11');
        $operation->setType('cash_in');
        $operation->setAmount('1000.00');
        $operation->setCurrency('EUR');

        $this->assertEquals('0.30', CommissionHelper::calculateCashInCommission($operation));
    }

    public function testCanCalculateCashOutLegalCommission()
    {
        $operation = new OperationModel();
        $operation->setDate('2018-09-11');
        $operation->setType('cash_out');
        $operation->setAmount('1000.00');
        $operation->setCurrency('EUR');

        $this->assertEquals('3.00', CommissionHelper::calculateCashOutCommission($operation, [], 'legal'));
    }

    public function testCanCalculateCashOutLegalMinCommission()
    {
        $operation = new OperationModel();
        $operation->setDate('2018-09-11');
        $operation->setType('cash_out');
        $operation->setAmount('100.00');
        $operation->setCurrency('EUR');

        $this->assertEquals('0.50', CommissionHelper::calculateCashOutCommission($operation, [], 'legal'));
    }

    public function testCanCalculateCashOutNaturalCommissionForFree()
    {
        $operation = new OperationModel();
        $operation->setDate('2018-09-11');
        $operation->setType('cash_out');
        $operation->setAmount('1000.00');
        $operation->setCurrency('EUR');

        $this->assertEquals('0.00', CommissionHelper::calculateCashOutCommission($operation, [], 'natural'));
    }

    public function testCanCalculateCashOutNaturalCommissionWithDiscount()
    {
        $operation = new OperationModel();
        $operation->setDate('2018-09-11');
        $operation->setType('cash_out');
        $operation->setAmount('2000.00');
        $operation->setCurrency('EUR');

        $this->assertEquals('3.00', CommissionHelper::calculateCashOutCommission($operation, [], 'natural'));
    }

    public function testCanCalculateCashOutNaturalCommissionWithDiscountPreviousOperations()
    {
        $previosOperation = new OperationModel();
        $previosOperation->setDate('2018-09-10');
        $previosOperation->setType('cash_out');
        $previosOperation->setAmount('1000.00');
        $previosOperation->setCurrency('EUR');

        $operation = new OperationModel();
        $operation->setDate('2018-09-11');
        $operation->setType('cash_out');
        $operation->setAmount('2000.00');
        $operation->setCurrency('EUR');

        $this->assertEquals('6.00', CommissionHelper::calculateCashOutCommission($operation, [$previosOperation], 'natural'));
    }

    public function testCanApplyDiscount()
    {
        $operation = new OperationModel();
        $operation->setDate('2018-09-11');
        $operation->setType('cash_out');
        $operation->setAmount('2000.00');
        $operation->setCurrency('EUR');

        $this->assertEquals('1000.00', CommissionHelper::applyDiscount($operation, []));
    }

    public function testCanApplyDiscountWithPrevious()
    {
        $previosOperation = new OperationModel();
        $previosOperation->setDate('2018-09-10');
        $previosOperation->setType('cash_out');
        $previosOperation->setAmount('1000.00');
        $previosOperation->setCurrency('EUR');

        $operation = new OperationModel();
        $operation->setDate('2018-09-11');
        $operation->setType('cash_out');
        $operation->setAmount('2000.00');
        $operation->setCurrency('EUR');

        $this->assertEquals('2000.00', CommissionHelper::applyDiscount($operation, [$previosOperation]));
    }

    public function testCanApplyMaxCashInFeeTrue()
    {
        $this->assertEquals('5.00', CommissionHelper::applyMaxCashInFee('100.00', 'EUR'));
    }

    public function testCanApplyMaxCashInFeeFalse()
    {
        $this->assertEquals('1.00', CommissionHelper::applyMaxCashInFee('1.00', 'EUR'));
    }
}
