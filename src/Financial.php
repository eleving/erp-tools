<?php

namespace Common\Tool;

class Financial
{
    const FINANCIAL_ACCURACY       = 1.0e-6;
    const FINANCIAL_MAX_ITERATIONS = 100;

    private static $isInitialized = false;

    private static function init()
    {
        if (!self::$isInitialized) {
            // forces the precision for calculations
            ini_set('precision', '14');

            self::$isInitialized = true;
        }
    }


    /**
     * Present value interest factor
     *
     *                 nper
     * PVIF = (1 + rate)
     *
     * @param float   $rate is the interest rate per period.
     * @param integer $nper is the total number of periods.
     *
     * @return float  the present value interest factor
     */
    private static function PVIF($rate, $nper)
    {
        self::init();

        return ((1 + $rate) ** $nper);
    }

    /**
     * Future value interest factor of annuities
     *
     *                   nper
     *          (1 + rate)    - 1
     * FVIFA = -------------------
     *               rate
     *
     * @param float   $rate is the interest rate per period.
     * @param integer $nper is the total number of periods.
     *
     * @return float  the present value interest factor of annuities
     */
    private static function FVIFA($rate, $nper)
    {
        self::init();

        // Removable singularity at rate == 0
        if ($rate == 0) {
            return $nper;
        }

        return (((1 + $rate) ** $nper) - 1) / $rate;
    }

    private static function interestPart($pv, $pmt, $rate, $period)
    {
        self::init();

        return -($pv * ((1 + $rate) ** $period) * $rate + $pmt * (((1 + $rate) ** $period) - 1));
    }

    public static function PMT($rate, $nper, $pv, $fv, $type = 0)
    {
        self::init();

        // Calculate the PVIF and FVIFA
        $pvif = self::PVIF($rate, $nper);
        $fvifa = self::FVIFA($rate, $nper);

        return ((-$pv * $pvif - $fv) / ((1.0 + $rate * $type) * $fvifa));
    }

    /**
     * IPMT
     * Returns the interest payment for a given period for an investment based
     * on periodic, constant payments and a constant interest rate.
     *
     * For a more complete description of the arguments in IPMT, see the PV function.
     *
     * @param       $rate
     * @param       $per
     * @param       $nper
     * @param       $pv
     * @param float $fv
     * @param int   $type
     *
     * @return mixed|null
     */
    public static function IPMT($rate, $per, $nper, $pv, $fv = 0.0, $type = 0)
    {
        self::init();

        if (($per < 1) || ($per >= ($nper + 1))) {
            return null;
        }

        $pmt = self::PMT($rate, $nper, $pv, $fv, $type);

        $ipmt = self::interestPart($pv, $pmt, $rate, $per - 1);

        if (!is_finite($ipmt)) {
            return null;
        }

        return $ipmt;
    }

    /**
     * PPMT
     * Returns the payment on the principal for a given period for an
     * investment based on periodic, constant payments and a constant
     * interest rate.
     *
     * @param       $rate
     * @param       $per
     * @param       $nper
     * @param       $pv
     * @param float $fv
     * @param int   $type
     *
     * @return float|null
     */
    public static function PPMT($rate, $per, $nper, $pv, $fv = 0.0, $type = 0)
    {
        self::init();

        if (($per < 1) || ($per >= ($nper + 1))) {
            return null;
        }

        $pmt = self::PMT($rate, $nper, $pv, $fv, $type);
        $ipmt = self::interestPart($pv, $pmt, $rate, $per - 1);

        return ((is_finite($pmt) && is_finite($ipmt)) ? $pmt - $ipmt : null);
    }


    /**
     * NPV
     * Calculates the net present value of an investment by using a
     * discount rate and a series of future payments (negative values)
     * and income (positive values).
     *
     *        n   /   values(i)  \
     * NPV = SUM | -------------- |
     *       i=1 |            i   |
     *            \  (1 + rate)  /
     *
     * @param float   $rate
     * @param float[] $values
     *
     * @return float|int|null
     */
    private static function NPV($rate, $values)
    {
        self::init();

        if (!is_array($values)) {
            return null;
        }

        $npv = 0.0;
        foreach ($values as $i => $iValue) {
            $npv += $iValue / ((1 + $rate) ** ($i + 1));
        }

        return (is_finite($npv) ? $npv : null);
    }

