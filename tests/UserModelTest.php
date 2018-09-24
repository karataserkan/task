<?php

declare (strict_types = 1);

namespace app\tests;

use PHPUnit\Framework\TestCase;
use app\models\UserModel;
use app\models\OperationModel;

final class UserModelTest extends TestCase
{
    public function testCanSetUserId()
    {
        $user = new UserModel();
        $value = '135';
        $user->setId($value);
        $this->assertEquals($value, $user->getId());
    }

    public function testCanGetUserId()
    {
        $user = new UserModel();
        $value = '135';
        $user->setId($value);
        $this->assertEquals($user->getId(), $value);
    }

    public function testCanSetUserType()
    {
        $user = new UserModel();
        $value = 'natural';
        $user->setType($value);
        $this->assertEquals($value, $user->getType());
    }

    public function testCanGetUserType()
    {
        $user = new UserModel();
        $value = 'natural';
        $user->setType($value);
        $this->assertEquals($user->getType(), $value);
    }

    public function testCanAddUserOperation()
    {
        $operation1 = new OperationModel();
        $operation1->setDate('2018-09-11');
        $operation1->setType('cash_out');
        $operation1->setAmount('1000.00');
        $operation1->setCurrency('EUR');

        $operation2 = new OperationModel();
        $operation2->setDate('2018-09-12');
        $operation2->setType('cash_in');
        $operation2->setAmount('1000.00');
        $operation2->setCurrency('USD');

        $user = new UserModel();
        $user->addOperation($operation1);
        $user->addOperation($operation2);

        $this->assertEquals([$operation1, $operation2], $user->getOperations());
    }

    public function testCanGetUserOperations()
    {
        $operation = new OperationModel();
        $operation->setDate('2018-09-12');
        $operation->setType('cash_in');
        $operation->setAmount('1000.00');
        $operation->setCurrency('USD');

        $user = new UserModel();
        $user->addOperation($operation);

        $this->assertEquals($user->getOperations(), [$operation]);
    }

    public function testUserCanCalculateCommission()
    {
        $operation = new OperationModel();
        $operation->setDate('2018-09-11');
        $operation->setType('cash_out');
        $operation->setAmount('1000.00');
        $operation->setCurrency('EUR');

        $user = new UserModel();
        $user->setId(1);
        $user->setType('natural');

        $this->assertEquals(
            $user->calculateCommission($operation),
            '0.00'
        );
    }
}
