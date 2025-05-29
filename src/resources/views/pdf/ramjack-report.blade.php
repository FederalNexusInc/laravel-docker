<!DOCTYPE html>
<html>
<head>
    <title>RAMJACK FOUNDATION SOLUTIONS™ - Project Report</title>
    <style>
        /* Base Styles */
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            /* margin: 15mm 15mm 15mm 15mm; */
            color: #333;
        }
        
        /* Header Styles */
        .header-cover {
            text-align: left;
            margin-bottom: 4mm;
        }

        .header {
            text-align: left;
            margin-bottom: 4mm;
            border-bottom: 1px solid rgb(78, 78, 78);
        }
        
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            color: #CA0101;
            margin: 0;
            padding: 0;
        }
        
        /* Section Styles */
        .section {
            margin-bottom: 6mm;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #CA0101;
            margin-bottom: 3mm;
            padding-bottom: 1mm;
            /* border-bottom: 1px solid #ddd; */
        }
        
        .subsection-title {
            font-weight: bold;
            margin: 3mm 0 2mm 0;
        }
        
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 3mm 0 5mm 0;
            font-size: 9pt;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
            padding: 3mm 2mm;
            border: 1px solid #ddd;
        }
        
        td {
            padding: 2mm;
            border: 1px solid #ddd;
        }
        
        /* Special Elements */
        .value-highlight {
            font-weight: bold;
            color: #CA0101;
        }
        
        .unit {
            color: #666;
            font-size: 0.9em;
        }
        
        .warning {
            color: #cc0000;
            font-weight: bold;
        }
        
        .note {
            font-style: italic;
            color: #666;
            font-size: 0.9em;
            color: #cc0000;
        }
        
        /* Layout */
        .two-column {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5mm;
        }
        
        .column {
            width: 48%;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .footer {
            margin-top: 10mm;
            font-size: 8pt;
            text-align: center;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 2mm;
        }
        
        /* Formula Styling */
        .formula {
            font-family: "DejaVu Sans", sans-serif;
            background-color: #f8f8f8;
            padding: 2mm;
            border-radius: 3px;
            margin: 2mm 0;
            font-size: 10px;
        }
        
        .formula-var {
            font-family: "DejaVu Sans", sans-serif;
            font-style: italic;
            font-size: 10px;
        }

        .cover-title {
            font-weight: bold;
            font-size: 32px;
            text-align: center;
        }
        .center {
            text-align: center;
            margin: 2px;
        }
        .bold {
            font-weight: bold;
        }
        .name {
            font-size: 18px;
        }
        .underline {
            text-decoration: underline;
        }

        .red {
            color: #CA0101;
        }

        .inline-block {
            display: inline-block;
        }
        .w-180 {
            width: 180px;
        }

        .half {
            width: 48%;
            float: left;
            margin-right: 4%;
        }
        .half:last-child {
            margin-right: 0;
        }
        .clearfix {
            clear: both;
        }
       
    </style>
</head>
<body>
    <!-- Page 1 - Cover Page -->
    <div class="header-cover">
    <img src="{{ 'file://' . public_path('images/ramjack_pdf_logo_white.png') }}" width="300px" alt="RAMJACK FOUNDATION SOLUTIONS™">
    </div>
    <div class="section" style="margin-top: 60mm; line-height: 1.5;">
        <div class="cover-title red">{{ $project_name ?? '' }}</div>
        <div class="center bold red" style="font-size: 20px; margin-bottom: 12px;"> {{ $project_number ? '('.$project_number.')' : '' }} </div>
        <div class="center"> {{ $project_address ?? '' }} </div>
        <div class="center"> {{ $project_type ?? '' }} </div>
    </div>
    <div class="section" style="margin-top: 50mm">
        <div class="center bold name">{{ $analyst_name ?? '' }}</div>
        <div class="center bold">Analyzer</div>
        <div class="center">{{ $analyst_company ?? '' }}</div>
        <div class="center underline">{{ $analyst_email ?? '' }}</div>
    </div>
    <div class="section" style="margin-top: 24px">
        <div class="center bold name">{{ $specialist_name ?? '' }}</div>
        <div class="center bold">Ram Jack Specialist</div>
        <div class="center">{{ $specialist_company ?? '' }}</div>
        <div class="center underline">{{ $specialist_email ?? '' }}</div>
    </div>

    

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Page 2 - Soil Information -->
    <div class="header">
        <span>
        <img src="{{ 'file://' . public_path('images/ramjack_pdf_logo_white.png') }}" width="240px" alt="RAMJACK FOUNDATION SOLUTIONS™">
        </span>
    </div>

    <div class="section">
        <div class="section-title"> Analysis Options</div>
        <table style="width: 100%; border: none; border-collapse: collapse;">
            <tr>
                <td style="border: none; width: 33%;">Omit Shaft Resistance: {{ $omit_shaft_resistance ? 'Yes' : 'No' }}</td>
                <td style="border: none; width: 33%;">Omit Mechianical Strength Checks: {{ $omit_helix_mechanical_strength_check ? 'Yes' : 'No' }}</td>
                <td style="border: none; width: 33%;">Omit Shaft Strength Checks: {{ $omit_shaft_mechanical_strength_check ? 'Yes' : 'No'}}</td>
            </tr>
        </table>
    </div>
    <div class="section" >
        <div class="half">
            <div class="section-title">Pile Specifications</div>

            <div class="section">
                <p>
                    <span style="display: inline-block; width: 200px;">Helical Pile Diameter: </span>
                    <span style="display: inline-block;">{{ $pile_diameter ?? 'N/A' }} <span class="unit">in</span></span>
                </p>
                <p>
                    <span style="display: inline-block; width: 200px;">Helix Configuration: </span>
                    <span style="display: inline-block;">{{ $helix_configuration ?? 'N/A' }}</span>
                </p>
                <p>
                    <span style="display: inline-block; width: 200px;">Torque Correlation Factor: </span>
                    <span style="display: inline-block;">{{ $torque_factor ?? 'N/A' }} <span class="unit">lbs/ft-lbs</span></span>
                </p>
            </div>
            <div class="section">
                <div class="bold red">Notes</div>
                <p>{{ $field_notes ?? 'No field notes recorded' }} </p>
            </div>
        </div>
       
        <div class="half">
            <div class="section-title">Geometric Data</div>
            <table>
                <tr>
                    <th></th>
                    @for($i = 1; $i <= 5; $i++)
                        <th>{{ $i }}</th>
                    @endfor
                </tr>
                <tr>
                    <td><strong>X</strong></td>
                    @for($i = 0; $i < 5; $i++)
                        <td>{{ ($x_values[$i] ?? 0) }}</td>
                    @endfor
                </tr>
                <tr>
                    <td><strong>Y</strong></td>
                    @for($i = 0; $i < 5; $i++)
                        <td>{{ ($y_values[$i] ?? 0) }}</td>
                    @endfor
                </tr>
            </table>
            <div style="margin-top: 3mm;">
                <p><span>Indication Angle: {{ $indication_angle ?? 'N/A' }}°</span></p>
                <p><span>Pile Head Position: {{ $pile_head_position ?? 'N/A' }}</span></p>
            </div>
        </div>
        
        <div class="clearfix"></div>
    </div>
    <div class="section">
        <div class="section-title">Soil Information</div>
        <div class="half">
            <p>
                <span style="display: inline-block; width: 150px;">Soil Report Number:</span>
                <span style="display: inline-block;">{{ $soil_report_number ?? 'N/A' }}</span>
            </p>
            <p>
                <span style="display: inline-block; width: 150px;">Soil Report Date:</span>
                <span style="display: inline-block;">{{ $soil_report_date ?? 'N/A' }}</span>
            </p>
            <p>
                <span style="display: inline-block; width: 150px;">Boring Number:</span>
                <span style="display: inline-block;">{{ $boring_number ?? 'N/A' }}</span>
            </p>
            <p>
                <span style="display: inline-block; width: 150px;">Boring Log Date:</span>
                <span style="display: inline-block;">{{ $boring_log_date ?? 'N/A' }}</span>
            </p>
        </div>
        <div class="half">
            <p>
                <span style="display: inline-block; width: 150px;">Water Table Depth:</span>
                <span style="display: inline-block;">{{ $water_table_depth ?? 'N/A' }} <span class="unit">ft</span> </span>
            </p>
            <p>
                <span style="display: inline-block; width: 150px;">Maximum Depth:</span>
                <span style="display: inline-block;">{{ $max_depth ?? 'N/A' }} <span class="unit">ft</span></span>
            </p>
            <p>
                <span style="display: inline-block; width: 150px;">Upper Soil Type:</span>
                <span style="display: inline-block;">{{ $upper_soil_type ?? 'N/A' }}</span>
            </p>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="section">
        <div class="section-title">Soil Profile</div>
        <table>
            <thead>
                <tr>
                    <th>Depth (ft)</th>
                    <th>SPT Blow Count</th>
                    <th>Layer</th>
                    <th>Cohesion (psf)</th>
                    <th>Adhesion Coefficient</th>
                    <th>Friction Angle (°)</th>
                    <th>Friction Co-efficient</th>
                    <th>Moist Unit Weight(pcf)</th>
                    <th>Sat Unit Weight(pcf)</th>
                    <th>Nc</th>
                    <th>Nq</th>
                </tr>
            </thead>
            <tbody>
                @foreach($soil_profile as $profile)
                    <tr>
                        <td>{{ $profile['start_depth'] ?? 'N/A' }}</td>
                        <td>{{ $profile['blow_count'] ?? 'N/A' }}</td>
                        <td>{{ $profile['soil_layer_type_name'] ?? 'N/A' }}</td>
                        <td>{{ $profile['cohesion'] ?? 'N/A' }}</td>
                        <td>{{ $profile['coefficient_of_adhesion'] ?? 'N/A' }}</td>
                        <td>{{ $profile['angle_of_internal_friction'] ?? 'N/A' }}</td>
                        <td>{{ $profile['coefficient_of_external_friction'] ?? 'N/A' }}</td>
                        <td>{{ $profile['moist_unit_weight'] ?? 'N/A' }}</td>
                        <td>{{ $profile['saturated_unit_weight'] ?? 'N/A' }}</td>
                        <td>{{ $profile['nc'] ?? 'N/A' }}</td>
                        <td>{{ $profile['nq'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Page 3 - Calculation Results -->
    <div class="header">
        <span>
        <img src="{{ 'file://' . public_path('images/ramjack_pdf_logo_white.png') }}" width="240px" alt="RAMJACK FOUNDATION SOLUTIONS™">
        </span>
    </div>

    <div class="half" style="line-height: 1.2">
        <div class="section-title">Design Parameters</div>
        <p>
            <span style="display: inline-block; width: 180px;">Required Allowable Capacity: </span>
            <span style="display: inline-block;">{{ $required_allowable_capacity ?? 'N/A' }} <span class="unit">kip</span></span>
        </p>
        <p>
            <span style="display: inline-block; width: 180px;">Applied Factor of Safety: </span>
            <span style="display: inline-block;">{{ $safety_factor ?? 'N/A' }}</span>
        </p>
        
        <div class="section" style="border: 2px solid gray; padding: 16px; width: 260px;">
            <div class="bold red">Pile Capacity Theory</div>
            <div class="bold" style="margin-top: 3mm;">End Bearing Capacity</div>
            <div class="formula">
                q<sub>u</sub> = cN<sub>c</sub> + qN<sub>q</sub>
            </div>
            <div style="margin-left: 5mm;">
                <p><span class="formula-var">q<sub>u</sub></span> = Ultimate End Bearing Capacity (psf)</p>
                <p><span class="formula-var">c</span> = Cohesion (psf)</p>
                <p><span class="formula-var">N<sub>c</sub>, N<sub>q</sub></span> = Bearing Capacity Factors</p>
                <p><span class="formula-var">q</span> = Effective Vertical Stress (psf)</p>
            </div>
            
            <div class="bold" style="margin-top: 3mm;">Skin Friction Capacity</div>
            <div class="formula">
                f<sub>s</sub> = ac + Kσ<sub>0</sub>tanδ
            </div>
            <div style="margin-left: 5mm;">
                <p><span class="formula-var">f<sub>s</sub></span> = Ultimate skin friction capacity</p>
                <p><span class="formula-var">a</span> = Adhesion Factor</p>
                <p><span class="formula-var">K</span> = Coefficient of lateral earth pressure</p>
                <p><span class="formula-var">σ<sub>0</sub></span> = Effective Vertical Stress (psf)</p>
                <p><span class="formula-var">δ</span> = Angle of External Friction = 0.54 (Ø)</p>
            </div>
        </div>
        <div class="section-title">Estimated Pile Capacity</div>
        <p>
            <span style="display: inline-block; width: 200px;">Allowable Frictional Resistance: </span>
            <span style="display: inline-block;">{{ $allowable_frictional_resistance ?? 'N/A' }} <span class="unit">kip</span></span>
        </p>
        <p>
            <span style="display: inline-block; width: 200px;">Allowable End Bearing Capacity: </span>
            <span style="display: inline-block;">{{ $allowable_end_bearing ?? 'N/A' }} <span class="unit">kip</span></span>
        </p>
        <p>
            <span style="display: inline-block; width: 200px;">Total Allowable Pile Capacity: </span>
            <span style="display: inline-block;">{{ $allowable_pile_capacity ?? 'N/A' }} <span class="unit">kip</span></span>
        </p>
        <p>
            <span style="display: inline-block; width: 200px;">Approximate Embedment Depth: </span>
            <span style="display: inline-block;">{{ $approx_embedment_depth ?? 'N/A' }} <span class="unit">ft</span></span>
        </p>
        <p>
            <span style="display: inline-block; width: 200px;">Required Installation Torque: </span>
            <span style="display: inline-block;">{{ $required_installation_torque ?? 'N/A' }} <span class="unit">ft-lbs</span></span>
        </p>
        
        <div class="note">
            <p><strong>Note 1:</strong> The reported embedment depth is an estimate and may vary based on field conditions.</p>
            <p><strong>Note 2:</strong> Piles must be installed to the specified torque unless approved by a licensed engineer.</p>
        </div>
    </div>

    <div class="half">
        <div class="section-title">{{ $calculation_type }} Results</div>
        <table style="font-size: 8px;">
            <thead>
                <tr>
                    <th style="padding: 3px;">Depth (ft)</th>
                    <th style="padding: 3px;">Ultimate Capacity (lbs)</th>
                    <th style="padding: 3px;">Total Resistance (lb-ft)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($depth_results as $result)
                    <tr style="page-break-inside: avoid;">
                        <td style="padding: 3px;">{{ $result['depth'] ?? 'N/A' }}</td>
                        <td style="padding: 3px;">{{ number_format($result['capacity'] ?? 0, 0) }}</td>
                        <td style="padding: 3px;">{{ number_format($result['resistance'] ?? 0, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="warning" style="margin-top: 5mm; padding: 2mm; background-color: #ffeeee; border-left: 3px solid #cc0000;">
            <strong>Warning:</strong> Torsional resistance values in red indicate calculated resistance exceeds Ram Jack rating for the selected shaft configuration.
        </div>
    </div>
    <div class="clearfix"></div>
    <div>
        @if (!empty($chart_image) && $chart_image !== 'N/A')
            <img src="{{ $chart_image }}" alt="Anchor Result Chart" width='100%'/>
        @else
            <p></p>
        @endif
    </div>
</body>
</html>