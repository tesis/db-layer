<?php //tests/TestCase.php

/*
vendor/bin/phpunit tests/TestCase

alias phpunit='vendor/bin/phpunit --config phpunit.xml'
phpunit tests/TestCase
*/

namespace PHPUnitTests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PHPUnitTests\Traits\ReflectionsTrait;
use PHPUnitTests\Traits\ManagementTrait;

class TestCase extends PHPUnitTestCase
{
    use ReflectionsTrait, ManagementTrait;

    // Create the object against which you will test
    public function setUp()
    {
        $this->setVerboseErrorHandler();
    }

    // Clean up the the objects against which you tested
    public function tearDown()
    {

    }

    /**
     *
     * testSimple to check if it is working
     *
     */
    public function test_simple()
    {
        $this->output(__METHOD__);
        $this->assertTrue(true, 'Expected Pass');
    }

/*---------------- End of class ----------------------*/
}
