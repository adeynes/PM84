<?php
declare(strict_types=1);

namespace adeynes\PM84\functions;

use adeynes\PM84\utils\NewtonRaphson;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;

// This was so fun
class Penis implements PM84Function
{

    /** @var Vector2[] */
    public $parametrization2d;

    /** @var float */
    public $precision;

    public function __construct()
    {
        $this->precision = 0.0001;
        $this->parametrization2d = $this->parametrize2d($this->precision);
    }

    /** @return float[] */
    public function getXDomainBounds(): array
    {
        return [-1.1, 1.1];
    }

    /** @return float[] */
    public function getYDomainBounds(): array
    {
        return [-0.6, 2.15];
    }

    /** @return float[] */
    public function getZDomainBounds(): array
    {
        return [-1.1, 1.1];
    }

    /** @return float[] */
    public function getUBounds(): array
    {
        return [0, 2*pi()];
    }

    /** @return float[] */
    public function getVBounds(): array
    {
        return [0, pi()];
    }

    public function function_(float $u, float $v): Vector3
    {
        $vector2 = $this->function2d($u);
        // If we simply rotate we get an ellipsoid for the testicles, we want two spheres
        // Right testicle: u ∈ [0, 0.7] ∪ [3pi/2, 2pi[. Center (0.55, -0.1)
        if ($u <= 0.5 || $u > 3*pi()/2) {
            return new Vector3(
                ($vector2->getX() - 0.5) * cos(2*$v) + 0.25,
                $vector2->getY(),
                ($vector2->getX() - 0.5) * sin(2*$v) + 0.25
            );
        }
        // Left testicle: u ∈ [pi - 0.7, 3pi/2]. Center (-0.55, -0.1)
        if ($u >= pi()-0.5 && $u <= 3*pi()/2) {
            return new Vector3(
                ($vector2->getX() + 0.5) * cos(2*$v) - 0.25,
                $vector2->getY(),
                ($vector2->getX() + 0.5) * sin(2*$v) - 0.25
            );
        }
        return new Vector3(
            $vector2->getX() * cos($v),
            $vector2->getY(),
            $vector2->getX() * sin($v)
        );
    }

    public function function2d(float $u): Vector2
    {
        $rounded_u = $u - fmod($u, $this->precision);
        if (isset($this->parametrization2d[strval($rounded_u)])) {
            return $this->parametrization2d[strval($rounded_u)];
        } else {
            $sign = 1;
            $increment = $this->precision;
            while (true) {
                $next_u = $rounded_u + $sign * $increment;
                if (isset($this->parametrization2d[strval($next_u)])) {
                    return $this->parametrization2d[strval($next_u)];
                }
                if ($sign === -1) {
                    $increment += $this->precision;
                }
                $sign *= -1;
            }
        }

        // Intentionally throw a type error
        return null;
    }

    protected function implicitFunction2d(float $x, float $y): float
    {
        return (7 * $x**6) + (2.8 * $x**4 * ($y-0.9)**2) - (5.6 * $x**4) + (10.08 * $x**2 * ($y-0.9)**4)
             + (2.52 * $x**2 * ($y-0.9)**3) - (20.264496 * $x**2 * ($y-0.9)**2) + (8.652 * $x**2)
             + (0.98 * ($y-0.9)**6) - (2.9498 * ($y-0.9)**4) + (2.94 * ($y-0.9)**2) - 1.005;
    }

    /**
     * Here we parametrize the 2d penis (1d in theory but embedded in 2-space)
     * by tracing a rotating ray from the origin and tracking its point of
     * intersection with the curve. We then have a parametrization theta -> (x, y)
     * @param float $delta
     * @return Vector2[]
     */
    protected function parametrize2d(float $delta): array
    {
        /** @var Vector2[] $map theta -> (x, y) */
        $map = [];

        for ($num_theta = 0; $num_theta <= floor(2*pi() / $delta); ++$num_theta) {
            $theta = $num_theta * $delta;

            // we manually set the values for theta = pi/2 and 3pi/2 since tan is undefined there
            // low precision but it's fine, minecraft blocks aren't very precise themselves
            // and the derivative w.r.t. x is close to 0 a the top & bottom
            if (abs($theta - pi()/2) <= 0.01) {
                $map[$theta] = new Vector2(0, 2.1003);
                continue;
            }

            if (abs($theta - 3*pi()/2) <= 0.01) {
                $map[$theta] = new Vector2(0, -0.3003);
                continue;
            }

            $tan = tan($theta);

            // our guess is the intersection of y = tan($theta)x with a rectangle:
            // y = 0.25 and y = 2
            // x = +/- 0.35
            // if we go above (resp. below) the corner we just set the guess to +/- 0.4
            // otherwise we calculate the intersection with the bottom (resp. top) of the rectangle
            // this is for the case when we're in the shaft part
            if ($theta > 0.5 && $theta < pi() - 0.5) {
                if ($theta < pi() / 2) {
                    $guess = $tan * 0.34 >= 2 ? 2 / $tan : 0.34;
                } elseif ($theta < pi()) {
                    $guess = $tan * -0.34 >= 2 ? 2 / $tan : -0.34;
                } else {
                    throw new \InvalidStateException("Reached the end of theta if-block: theta = $theta");
                }
            }
            // if we're around the testicles we use the rectangle:
            // y = -0.4 and y = 0.25
            // x = +/- 0.9
            else {
                if ($theta < pi() / 2) {
                    $guess = $tan * 0.9 >= 0.25 ? 0.25 / $tan : 0.9;
                } elseif ($theta < pi()) {
                    $guess = $tan * -0.9 >= 0.25 ? 0.25 / $tan : -0.9;
                } elseif ($theta < 3 * pi() / 2){
                    $guess = $tan * -0.9 <= -0.4 ? -0.4 / $tan : -0.9;
                } elseif ($theta < 2 * pi()){
                    $guess = $tan * 0.9 <= -0.4 ? -0.4 / $tan : 0.9;
                } else {
                    throw new \InvalidStateException("Reached the end of theta if-block: theta = $theta");
                }
            }

            $zero_x = (new NewtonRaphson(
                function ($x) use ($tan) {
                    return $this->implicitFunction2d($x, $tan * $x);
                },
                function ($x) use ($tan) {
                    return 5.88 * $tan**6 * $x**5 - 26.46 * $tan**5 * $x**4 + $tan**4 * (60.48 * $x**5 + 35.8288 * $x**3)
                         + $tan**3 * (-168.84 * $x**4 - 11.0074 * $x**2) + $tan**2 * (16.8 * $x**5 + 87.6812 * $x**3 - 3.50272 * $x)
                         + $tan * (-25.2 * $x**4 + 39.6192 * $x**2 - 0.162464) + 42 * $x**5 - 13.328 * $x**3 - 5.97167 * $x;
                }
            ))->findZero($guess, 0.001);

            if ($zero_x !== null) {
                $map[strval($theta)] = new Vector2($zero_x, $tan * $zero_x);
            } else {
                var_dump("theta : $theta");
            }
        }

        return $map;
    }

}