<?php
/*
 * Ensures compatibility with PHPUnit < 6.x
 */

if(!class_exists('PHPUnit_Framework_Constraint') && class_exists('PHPUnit\Framework\Constraint\Constraint'))
{
    abstract class PHPUnit_Framework_Constraint extends \PHPUnit\Framework\Constraint\Constraint {}
}

if(!class_exists('PHPUnit_Framework_TestCase') && class_exists('PHPUnit\Framework\TestCase'))
{
    abstract class PHPUnit_Framework_TestCase extends \PHPUnit\Framework\TestCase {}
}

if(!class_exists('PHPUnit_Runner_Version') && class_exists('PHPUnit\Runner\Version'))
{
    class_alias('\PHPUnit\Runner\Version', 'PHPUnit_Runner_Version', true);
}

if(!class_exists('PHPUnit_Framework_Assert') && class_exists('PHPUnit\Framework\Assert'))
{
    abstract class PHPUnit_Framework_Assert extends \PHPUnit\Framework\Assert {}
}

if (!interface_exists('PHPUnit_Framework_MockObject_MockObject') && interface_exists('PHPUnit\Framework\MockObject\MockObject')) {
    interface PHPUnit_Framework_MockObject_MockObject extends \PHPUnit\Framework\MockObject\MockObject {}
}