<?php

declare (strict_types = 1);

namespace app\tests;

use PHPUnit\Framework\TestCase;
use app\models\OperationModel;

final class OperationModelTest extends TestCase
{
    public function testCanSetOperationDate()
    {
        $operation = new OperationModel();
        $value = '2018-09-15';
        $operation->setDate($value);
        $this->assertEquals($value, $operation->getDate());
    }

    public function testCanSetOperationType()
    {
        $operation = new OperationModel();
        $value = 'cash_in';
        $operation->setType($value);
        $this->assertEquals($value, $operation->getType());
    }

    public function testCanSetOperationAmount()
    {
        $operation = new OperationModel();
        $value = '1200.00';
        $operation->setAmount($value);
        $this->assertEquals($value, $operation->getAmount());
    }

    public function testCanSetOperationCurrency()
    {
        $operation = new OperationModel();
        $value = 'EUR';
        $operation->setCurrency($value);
        $this->assertEquals($value, $operation->getCurrency());
    }

    public function testCanGetOperationDate()
    {
        $operation = new OperationModel();
        $value = '2018-09-15';
        $operation->setDate($value);
        $this->assertEquals($operation->getDate(), $value);
    }

    public function testCanGetOperationType()
    {
        $operation = new OperationModel();
        $value = 'cash_in';
        $operation->setType($value);
        $this->assertEquals($operation->getType(), $value);
    }

    public function testCanGetOperationAmount()
    {
        $operation = new OperationModel();
        $value = '1200.00';
        $operation->setAmount($value);
        $this->assertEquals($operation->getAmount(), $value);
    }

    public function testCanGetOperationCurrency()
    {
        $operation = new OperationModel();
        $value = 'EUR';
        $operation->setCurrency($value);
        $this->assertEquals($operation->getCurrency(), $value);
    }
}
