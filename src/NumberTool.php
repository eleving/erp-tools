<?php

namespace Common\Tool;

use DivisionByZeroError;
use PHPUnit\Framework\Error\Warning;
use RuntimeException;

/**
 * Utility for financial calculations
 */
class NumberTool
{
    protected const SCALE = 10;

    /**
     * Convert numeric variable to string with right formatting
     * @param mixed $s
     * @return string
     */
    private static function f($s): string
    {
        return sprintf('%.' . self::SCALE . 'f', $s);
    }
    /**
     * Performs addition
     * NumberTool::add('2.71', '3.18') //5.89
     * @param string $op1
     * @param string $op2
     * @param boolean $round
     * @return string
     */
    public static function add($op1, $op2, $round = true): string
    {
        $res = bcadd(self::f($op1), self::f($op2), self::SCALE);
        return $round ? self::round($res) : $res;
    }

    /**
     * Performs substraction
     * NumberTool::sub('5.89', '3.18') //2.71
     * @param string $op1
     * @param string $op2
     * @param boolean $round
     * @return string
     */
    public static function sub($op1, $op2, $round = true): string
    {
        $res = bcsub(self::f($op1), self::f($op2), self::SCALE);
        return $round ? self::round($res) : $res;
    }

    /**
     * Performs multiplication
     * NumberTool::mul('16.69', '12.47') //208.12
     * @param string $op1
     * @param string $op2
     * @param boolean $round
     * @return string
     */
    public static function mul($op1, $op2, $round = true): string
    {
        $res = bcmul(self::f($op1), self::f($op2), self::SCALE);
        return $round ? self::round($res) : $res;
    }

    /**
     * Performs division
     * NumberTool::div('208.12', '16.69') //12.47
     * @param string $op1
     * @param string $op2
     * @param boolean $round
     * @return string
     */
    public static function div($op1, $op2, $round = true): ?string
    {
        try {
            $res = bcdiv(self::f($op1), self::f($op2), self::SCALE);
        } catch (DivisionByZeroError|RuntimeException $_) {
            $res = null;
        }
        return $round ? self::round($res) : $res;
    }

    /**
     * Rise $left to $right
     * @param string $left left operand
     * @param string $right right operand
     * @param boolean $round
     * @return string
     */
    public static function pow($left, $right, $round = true): string
    {
        //bcpow does not support decimal numbers
        $res = $left ** $right;
        return $round ? self::round($res) : (string)$res;
    }

    /**
     * Truncates decimal number to given precision
     * NumberTool::truncate('1.9999', 2) //1.99
     * TODO: rework for using sprintf
     * @param string $number
     * @param integer $precision
     * @return string
     */
    public static function truncate(?string $number, $precision): string
    {
        $x = explode('.', $number);
        if (count($x) === 1) {
            return $x[0];
        }
        if ($precision === 0) {
            return $x[0];
        }
        return $x[0] . '.' . substr($x[1], 0, $precision);
    }

    /**
     * Absolute number value
     * NumberTool::abs('-10.99') //10.99
     * @param string $number
     * @return string
     */
    public static function abs($number): string
    {
        $number = self::f($number);
        if ($number === '') {
            return $number;
        }

        if ($number[0] !== '-') {
            return $number;
        }

        return substr($number, 1);
    }

    /**
     * Rounds number with precision of $precision decimal places
     * NumberTool::round('208.1243') //208.12
     * @param string|int|float $val
     * @param integer $precision
     * @return string
     */
    public static function round($val, $precision = 2): string
    {
        return number_format(round($val, $precision), $precision, '.', '');
    }

    /**
     * Formats number to decimal
     *
     * @param string $val
     * @param integer $precision
     * @return string
     */
    public static function format($val, $precision = 2): string
    {
        return number_format($val, $precision, '.', '');
    }

    /**
     * Rounds down number with precision of $precision decimal places
     * NumberTool::roundDown('2.03717') //2.03
     * @param string $val
     * @param integer $precision
     * @return string
     */
    public static function roundDown(string $val, $precision = 2): string
    {
        if (self::isZero($val)) {
            return self::round($val, $precision);
        }

        $half = 0.5 / (10 ** $precision);
        return number_format(round($val - $half, $precision), $precision, '.', '');
    }

    /**
     * Floor
     * @param string $val
     * @return string
     */
    public static function floor(?string $val): string
    {
        return self::truncate($val, 0);
    }

    /**
     * Rounds number with custom precision and custom format
     * Example: NumberTool::round('208.1243', 2) // 208.12
     *
     * @access public
     * @param string $val
     * @param int $precision
     * @return string
     */
    public static function roundCustom($val, $precision = 1): string
    {
        return self::round($val, $precision);
    }

    /**
     * Calculates percentage
     * NumberTool::percent('19.99', '21.00') //4.20
     * @param string $amount
     * @param string $percentage
     * @param boolean $round
     * @return string
     */
    public static function percent($amount, $percentage, $round = true): string
    {
        $res = bcmul(self::f($amount), bcdiv(self::f($percentage), '100', self::SCALE), self::SCALE);
        return $round ? self::round($res) : $res;
    }

    /**
     * NumberTool::addPercent('19.99', '21.00') //24.19
     * @param string $amount
     * @param string $percentage
     * @return string
     */
    public static function addPercent($amount, $percentage, $round = true): string
    {
        $res = bcadd(self::f($amount), self::percent(self::f($amount), self::f($percentage)), self::SCALE);
        return $round ? self::round($res) : $res;
    }

