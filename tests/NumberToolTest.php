<?php

namespace Test\Common\Tool;

use Common\Tool\NumberTool;
use PHPUnit\Framework\TestCase;

class NumberToolTest extends TestCase
{
    public function strictCorrectAddDataProvider()
    {
        return [
            [1, 1, '2.00'],
            ['1', '1', '2.00'],
            ['1', 1.00, '2.00'],
            [1.2, 5.39, '6.59'],
            [0.0, 0.1, '0.10'],
            [0.000001, 0.99, '0.9900010000', false],
            [0.000001, 0.99, '0.99'],
            [0.0000000001, 0.9999999999, '1.00'],
            [0.00000000001, 0.99999999999, '1.00'],
            [0.00000000001, 0.99999999999, '1.0000000000', false],
            [0.1, 0.99, '1.09'],
            [0.001, 0.99, '0.99'],
            [0.001, 0.99, '0.9910000000', false],
            [0.001, 0.99, '0.99'],
            [-0.001, -0.99, '-0.99'],
            ['-1.', 0.01, '-0.99'],
            ['-1.', 0.001, '-0.9990000000', false],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectAddDataProvider
     */
    public function strictCorrectAddition($a, $b, $expectedResult, $round = true)
    {
        $actualResult = NumberTool::add($a, $b, $round);
        self::assertSame($expectedResult, $actualResult);
    }


    public function correctAddDataProvider()
    {
        return [
            [1, 1, 2],
            [2, 5, 7.],
            [1, 1, 0b10],
            [-1.23, 1.25, 0.02000],
        ];
    }

    /**
     * @test
     * @dataProvider correctAddDataProvider
     */
    public function notStrictCorrectAddition($a, $b, $expectedResult, $round = true)
    {
        $actualResult = NumberTool::add($a, $b, $round);
        self::assertEquals($actualResult, $expectedResult);
    }


    public function incorrectAddDataProvider()
    {
        return [
            [1, 2, '4'],
            [0.000001, 0.99999999999999999999, '1.0000009999', false],
            [0.00000000001, 0.99999999999, '1.00', false],
        ];
    }

    /**
     * @test
     * @dataProvider incorrectAddDataProvider
     */
    public function incorrectAddition($a, $b, $expectedResult, $round = true)
    {
        $actualResult = NumberTool::add($a, $b, $round);
        self::assertNotEquals($actualResult, $expectedResult);
    }


    public function strictCorrectSubDataProvider()
    {
        return [
            [3, 1.5, '1.50'],
            [3, 1.75, '1.2500000000', false],
            ['3.456', .856, '2.60'],
            ['.456', .856, '-0.40'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectSubDataProvider
     */
    public function strictCorrectSubtraction($a, $b, $expectedResult, $round = true)
    {
        $actualResult = NumberTool::sub($a, $b, $round);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectMulDataProvider()
    {
        return [
            ['16.69', '12.47', '208.12'],
            [16.69, 12.47, '208.12'],
            [12.34, 0, '0.00'],
            [12.01, -3, '-36.03'],
            [-12.01, -3, '36.03'],
            [12.3, 0, '0.0000000000', false],
            [12.341, 0, '0.0000000000', false],
            [16.69, 12.47, '208.1243000000', false],
            ['10', 0.001, '0.01'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectMulDataProvider
     */
    public function strictCorrectMultiplication($a, $b, $expectedResult, $round = true)
    {
        $actualResult = NumberTool::mul($a, $b, $round);
        self::assertSame($expectedResult, $actualResult);
    }


    public function incorrectMulDataProvider()
    {
        return [
            ['123', 0, '0.01'],
            ['10', 0.001, '-0.01'],
        ];
    }

    /**
     * @test
     * @dataProvider incorrectMulDataProvider
     */
    public function incorrectMultiplication($a, $b, $expectedResult, $round = true)
    {
        $actualResult = NumberTool::mul($a, $b, $round);
        self::assertNotEquals($actualResult, $expectedResult);
    }


    public function strictCorrectDivDataProvider()
    {
        return [
            ['208.12', '16.69', '12.47'],
            ['208.12', '-16.69', '-12.47'],
            ['10', -0.1, '-100.00'],
            ['10', '0', '0.00'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectDivDataProvider
     */
    public function strictCorrectDivision($a, $b, $expectedResult, $round = true)
    {
        $actualResult = NumberTool::div($a, $b, $round);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectPowDataProvider()
    {
        return [
            ['3', '2', '9.00'],
            ['-3', '3', '-27.00'],
            ['0.3', '2', '0.09'],
            ['-0.3', '2', '0.09'],
            ['3', '-2', '0.11'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectPowDataProvider
     */
    public function strictCorrectPower($a, $b, $expectedResult, $round = true)
    {
        $actualResult = NumberTool::pow($a, $b, $round);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectTruncateDataProvider()
    {
        return [
            ['3', '0', '3'],
            ['-3', '3', '-3'],
            ['0.3099f45845698', '2', '0.30'],
            ['-0.3', '2', '-0.3'],
            ['-0.3', '-52', '-0.'],
            ['-0.3', '-52.5', '-0.'],
            ['1.234567', '.5', '1.'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectTruncateDataProvider
     */
    public function strictCorrectTruncate($a, $b, $expectedResult)
    {
        $actualResult = NumberTool::truncate($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectAbsDataProvider()
    {
        return [
            ['3', '3.0000000000'],
            ['', '0.0000000000'],
            ['-3', '3.0000000000'],
            ['-0', '0.0000000000'],
            ['0', '0.0000000000'],
            ['-0.3', '0.3000000000'],
            ['-1.234567', '1.2345670000'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectAbsDataProvider
     */
    public function strictCorrectAbs($a, $expectedResult)
    {
        $actualResult = NumberTool::abs($a);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectRoundDataProvider()
    {
        return [
            ['3', '3', '3.000'],
            ['-3', '3', '-3.000'],
            ['0', '0', '0'],
            ['-0.3', '0', '0'],
            ['-0.3', '1', '-0.3'],
            ['1.234567', '0.3', '1'],
            ['1.234567', '0.9', '1'],
            ['1.234567', '1.234567', '1.2'],
            ['1.234567', null, '1'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectRoundDataProvider
     */
    public function strictCorrectRound($a, $b, $expectedResult)
    {
        $actualResult = NumberTool::round($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectFormatDataProvider()
    {
        return [
            ['3', '3', '3.000'],
            ['-3', '3', '-3.000'],
            ['0', '0', '0'],
            ['-0.3', '0', '0'],
            ['-0.3', '1', '-0.3'],
            ['1.234567', '0.3', '1'],
            ['1.234567', '4', '1.2346'],
            ['1.234567', '-4.234567', '1'],
            ['1.234567', '4.234567', '1.2346'],
            ['1.234567', null, '1'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectFormatDataProvider
     */
    public function strictCorrectFormat($a, $b, $expectedResult)
    {
        $actualResult = NumberTool::format($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectRoundDownDataProvider()
    {
        return [
            ['3', '3', '3.000'],
            ['-3', '3', '-3.001'],
            ['0', '0', '0'],
            ['-0.3', '0', '-1'],
            ['-0.3', '1', '-0.4'],
            ['1.234567', '0.3', '1'],
            ['1.234567', '4', '1.2345'],
            ['1.234567', '-4.234567', '-10000'],//TODO wtf
            ['1.234567', '4.234567', '1.2345'],
            ['1.234567', null, '1'],
            ['1.987654', '0.999999', '2'],
            ['1.987654', '3', '1.987'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectRoundDownDataProvider
     */
    public function strictCorrectRoundDown($a, $b, $expectedResult)
    {
        $actualResult = NumberTool::roundDown($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectFloorDataProvider()
    {
        return [
            ['3', '3'],
            ['-3', '-3'],
            ['0', '0'],
            ['0.999', '0'],
            ['-0.999', '-0'], //TODO should be -1
            ['1.234567', '1'],
            [null, ''],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectFloorDataProvider
     */
    public function strictCorrectFloor($a, $expectedResult)
    {
        $actualResult = NumberTool::floor($a);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectRoundCustomDataProvider()
    {
        return [
            ['3', '3', '3.000'],
            ['-3', '3', '-3.000'],
            ['0', '0', '0'],
            ['-0.3', '0', '0'],
            ['-0.3', '1', '-0.3'],
            ['1.234567', '0.3', '1'],
            ['1.234567', '0.9', '1'],
            ['1.234567', '1.234567', '1.2'],
            ['1.234567', null, '1'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectRoundCustomDataProvider
     *  Practically the same as regular round
     */
    public function strictCorrectRoundCustom($a, $b, $expectedResult)
    {
        $actualResult = NumberTool::roundCustom($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectPercentDataProvider()
    {
        return [
            ['19.99', '21', true, '4.20'],
            ['19.99', '21', false, '4.1979000000'],
            ['20', '25', true, '5.00'],
            ['20', '25', false, '5.0000000000'],
            ['20', '0', false, '0.0000000000'],
            ['20', '10', true, '2.00'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectPercentDataProvider
     */
    public function strictCorrectPercent($a, $b, $round, $expectedResult)
    {
        $actualResult = NumberTool::percent($a, $b, $round);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectAddPercentDataProvider()
    {
        return [
            ['19.99', '21', true, '24.19'],
            ['19.99', '21', false, '24.1900000000'],
            ['20', '25', true, '25.00'],
            ['20', '25', false, '25.0000000000'],
            ['-20', '25', false, '-25.0000000000'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectAddPercentDataProvider
     */
    public function strictCorrectAddPercent($a, $b, $round, $expectedResult)
    {
        $actualResult = NumberTool::addPercent($a, $b, $round);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectBeforePercentAdditionDataProvider()
    {
        return [
            ['19.99', '21', true, '16.52'],
            ['19.99', '21', false, '16.5206611500'],
            ['20', '25', true, '16.00'],
            ['20', '25', false, '16.0000000000'],
            ['-20', '25', false, '-16.0000000000'],
            ['1', '-100', false, '0.0000000000'],
            ['1', '-100', true, '0.00'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectBeforePercentAdditionDataProvider
     */
    public function strictCorrectBeforePercentAddition($a, $b, $round, $expectedResult)
    {
        $actualResult = NumberTool::beforePercentAddition($a, $b, $round);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectAddVatDataProvider()
    {
        return [
            ['20', '100', '40.00'],
            ['20', '20', '24.00'],
            ['-20', '20', '-24.00'],
            ['20', '-20', '16.00'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectAddVatDataProvider
     */
    public function strictCorrectAddVat($a, $b, $expectedResult)
    {
        $actualResult = NumberTool::addVat($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectRemoveVatDataProvider()
    {
        return [
            ['20', '100', '10.00'],
            ['20', '20', '16.67'],
            ['-20', '20', '-16.67'],
            ['20', '-20', '25.00'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectRemoveVatDataProvider
     */
    public function strictCorrectRemoveVat($a, $b, $expectedResult)
    {
        $actualResult = NumberTool::removeVat($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectVatAmountProvider()
    {
        return [
            ['20', '100', '10.00'],
            ['100', '20', '16.67'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectVatAmountProvider
     */
    public function strictCorrectVatAmount($a, $b, $expectedResult)
    {
        $actualResult = NumberTool::vatAmount($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectIsNullOrZeroProvider()
    {
        return [
            ['null', true],
            [null, true],
            [0, true],
            ['', true],
            [7, false],
            ['qwertyui', true],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectIsNullOrZeroProvider
     */
    public function strictCorrectIsNullOrZero($a, $expectedResult)
    {
        $actualResult = NumberTool::isNullOrZero($a);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectIsZeroProvider()
    {
        return [
            ['null', true],
            [null, true],
            [0, true],
            ['', true],
            [7, false],
            ['qwertyui', true],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectIsZeroProvider
     */
    public function strictCorrectIsZero($a, $expectedResult)
    {
        $actualResult = NumberTool::isZero($a);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectAddAllProvider()
    {
        return [
            [[12, 6, 6], '24.00'],
            [[-12, 6, 6], '0.00'],
            [[-12.56, 6.6777, 6.123], '0.24'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectAddAllProvider
     */
    public function strictCorrectAddAll($a, $expectedResult)
    {
        if (is_array($a)) {
            $nt = new NumberTool();
            $actualResult = call_user_func_array([$nt, 'addAll'], $a);
        } else {
            $actualResult = NumberTool::addAll($a);
        }
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectGtProvider()
    {
        return [
            ['3', '2', true],
            ['3', '2.99', true],
            ['3', '-2.99', true],
            ['3', null, true],
            ['-3', null, false],
            ['3', '', true],
            ['-3', -1, false],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectGtProvider
     */
    public function strictCorrectGt($a, $b, $expectedResult)
    {
        $actualResult = NumberTool::gt($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectGteProvider()
    {
        return [
            ['3', '2', true],
            ['3', '2.99', true],
            ['3', '-2.99', true],
            ['3', 3, true],
            ['3', null, true],
            ['-3', null, false],
            ['3', '', true],
            ['-3', -1, false],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectGteProvider
     */
    public function strictCorrectGte($a, $b, $expectedResult)
    {
        $actualResult = NumberTool::gte($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectLtProvider()
    {
        return [
            ['2', '3', true],
            ['2', '2.99', true],
            ['-3', '2.99', true],
            [null, 5, true],
            ['3', null, false],
            ['', '3', true],
            [-1, '', true],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectLtProvider
     */
    public function strictCorrectLt($a, $b, $expectedResult)
    {
        $actualResult = NumberTool::lt($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectLteProvider()
    {
        return [
            ['2', '3', true],
            ['2', '2.99', true],
            ['-3', '2.99', true],
            [null, '', true],
            ['3', null, false],
            ['', '3', true],
            [-1, '', true],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectLteProvider
     */
    public function strictCorrectLte($a, $b, $expectedResult)
    {
        $actualResult = NumberTool::lte($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectEqProvider()
    {
        return [
            [3, '3', true],
            ['2', '2.99', false],
            [null, '', true],
            [-1, '-1', true],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectEqProvider
     */
    public function strictCorrectEq($a, $b, $expectedResult)
    {
        $actualResult = NumberTool::eq($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectPmtProvider()
    {
        return [
            ['30', '36', '10000', '424.52'],
            ['15', '46', '10000', '287.17'],
            // TODO if $apr is greater than $term it dies
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectPmtProvider
     */
    public function strictCorrectPmt($apr, $term, $loan, $expectedResult)
    {
        $actualResult = NumberTool::pmt($apr, $term, $loan);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectCalculateMedianProvider()
    {
        return [
            [['30'], true, '30.00'],
            [['30', 31], false, '30.5'],
            [['30', 32], false, '31'],
            [['30', 32, 1000, 3400000], false, '516'],
            [['30', '36', '10000'], true, '36.00'],
            [['15', '46', '10000'], true, '46.00'],
            [['15', '46', '10000'], false, '46'],
            [['15', '46', '10000', 10, 15, 23, 56, 67, 68, 79, 14, 56, 89], true, '56.00'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectCalculateMedianProvider
     */
    public function strictCorrectCalculateMedian($values, $round, $expectedResult)
    {
        $actualResult = NumberTool::calculateMedian($values, $round);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectCalculateAverageProvider()
    {
        return [
            [['30'], true, '30.00'],
            [['30', 31], false, '30.5'],
            [['30', 32], false, '31'],
            [['30', '36'], true, '33.00'],
            [['15', '46'], true, '30.50'],
            [['14', '46', '30'], false, '30'],
            [['15', '46', '10000', 10, 15, 23, 56, 67, 68, 79, 14, 56, 89], true, '810.62'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectCalculateAverageProvider
     */
    public function strictCorrectCalculateAverage($values, $round, $expectedResult)
    {
        $actualResult = NumberTool::calculateAverage($values, $round);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectNumberToTextProvider()
    {
        return [
            ['30', 'LV', 'trīsdesmit'],
            ['-80', 'LV', 'mīnus astoņdesmit'],
            ['0', 'LV', 'nulle'],
            ['0', 'EN', 'zero'],
            ['0', 'EN', 'zero'],
            ['0', null, 'zero'],
            ['0', 'LV', 'nulle'],
            ['0', 'EN', 'zero'],
            ['1337', 'RU', 'одна тысяча триста тридцать семь'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectNumberToTextProvider
     */
    public function strictCorrectNumberToText($a, $b,  $expectedResult)
    {
        $actualResult = NumberTool::numberToText($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectGetRoundedValueAndDifferenceProvider()
    {
        return [
            ['30', ['roundedValue' => '30', 'diff' => '0.0000000000']],
            ['30.123456789', ['roundedValue' => '30.12', 'diff' => '0.0034567890']],
            ['0.123456789', ['roundedValue' => '0.12', 'diff' => '0.0034567890']],
            ['-0.123456789', ['roundedValue' => '-0.12', 'diff' => '-0.0034567890']],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectGetRoundedValueAndDifferenceProvider
     */
    public function strictCorrectGetRoundedValueAndDifference($a, $expectedResult)
    {
        $actualResult = NumberTool::getRoundedValueAndDifference($a);
        self::assertSame($expectedResult, $actualResult);
    }


    public function strictCorrectGetPercentageBetweenTwoProvider()
    {
        return [
            ['30', '30', '100.00'],
            ['100', '35.456', '35.46'],
            ['100', '125.456', '125.46'],
            ['25', '-25', '-100.00'],
            ['25', '0.25', '1.00'],
            ['-45', '0.25', '-0.56'],
            ['-50', '-0.1', '0.20'],
            ['-50', '0', '0'],
            ['-51', '0.0', '0'],
            ['-52', '0.00', '0'],
            ['-53', '', '0'],
            ['-54', null, '0'],
        ];
    }

    /**
     * @test
     * @dataProvider strictCorrectGetPercentageBetweenTwoProvider
     */
    public function strictCorrectGetPercentageBetweenTwo($a, $b, $expectedResult)
    {
        $actualResult = NumberTool::getPercentageBetweenTwo($a, $b);
        self::assertSame($expectedResult, $actualResult);
    }
}
