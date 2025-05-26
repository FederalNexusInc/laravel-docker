<?php

namespace App\Services;

class RequiredAllowablePileCapacity
{
    private float $kips = 0;

    public function __construct(float $kips)
    {
        $this->kips = $kips;
    }

    public function getKips(): float
    {
        return $this->kips;
    }

    public function setKips(float $kips): void
    {
        $this->kips = $kips;
    }

    public function getLbs(): float
    {
        return $this->kips * 1000;
    }
}