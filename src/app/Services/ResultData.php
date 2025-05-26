<?php

namespace App\Services;

class ResultData
{
    // Project Data
    public $ProjectId;
    public $ProjectName;
    public $ProjectNumber;
    public $SoilReportNumber;
    public $SoilReportDate;
    public $PileType;
    public $BoringNumber;
    public $BoringLogDate;
    public $BoringTerminationDepth;
    public $ProjectAddress;
    public $ProjectNotes;

    // Soil Specialist Data
    public $SpecialistName;
    public $SpecialistEmail;
    public $SpecialistCompany;

    // Soil Profile Data
    public $MaxDepth;
    public $WaterTableDepth;
    public $SoilType;

    // Soil Layer Data
    public $SoilLayers = [];

    // Anchor Data
    public $AnchorId;
    public $EmpericalTorqueFactor;
    public $RequiredAllowableCapacity;
    public $CalculationType;
    public $RequiredSafetyFactor;
    public $AnchorDeclinationDegree;
    public $PileHeadPosition;
    public $XValues = [];
    public $YValues = [];
    public $OmitShaftResistance;
    public $OmitHelixMechanicalStrengthCheck;
    public $OmitShaftMechanicalStrengthCheck;

    // Helix Data
    public $HelicalPileDiameter;
    public $HelixConfiguration;


    // Calculation Type
    public $RequiredAllowablePileCapacity;
    public $RequiredUltimateCapacity;
    public float $calculationInterval = 1.0;
    public $RequiredInstallationTorque;
    public $AllowableEndBearing;
    public $AllowableFrictionalResistance;
    public $AllowablePileCapacity;
    public $ApproximatePileEmbedmentDepth;
    public $DepthResults = [];

    /**
     * Adds depth-specific results to the ResultData object.
     *
     * @param int $depth
     * @param array $results
     */
    public function addDepthResult(int $depth, array $results): void
    {
        $this->DepthResults[$depth] = $results;
    }
    
    public function validateTorqueCalculation(): void
    {
        if ($this->RequiredInstallationTorque <= 0) {
            throw new \RuntimeException(
                "Invalid torque calculation result. " .
                "Check anchor configuration and soil parameters."
            );
        }
    }

    /**
     * Converts the ResultData object to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            // Project Data
            'ProjectId' => $this->ProjectId,
            'ProjectName' => $this->ProjectName,
            'ProjectNumber' => $this->ProjectNumber,
            'SoilReportNumber' => $this->SoilReportNumber,
            'SoilReportDate' => $this->SoilReportDate,
            'PileType' => $this->PileType,
            'BoringNumber' => $this->BoringNumber,
            'BoringLogDate' => $this->BoringLogDate,
            'BoringTerminationDepth' => $this->BoringTerminationDepth,
            'ProjectAddress' => $this->ProjectAddress,
            'ProjectNotes' => $this->ProjectNotes,
            
            // Soil Specialist Data
            'SpecialistName' => $this->SpecialistName,
            'SpecialistEmail' => $this->SpecialistEmail,
            'SpecialistCompany' => $this->SpecialistCompany,
            
            // Soil Profile Data
            'MaxDepth' => $this->MaxDepth,
            'WaterTableDepth' => $this->WaterTableDepth,
            'SoilType' => $this->SoilType,
            'SoilLayers' => $this->SoilLayers,
            
            // Anchor Data
            'AnchorId' => $this->AnchorId,
            'AnchorDeclinationDegree' => $this->AnchorDeclinationDegree,
            'PileHeadPosition' => $this->PileHeadPosition,
            'XValues' => $this->XValues,
            'YValues' => $this->YValues,
            'OmitShaftResistance' => $this->OmitShaftResistance,
            'OmitHelixMechanicalStrengthCheck' => $this->OmitHelixMechanicalStrengthCheck,
            'OmitShaftMechanicalStrengthCheck' => $this->OmitShaftMechanicalStrengthCheck,
            
            // Helix Data
            'HelicalPileDiameter' => $this->HelicalPileDiameter,
            'HelixConfiguration' => $this->HelixConfiguration,
            
            // Calculation Results
            'EmpericalTorqueFactor' => $this->EmpericalTorqueFactor,
            'RequiredAllowableCapacity' => $this->RequiredAllowableCapacity,
            'CalculationType' => $this->CalculationType,
            'RequiredSafetyFactor' => $this->RequiredSafetyFactor,
            'RequiredAllowablePileCapacity' => $this->RequiredAllowablePileCapacity,
            'RequiredUltimateCapacity' => $this->RequiredUltimateCapacity,
            'RequiredInstallationTorque' => $this->RequiredInstallationTorque,
            'AllowableEndBearing' => $this->AllowableEndBearing,
            'AllowableFrictionalResistance' => $this->AllowableFrictionalResistance,
            'AllowablePileCapacity' => $this->AllowablePileCapacity,
            'ApproximatePileEmbedmentDepth' => $this->ApproximatePileEmbedmentDepth,
            'DepthResults' => $this->DepthResults,
        ];
    }
}