<?php

namespace App\Services;

class RequiredInstallationTorque
{
    private $CompressionResultsMin;
    private $CompressionResultsMax;
    private $RequiredUltimateCapacity;
    private $TorqueCorrelationFactor;
    public $x1;
    public $x2;
    public $x3;
    public $y1;
    public $y2;
    public $y3;
    public $RequiredInstallationTorque1;
    public $RequiredInstallationTorque2;

    public function __construct(
        float $pTorqueCorrelationFactor,
        RequiredUltimateCapacity $pReqUltCapacity,
        array $pCompressionResultsMin,
        array $pCompressionResultsMax
    ) {
        $this->TorqueCorrelationFactor = $pTorqueCorrelationFactor;
        $this->CompressionResultsMin = $pCompressionResultsMin;
        $this->CompressionResultsMax = $pCompressionResultsMax;
        $this->RequiredUltimateCapacity = $pReqUltCapacity;

        $this->x1 = $this->CompressionResultsMin['anchor_capacity'];
        $this->x2 = $this->RequiredUltimateCapacity->getOutput();
        
        // Calculate x3 as sum of all helices plus shaft resistance
        $this->x3 = $this->CompressionResultsMax['anchor_capacity']; 
        
        $this->y1 = $this->CompressionResultsMin['torsional_resistance'];
        $this->y3 = $this->CompressionResultsMax['torsional_resistance'];

        $this->RequiredInstallationTorque1 = $this->RequiredUltimateCapacity->getOutput() / $this->TorqueCorrelationFactor;
        
        // (y2) = {[(x2 - x1) (y3 - y1)]/ (x3 - x1)} + y1
        $this->y2 = ((($this->x2 - $this->x1) * ($this->y3 - $this->y1)) / ($this->x3 - $this->x1)) + $this->y1;
        $this->RequiredInstallationTorque2 = $this->y2;
    }

    public function getLinearInterpolationFormula(): float
    {
        return $this->y2;
    }

    public function getMax(): float
    {
        return ($this->RequiredInstallationTorque1 > $this->RequiredInstallationTorque2) 
            ? $this->RequiredInstallationTorque1 
            : $this->RequiredInstallationTorque2;
    }

    public function getOutput(): float
    {
        $temp = (int)$this->getMax();
        
        // Round this value up to the nearest 100
        if ($temp % 100 != 0) {
            $temp = ($temp - $temp % 100) + 100;
        }
        
        return $temp;
    }
}