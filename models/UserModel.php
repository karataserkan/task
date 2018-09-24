<?php

namespace app\models;

use app\helpers\CommissionHelper;

/**
 * User Model.
 */
class UserModel extends BaseModel
{
    const TYPE_NATURAL = 'natural';
    const TYPE_LEGAL = 'legal';

    private $id;
    private $type;
    public $operations = [];

    /**
     * Sets user id.
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * Sets user type.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns user type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns user id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns user operations.
     *
     * @return array
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * Calculate commission of operation .
     *
     * @param object $operation OperationModel object
     *
     * @return string
     */
    public function calculateCommission($operation)
    {
        return CommissionHelper::calculate(
        	$operation, 
        	$this->getOperations(), 
        	$this->getType()
    	);
    }

    /**
     * Adds opearation to user.
     *
     * @param object $operation OperationModel object
     */
    public function addOperation($operation)
    {
        $this->operations[] = $operation;
    }
}
