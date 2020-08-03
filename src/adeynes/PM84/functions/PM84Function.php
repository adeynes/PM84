<?php
declare(strict_types=1);

namespace adeynes\PM84\functions;

use pocketmine\math\Vector3;

interface PM84Function
{

    // ALL BOUNDS ARE INCLUSIVE LEFT, EXCLUSIVE RIGHT

    /** @return float[] */
    public function getXDomainBounds(): array;

    /** @return float[] */
    public function getYDomainBounds(): array;

    /** @return float[] */
    public function getZDomainBounds(): array;

    /** @return float[] */
    public function getUBounds(): array;

    /** @return float[] */
    public function getVBounds(): array;

    public function function_(float $u, float $v): Vector3;

}