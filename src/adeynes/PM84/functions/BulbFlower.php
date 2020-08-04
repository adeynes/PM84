<?php
declare(strict_types=1);

namespace adeynes\PM84\functions;

use pocketmine\math\Vector3;

class BulbFlower implements PM84Function
{

    /** @return float[] */
    public function getXDomainBounds(): array
    {
        return [-2, 2];
    }

    /** @return float[] */
    public function getYDomainBounds(): array
    {
        return [-2, 2];
    }

    /** @return float[] */
    public function getZDomainBounds(): array
    {
        return [-2.5, 2.5];
    }

    /** @return float[] */
    public function getUBounds(): array
    {
        return [0, pi()];
    }

    /** @return float[] */
    public function getVBounds(): array
    {
        return [0, 2*pi()];
    }

    public function function_(float $u, float $v): Vector3
    {
        $r = sin(4*$u)**3 + cos(2*$u)**3 + sin(6*$v)**2 + cos(6*$v)**4;
        return new Vector3(
            $r * sin($u) * sin($v),
            $r * sin($u) * cos($v),
            $r * cos($u)
        );
    }
    
}