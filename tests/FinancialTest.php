<?php


namespace Test\Common\Tool;


use Common\Tool\Financial;
use PHPUnit\Framework\TestCase;

class FinancialTest extends TestCase
{

    /**
     * @test
     */
    public function PMT()
    {
        $test = Financial::PMT(0.1, 1,1, 0);
        self::assertEqualsWithDelta('-1.1', $test, 0.00000000000001);

        $test = Financial::PMT(0, 100,1, 0);

        self::assertSame(-0.01, $test);

        $test = Financial::PMT(-10, 100,1, 0);
        self::assertSame(10., $test);


        $test = Financial::PMT(-1, 100,1, 0);
        self::assertSame(0., $test);

        $test = Financial::PMT(-0.99, 100,1, 0);
        self::assertEqualsWithDelta(0., $test, 0.00000000000001);

        $test = Financial::PMT(-0.55, 100,10, 10);
        self::assertSame(-5.5, $test);
    }

    /**
     * @test
     */
    public function IPMT()
    {
        $test = Financial::IPMT(5.0, 1, 12, 12);
        self::assertSame(-60.0, $test);

        $test = Financial::IPMT(5.0, 0, 12, 12);
        self::assertSame(null, $test);
    }
    /**
     * @test
     */
    public function PPMT()
    {
        $test = Financial::PPMT(5.0, 1, 12, 12);
        self::assertSame(-2.7563622495563322E-8, $test);

        $test = Financial::PPMT(5.0, -10, 12, 12);
        self::assertSame(null, $test);
    }
}