    /**
     * IRR
     * Returns the internal rate of return for a series of cash flows
     * represented by the numbers in values. These cash flows do not
     * have to be even, as they would be for an annuity. However, the
     * cash flows must occur at regular intervals, such as monthly or
     * annually. The internal rate of return is the interest rate
     * received for an investment consisting of payments (negative
     * values) and income (positive values) that occur at regular periods.
     *
     * @param float[] $values
     * @param float   $guess
     *
     * @return float|null
     */
    public static function IRR($values, $guess = 0.1)
    {
        self::init();

        if (!is_array($values)) {
            return null;
        }

        // create an initial bracket, with a root somewhere between bot and top
        $x1 = 0.0;
        $x2 = $guess;
        $f1 = self::NPV($x1, $values);
        $f2 = self::NPV($x2, $values);
        for ($i = 0; $i < self::FINANCIAL_MAX_ITERATIONS; $i++) {
            if (($f1 * $f2) < 0.0) {
                break;
            }
            if (abs($f1) < abs($f2)) {
                $f1 = self::NPV($x1 += 1.6 * ($x1 - $x2), $values);
            } else {
                $f2 = self::NPV($x2 += 1.6 * ($x2 - $x1), $values);
            }
        }
        if (($f1 * $f2) > 0.0) {
            return null;
        }

        $f = self::NPV($x1, $values);
        if ($f < 0.0) {
            $rtb = $x1;
            $dx = $x2 - $x1;
        } else {
            $rtb = $x2;
            $dx = $x1 - $x2;
        }

        for ($i = 0; $i < self::FINANCIAL_MAX_ITERATIONS; $i++) {
            $dx *= 0.5;
            $x_mid = $rtb + $dx;
            $f_mid = self::NPV($x_mid, $values);
            if ($f_mid <= 0.0) {
                $rtb = $x_mid;
            }
            if ((abs($f_mid) < self::FINANCIAL_ACCURACY) || (abs($dx) < self::FINANCIAL_ACCURACY)) {
                return $x_mid;
            }
        }

        return null;
    }

    /**
     * @param float[] $values
     * @param int[]   $timestamps
     * @param float   $guess
     *
     * @return float|null
     */
    public static function XIRR($values, $timestamps, $guess = 0.1)
    {
        self::init();

        // Initialize dates and check that values contains at least one positive value and one negative value
        $positive = false;
        $negative = false;

        //XIRR Return error if number of values does not equal to number of timestamps
        if (count($values) != count($timestamps)) {
            return null;
        }

        //XIRR sort array on key (not sure whether it is needed, but just to be sure :-))
        array_multisort($timestamps, $values);

        //XIRR determine first timestamp
        $lowestTimestamp = new \DateTime('@' . min($timestamps));

        $dates = [];
        foreach ($values as $key => $value) {
            //XIRR Calculate the number of days between the given timestamp and the lowest timestamp
            $dates[] = date_diff($lowestTimestamp, date_create('@' . $timestamps[$key]))->days;

            if ($value > 0) {
                $positive = true;
            }
            if ($value < 0) {
                $negative = true;
            }
        }
        //XIRR remove all keys from the input array (which are the timestamps)
        $values = array_values($values);

        // Return error if values does not contain at least one positive value and one negative value
        if (!$positive || !$negative) {
            return null;
        }

        // Initialize guess and resultRate
        $resultRate = $guess;

        // Implement Newton's method

        $iteration = 0;
        $contLoop = true;
        while ($contLoop && (++$iteration < self::FINANCIAL_MAX_ITERATIONS)) {
            $resultValue = self::irrResult($values, $dates, $resultRate);
            $newRate = $resultRate - $resultValue / self::irrResultDeriv($values, $dates, $resultRate);
            $epsRate = abs($newRate - $resultRate);
            $resultRate = $newRate;
            $contLoop = ($epsRate > self::FINANCIAL_ACCURACY) && (abs($resultValue) > self::FINANCIAL_ACCURACY);
        }

        if ($contLoop) {
            return null;
        }

        // Return internal rate of return
        return $resultRate;
    }

    // Calculates the resulting amount
    private static function irrResult($values, $dates, $rate)
    {
        self::init();

        $r = $rate + 1;
        $result = $values[0];
        for ($i = 1, $iMax = count($values); $i < $iMax; $i++) {
            $result += $values[$i] / ($r ** (($dates[$i] - $dates[0]) / 365));
        }

        return $result;
    }

    // Calculates the first derivation
    private static function irrResultDeriv($values, $dates, $rate)
    {
        self::init();

        $r = $rate + 1;
        $result = 0;
        for ($i = 1, $iMax = count($values); $i < $iMax; $i++) {
            $frac = ($dates[$i] - $dates[0]) / 365;
            $result -= $frac * $values[$i] / ($r ** ($frac + 1));
        }

        return $result;
    }


    /**
     * @param float   $apr  Interest rate.
     * @param integer $term Loan length in years.
     * @param float   $loan The loan amount.
     *
     * @return float
     */
    public function calculatePMT($apr, int $term, $loan): float
    {
        $term = $term * 12;
        $apr = $apr / 1200;
        $amount = $apr * -$loan * ((1 + $apr) ** $term) / (1 - ((1 + $apr) ** $term));

        return round($amount);

    }
}
