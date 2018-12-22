<?php
/**
 * Created by PhpStorm.
 * User: a.falahati
 * Date: 12/17/2018
 * Time: 10:06 AM
 */

namespace App\Tests;


use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testAssertEqual()
    {
        $this->assertEquals(5, 3 + 2, 'Hi ali');
    }

    public function testAssertTrue()
    {
        $this->assertTrue(true);
    }
}