    /**
     * NumberTool::beforePercentAddition('24.19', '21.00') //19.99
     * @param string $result
     * @param string $percentage
     * @return string
     */
    public static function beforePercentAddition($result, $percentage, $round = true): string
    {
        // ($result / ($percentage + 100)) * 100;
        try {
            $div = bcdiv($result, bcadd($percentage, '100', self::SCALE), self::SCALE);
        } catch (DivisionByZeroError|RuntimeException $_) {
            $div = null;
        }
        $res = bcmul($div, '100', self::SCALE);
        return $round ? self::round($res) : $res;
    }

    public static function addVat($value, $percentage): string
    {
        return self::addPercent($value, $percentage);
    }

    public static function removeVat($total, $percentage): string
    {
        return self::beforePercentAddition($total, $percentage);
    }

    public static function vatAmount($total, $percentage): string
    {
        $withoutVat = self::beforePercentAddition($total, $percentage, false);
        return self::percent($withoutVat, $percentage);
    }

    public static function isNullOrZero($number): bool
    {
        return $number === null || self::isZero($number);
    }

    public static function isZero($number): bool
    {
        return (float)$number === .0;
    }

    /**
     * Performs addition to all passed arguments
     * NumberTool::add('1.00', '2.00', '3.00') //6.00
     * @param string $op1
     * @param string $op2
     * @param boolean $round
     * @return string
     */
    public static function addAll(): string
    {
        $res = '0.00';
        foreach (func_get_args() as $arg) {
            $res = self::add($res, $arg);
        }
        return $res;
    }

    /**
     * Returns true if $left is greater than $right
     * @param string $left left operand
     * @param string $right right operand
     * @return bool
     */
    public static function gt($left, $right): bool
    {
        return bccomp(self::f($left), self::f($right), self::SCALE) === 1;
    }

    /**
     * Returns true if $left is greater than or equal to $right
     * @param string $left left operand
     * @param string $right right operand
     * @return bool
     */
    public static function gte($left, $right): bool
    {
        $comp = bccomp(self::f($left), self::f($right), self::SCALE);
        return $comp === 0 || $comp === 1;
    }

    /**
     * Returns true if $left is smaller than $right
     * @param string $left left operand
     * @param string $right right operand
     * @return bool
     */
    public static function lt($left, $right): bool
    {
        return bccomp(self::f($left), self::f($right), self::SCALE) === -1;
    }

    /**
     * Returns true if $left is smaller than or equal to $right
     * @param mixed $left left operand
     * @param mixed $right right operand
     * @return bool
     */
    public static function lte($left, $right): bool
    {
        $comp = bccomp(self::f($left), self::f($right), self::SCALE);
        return $comp === 0 || $comp === -1;
    }

    /**
     * Returns true if $left is equal to $right
     * @param string $left left operand
     * @param string $right right operand
     * @return bool
     */
    public static function eq($left, $right): bool
    {
        return bccomp(self::f($left), self::f($right), self::SCALE) === 0;
    }

    /**
     * PHP Version of PMT in Excel.
     *
     * @param float $apr
     *   Interest rate.
     * @param integer $term
     *   Loan length in months.
     * @param float $loan
     *   The loan amount.
     *
     * @return string
     *   The monthly mortgage amount.
     */
    public static function pmt($apr, $term, $loan): string
    {
        $apr = $apr / 1200;
        $amount = $apr * -$loan * ((1 + $apr) ** $term) / (1 - ((1 + $apr) ** $term));
        return number_format($amount, 2);
    }

    /**
     * Calculate median value by array values.
     *
     * @access public
     * @param array $values
     * @param boolean $round
     * @return string
     */
    public static function calculateMedian($values, $round = true): string
    {
        $count = count($values); // total numbers in array
        $middleval = floor(($count - 1) / 2); // find the middle value, or the lowest middle value

        if ($count % 2) { // odd number, middle is the median
            $median = $values[$middleval];
        } else { // even number, calculate avg of 2 medians
            $low = $values[$middleval];
            $high = $values[$middleval + 1];
            $median = (($low + $high) / 2);
        }

        return $round ? self::round($median) : (string)$median;
    }

    /**
     * Calculate average value by array values.
     *
     * @access public
     * @param array $values
     * @param boolean $round
     * @return string
     */
    public static function calculateAverage($values, $round = true): string
    {
        $count = count($values); // total numbers in array

        $total = 0;
        foreach ($values as $value) {
            $total += $value; // total value of array numbers
        }
        $average = ($total / $count); // get average value

        return $round ? self::round($average) : (string)$average;
    }

    public static function numberToText($number, $locale = null): string
    {
        if (!$locale) {
            $locale = 'en';
        }

        $style = \NumberFormatter::SPELLOUT;
        $formatter = new \NumberFormatter($locale, $style);

        // Format
        $formatted = $formatter->format($number);

        // Remove soft hyphens
        $formatted = preg_replace('~\x{00AD}~u', '', $formatted);

        return $formatted;
    }

    /**
     * Returns rounded value and difference after rounding
     * @param mixed $value
     * @return array
     */
    public static function getRoundedValueAndDifference($value): array
    {
        $rounded = self::truncate($value, 2);
        $diff = self::sub($value, $rounded, false);

        return [
            'roundedValue' => $rounded,
            'diff' => $diff,
        ];
    }

    /**
     * @param float|int $total
     * @param float|int $partial
     * @return string
     */
    public static function getPercentageBetweenTwo($total, $partial): string
    {
        if (!(float)$partial) {
            return '0';
        }

        return number_format(((float)$partial / (float)$total) * 100, 2);
    }
}
