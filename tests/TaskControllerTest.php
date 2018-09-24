<?php

declare (strict_types = 1);

namespace app\tests;

use PHPUnit\Framework\TestCase;
use app\controllers\TaskController;

final class TaskControllerTest extends TestCase
{
    public function testCanGetFile()
    {
        $controller = new TaskController();
        $file = 'input.csv';
        $params = ['index.php', 'task/operate', $file];
        $this->assertEquals($file, $controller->getFile($params));
    }

    public function testFileNotExistsForGetFile()
    {
        $this->expectException(\InvalidArgumentException::class);

        $controller = new TaskController();
        $file = 'nofile.csv';
        $params = ['index.php', 'task/operate', $file];
        $controller->getFile($params);
    }

    public function testFileNotProvided()
    {
        $this->expectException(\OutOfRangeException::class);

        $controller = new TaskController();
        $params = ['index.php', 'task/operate'];
        $controller->getFile($params);
    }
}
