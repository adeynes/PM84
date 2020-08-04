<?php
declare(strict_types=1);

namespace adeynes\PM84\functions;

use pocketmine\math\Vector3;

class KleinFigure8 implements PM84Function
{

    /** @return float[] */
    public function getXDomainBounds(): array
    {
        return [-5, 5];
    }

    /** @return float[] */
    public function getYDomainBounds(): array
    {
        return [-1.2, 1.2];
    }

    /** @return float[] */
    public function getZDomainBounds(): array
    {
        return [-5, 5];
    }

    /** @return float[] */
    public function getUBounds(): array
    {
        return [0, 2*pi()];
    }

    /** @return float[] */
    public function getVBounds(): array
    {
        return [0, 2*pi()];
    }

    public function function_(float $u, float $v): Vector3
    {
        $r = 3.5;
        $s = $r + cos($u/2) * sin($v) - sin($u/2) * sin(2*$v);
        return new Vector3(
            $s * cos($u),
            sin($u/2) * sin($v) + cos($u/2) * sin(2*$v),
            $s * sin($u)
        );
    }

}