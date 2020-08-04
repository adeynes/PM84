<?php
declare(strict_types=1);

namespace adeynes\PM84\functions;

use pocketmine\math\Vector3;

class KuenSurface implements PM84Function
{

    /** @return float[] */
    public function getXDomainBounds(): array
    {
        return [-1.2, 1.2];
    }

    /** @return float[] */
    public function getYDomainBounds(): array
    {
        return [-1.1, 2];
    }

    /** @return float[] */
    public function getZDomainBounds(): array
    {
        return [-3, 3];
    }

    /** @return float[] */
    public function getUBounds(): array
    {
        return [-1.5*pi(), 1.5*pi()];
    }

    /** @return float[] */
    public function getVBounds(): array
    {
        return [-1.5*pi(), 1.5*pi()];
    }

    public function function_(float $u, float $v): Vector3
    {
        $r = cosh($v)**2 + $u**2;
        return new Vector3(
            2 * cosh($v) * (-$u * cos($u) + sin($u)) / $r,
            2 * cosh($v) * (cos($u) + $u * sin($u)) / $r,
            $v - (2 * sinh($v) * cosh($v)) / $r
        );
    }
}