<?php
declare(strict_types=1);

namespace adeynes\PM84\functions;

use pocketmine\math\Vector3;

class Hyperboloid implements PM84Function
{

    /** @return float[] */
    public function getXDomainBounds(): array
    {
        return [-4, 4];
    }

    /** @return float[] */
    public function getYDomainBounds(): array
    {
        return [-4, 4];
    }

    /** @return float[] */
    public function getZDomainBounds(): array
    {
        return [-4, 4];
    }

    /** @return float[] */
    public function getUBounds(): array
    {
        return [-2, 2];
    }

    /** @return float[] */
    public function getVBounds(): array
    {
        return [0, 2*pi()];
    }

    public function function_(float $u, float $v): Vector3
    {
        return new Vector3(
            cosh($u) * cos($v),
            sinh($u),
            cosh($u) * sin($v)
        );
    }

}