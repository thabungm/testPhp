<?php

namespace Calculator\Tests;

use Calculator\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    public function testAdd(): void
    {
        $this->assertSame(5.0, $this->calculator->add(2, 3));
    }

    public function testSubtract(): void
    {
        $this->assertSame(2.0, $this->calculator->subtract(5, 3));
    }

    public function testMultiply(): void
    {
        $this->assertSame(12.0, $this->calculator->multiply(4, 3));
    }

    public function testDivide(): void
    {
        $this->assertSame(5.0, $this->calculator->divide(10, 2));
    }

    public function testDivideByZeroThrows(): void
    {
        $this->expectException(\DivisionByZeroError::class);
        $this->calculator->divide(1, 0);
    }
}
