<?php

namespace App\Services;

use App\Models\SoilLayer;

class AllowablePileCapacity
{
    public $RequiredAllowablePileCapacity;
    public $CompressionResultsMin;
    public $CompressionResultsMax;
    public $F1;
    public $F2;
    public $F3;
    public $E1;
    public $E2;
    public $E3;
    public $Y1;
    public $Y2;
    public $Y3;
    private $RequiredSafetyFactor;
    public $RequiredUltimateCapacity;
    private $TorqueCorrelationFactor;
    public $RequiredInstallationTorque;
    private $RequiredInstallationTorqueValue;
    private $CompressionResults;

    public function __construct(
        float $pKips = 0.0,
        float $pRequiredSafetyFactor = 0.0,
        float $pTorqueCorrelationFactor = 0.0,
        array $pCompressionResults = null
    ) {
        $this->RequiredAllowablePileCapacity = new RequiredAllowablePileCapacity($pKips);
        $this->RequiredUltimateCapacity = new RequiredUltimateCapacity($this->RequiredAllowablePileCapacity, $pRequiredSafetyFactor);
        $this->RequiredSafetyFactor = $pRequiredSafetyFactor;
        $this->TorqueCorrelationFactor = $pTorqueCorrelationFactor;
        $dReqAltCap = $this->RequiredUltimateCapacity->getOutput();

        $this->CompressionResults = $pCompressionResults;

        if (empty($pCompressionResults)) {
            throw new \Exception("Error Allowable Pile Capacity Calculation: Compression Results is Empty");
        }
        $this->CompressionResultsMin = $this->getCompressionResultsMin($dReqAltCap);
        if ($this->CompressionResultsMin === null) {
            throw new \Exception("Error Allowable Pile Capacity Calculation: <br\\> Could not find Compression Results Min, Change Helix Configuration<br\\>");
        }

        $this->CompressionResultsMax = $this->getCompressionResultsMax($dReqAltCap);
        if ($this->CompressionResultsMax === null) {
            throw new \Exception("Error Allowable Pile Capacity Calculation: <br\\> Could not find Compression Results Max, Increase Embedment Depth (or) Change Helix Configuration<br\\>");
        }

        $this->RequiredInstallationTorque = new RequiredInstallationTorque(
            $pTorqueCorrelationFactor,
            $this->RequiredUltimateCapacity,
            $this->CompressionResultsMin,
            $this->CompressionResultsMax
        );

        $this->RequiredInstallationTorqueValue = $this->RequiredInstallationTorque->getOutput();

        $this->InitFormulaVariables();
    }

    public function getCompressionResultsMin(float $dReqAltCap): array
    {
        $result = null;
        
        // Sort by embedment
        $sortedResults = collect($this->CompressionResults)->sortBy('Embedment')->all();
        
        foreach ($sortedResults as $e) {
            if ($e['anchor_capacity'] > $dReqAltCap) {
                break;
            }
            $result = $e;
        }

        return $result;
    }

    public function getCompressionResultsMax(float $dReqAltCap): array
    {
        $result = null;
        
        // Sort by embedment
        $sortedResults = collect($this->CompressionResults)->sortBy('Embedment')->all();
        
        foreach ($sortedResults as $e) {
            if ($e['anchor_capacity'] > $dReqAltCap) {
                $result = $e;
                break;
            }
        }

        return $result;
    }

    private function InitFormulaVariables(): void
    {
        $this->F1 = $this->CompressionResultsMin['shaft_resistance'];
        $this->E1 = $this->CompressionResultsMin['sumQT'];
        $this->Y1 = $this->CompressionResultsMin['torsional_resistance'];

        $this->F3 = $this->CompressionResultsMax['shaft_resistance'];
        $this->E3 = $this->CompressionResultsMax['sumQT'];
        $this->Y3 = $this->CompressionResultsMax['torsional_resistance'];

        // assign Y2 from RequiredInstallationTorque
        $this->Y2 = $this->RequiredInstallationTorque->getLinearInterpolationFormula();
        $this->F2 = ((($this->Y2 - $this->Y1) * ($this->F3 - $this->F1)) / ($this->Y3 - $this->Y1)) + $this->F1;
        $this->E2 = ((($this->Y2 - $this->Y1) * ($this->E3 - $this->E1)) / ($this->Y3 - $this->Y1)) + $this->E1;
    }

    public function getAllowableFrictionalResistance(): float
    {
        return $this->F2 / $this->RequiredSafetyFactor;
    }

    public function getAllowableEndBearing(): float
    {
        return $this->E2 / $this->RequiredSafetyFactor;
    }

    public function getOutput(): float
    {
        return $this->getAllowableFrictionalResistance() + $this->getAllowableEndBearing();
    }

    public function getTorque(): float
    {
        return $this->RequiredInstallationTorqueValue;
    }

    public function getDepth(): float
    {
        $ruc = $this->RequiredUltimateCapacity->getOutput();
        $apc = $this->getOutput();
        $value = $this->RequiredInstallationTorqueValue;
        $Emin = 0;
        $flag = false;

        // Sort by embedment
        $sortedResults = collect($this->CompressionResults)->sortBy('Embedment')->all();
        
        foreach ($sortedResults as $e) {
            $Emin = $e['Embedment'];
            $dAc = ($e['anchor_capacity'] / 2);
            
            if ($e['torsional_resistance'] > $this->RequiredInstallationTorqueValue && $e['anchor_capacity'] > $ruc) {
                $flag = true;
                break;
            }
        }
        
        return $flag ? $Emin : -1;
    }
}