<?php

namespace App\Services;

class RequiredUltimateCapacity
{
    private RequiredAllowablePileCapacity $objReqAlwPilCap;
    private float $requiredSafetyFactor;

    public function __construct(RequiredAllowablePileCapacity $rapcObject, float $pRequiredSafetyFactor)
    {
        $this->objReqAlwPilCap = $rapcObject;
        $this->requiredSafetyFactor = $pRequiredSafetyFactor;
    }

    public function getOutput(): float
    {
        return $this->objReqAlwPilCap->getLbs() * $this->requiredSafetyFactor;
    }
}