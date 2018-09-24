<?php

namespace app\controllers;

use app\models\UserModel;
use app\models\OperationModel;

/**
 * Task Controller.
 */
class TaskController extends BaseController
{
    /**
     * Calculates commission fees for provided csv file.
     *
     * @param array $params Input parameters
     */
    public function operate($params)
    {
        $file = $this->getFile($params);
        $users = [];
        $out = fopen('php://stdout', 'w');

        foreach ($this->getLines($file) as $n => $line) {
            if (count($line) < 6) {
                throw new \LengthException('Please provide correct data on line '.($n + 1), 1);
            }

            $userId = (int) $line[1];
            $userType = $line[2];

            //if user not created, create a user
            if (isset($users[$userId])) {
                $user = $users[$userId];
            } else {
                $user = new UserModel();
                $user->setId($userId);
            }

            //we always set user type, because user type may change for each operation
            $user->setType($userType);

            //create new operation with line data
            $operation = new OperationModel();
            $operation->setDate($line[0]);
            $operation->setType($line[3]);
            $operation->setAmount($line[4]);
            $operation->setCurrency($line[5]);

            //calculate commission for each line of provided csv file
            $commission = $user->calculateCommission($operation);
            //echo $commission.PHP_EOL;
            fputs($out, $commission.PHP_EOL);

            //add operation to user to calculate commissions and discounts correctly 
            $user->addOperation($operation);
            $users[$userId] = $user;
        }
        fclose($out);
    }

    /**
     * Returns file path from provided $params array.
     *
     * @param array $params provided params
     *
     * @return string
     */
    public function getFile($params)
    {
        if (!isset($params[2])) {
            throw new \OutOfRangeException('Please provide a file', 1);
        }

        if (!file_exists($params[2])) {
            throw new \InvalidArgumentException('File not exists', 1);
        }

        return $params[2];
    }

    /**
     * Returns lines of provided csv file.
     *
     * @param string $file file path
     *
     * @return array
     */
    public function getLines($file)
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException('File not exists', 1);
        }

        $f = fopen($file, 'r');
        try {
            while ($line = fgetcsv($f)) {
                yield $line;
            }
        } finally {
            fclose($f);
        }
    }
}
