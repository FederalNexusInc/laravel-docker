<?php

namespace App\Services;

use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PDFResult
{
    public function generatePdf(array $resultData): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // Prepare all the data needed for the PDF
        $pdfData = [
            // Project Information
            'project_name' => $resultData['ProjectName'] ?? 'N/A',
            'project_address' => $resultData['ProjectAddress'] ?? 'N/A',
            'project_number' => $resultData['ProjectNumber'] ?? 'N/A',
            'project_type' => $resultData['AnchorType'] ?? 'Guy Anchor',
            'field_notes' => $resultData['ProjectNotes'] ?? 'N/A',
            
            // Analyst/Specialist Information
            'analyst_name' => Auth::user()->name ?? 'N/A',
            'analyst_company' => Auth::user()->company_name ?? 'N/A',
            'analyst_email' => Auth::user()->email ?? 'N/A',
            'specialist_name' => $resultData['SpecialistName'] ?? 'N/A',
            'specialist_email' => $resultData['SpecialistEmail'] ?? 'N/A',
            'specialist_company' => $resultData['SpecialistCompany'] ?? 'N/A',
            
            // From calculation results
            'required_allowable_capacity' => round($resultData['RequiredAllowableCapacity'] ?? 0, 2),
            'safety_factor' => round($resultData['RequiredSafetyFactor'] ?? 0, 2),
            'pile_diameter' => $resultData['HelicalPileDiameter'] ?? 'N/A',
            'helix_configuration' => $resultData['HelixConfiguration'] ?? 'N/A',
            'torque_factor' => round($resultData['EmpericalTorqueFactor'] ?? 0, 2),
            'allowable_frictional_resistance' => round($resultData['AllowableFrictionalResistance'] ?? 0, 2),
            'allowable_end_bearing' => round($resultData['AllowableEndBearing'] ?? 0, 2),
            'allowable_pile_capacity' => round($resultData['AllowablePileCapacity'] ?? 0, 2),
            'approx_embedment_depth' => round($resultData['ApproximatePileEmbedmentDepth'] ?? 0, 2),
            'required_installation_torque' => round($resultData['RequiredInstallationTorque'] ?? 0, 2),
            
            // Soil Information (from project)
            'omit_shaft_resistance' => $resultData['OmitShaftResistance'] ?? false,
            'omit_helix_mechanical_strength_check' => $resultData['OmitHelixMechanicalStrengthCheck'] ?? false,
            'omit_shaft_mechanical_strength_check' => $resultData['OmitShaftMechanicalStrengthCheck'] ?? false,
            'soil_report_number' => $resultData['SoilReportNumber'] ?? 'N/A',
            'soil_report_date' => $resultData['SoilReportDate'] ?? 'N/A',
            'boring_number' => $resultData['BoringNumber'] ?? 'N/A',
            'boring_log_date' => $resultData['BoringLogDate'] ?? 'N/A',
            'boring_termination_depth' => $resultData['BoringTerminationDepth'] ?? 'N/A',
            'water_table_depth' => $resultData['WaterTableDepth'] ?? 'N/A',
            'max_depth' => $resultData['MaxDepth'] ?? 'N/A',
            'upper_soil_type' => $resultData['SoilType'] ?? 'N/A',
            'indication_angle' => $resultData['AnchorDeclinationDegree'] ?? 0,
            'pile_head_position' => $resultData['PileHeadPosition'] ?? 0,
            'x_values' => $resultData['XValues'] ?? [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
            'y_values' => $resultData['YValues'] ?? [1 => 5, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
            'soil_profile' => $resultData['SoilLayers'] ?? [],
            'torsional_resistance_limit' => $resultData['RequiredInstallationTorque'] ?? 5000,
            
            // Depth results
            'calculation_type' => $resultData['CalculationType'] ?? 'N/A',
            'depth_results' => $this->formatDepthResults($resultData['DepthResults'] ?? []),
        ];

        // Generate the PDF
        $pdf = Pdf::loadView('pdf.ramjack-report', $pdfData)
                  ->setPaper('letter', 'portrait');
        
                  // Render the HTML as PDF
        $pdf->render();

        $font = $pdf->getFontMetrics()->get_font("helvetica");
        if ($pdf->get_canvas()->get_page_number() > 1) {
            $grayColor = array(0.6, 0.6, 0.6); 
            // Add page number to the left side
            $pdf->getCanvas()->page_text(30, 750, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, $grayColor);
            // Add the timestamp to the right side of the footer
            $dt = new \DateTime("now", new \DateTimeZone("America/New_York"));
            $currentDate = $dt->format("m/d/Y h:i:s A");
            $pdf->getCanvas()->page_text(450, 750, "Printed On $currentDate", $font, 8, $grayColor);
        }
        // Return as download
        return response()->streamDownload(
            fn () => print($pdf->output()),
            "ramjack-report-{$resultData['ProjectName']}-".now()->format('Y-m-d').".pdf"
        );
    }

    protected function formatDepthResults(array $depthResults): array
    {
        $formatted = [];

        // Calculate the mean (average) depth
        $totalDepth = 0;
        $depthCount = count($depthResults);

        foreach ($depthResults as $depth => $results) {
            $totalDepth += $depth;
        }

        $embedmentDepth = $depthCount > 0 ? $totalDepth / $depthCount : 0;

        // Define bounds: embedment ± 20 ft
        $minDepth = $embedmentDepth - 20 >= 1? $embedmentDepth - 20 : 1;
        $maxDepth = $embedmentDepth + 20 <= 100? $embedmentDepth + 20 : 100;

        // Loop and filter only depths within the ±20 ft range of the mean depth
        foreach ($depthResults as $depth => $results) {
            // Make sure $depth is numeric for comparison
            if (is_numeric($depth) && $depth >= $minDepth && $depth <= $maxDepth) {
                $formatted[] = [
                    'depth' => (float) $depth,
                    'capacity' => round($results['anchor_capacity'] ?? 0, 2),
                    'resistance' => round($results['torsional_resistance'] ?? 0, 2),
                ];
            }
        }

        //sort by depth (ascending)
        usort($formatted, fn($a, $b) => $a['depth'] <=> $b['depth']);
        return $formatted;
    }

}