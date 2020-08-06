<?php
declare(strict_types=1);

namespace adeynes\PM84\utils;

class NewtonRaphson
{

    public const DEFAULT_EPSILON = 0.001;

    /** @var callable[[float], float] */
    protected $f;

    /** @var callable[[float], float] Derivative of f */
    protected $fDeriv;

    public function __construct($f, $fDeriv)
    {
        $this->f = $f;
        $this->fDeriv = $fDeriv;
    }

    /**
     * @param float $guess
     * @param float $epsilon
     * @return float|null null if it hasn't converged fast enough
     */
    public function findZero(float $guess, float $epsilon = self::DEFAULT_EPSILON): ?float
    {
        $x = $guess;
        $count = 0;
        do {
            if ($count === 499999) {
                var_dump("stopped at x : $x, guess : $guess");
            }
            try {
                $fx = ($this->f)($x);
                $h = $fx / ($this->fDeriv)($x);
            } catch (\ErrorException $e) {
                var_dump("division by 0");
                var_dump($x);
                return null;
            }
            $x -= $h;
            if (++$count > 500000) return null;
        } while (abs($fx) > $epsilon);

        return $x;
    }

}