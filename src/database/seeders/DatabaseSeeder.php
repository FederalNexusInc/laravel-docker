<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Helix;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Anchor;
use App\Models\Project;
use App\Models\SoilLayer;
use App\Models\SoilProfile;
use App\Models\SoilLayerType;
use Illuminate\Database\Seeder;
use App\Models\ProjectSpecialist;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'user@email.com',
            'password' => Hash::make('password'),
            'disclaimer_acknowledged' => false,
            'company_name' => 'Federal Nexus Inc',
        ]);

        Project::factory()->create([
            'project_name' => 'Test 1',
            'project_number' => '1',
            'run_id' => '1',
            'soil_reporter' => 'Tyler Test',
            'soil_report_number' => 1,
            'soil_report_date' => '2025-03-11',
            'pile_type' => 'guy_anchor',
            'boring_number' => 1,
            'boring_log_date' => '2025-03-11',
            'termination_depth' => 30,
            'project_address' => '123 Fake St',
            'project_city' => 'Placerville',
            'project_state' => 'AL',
            'project_zip_code' => '55555',
            'remarks' => 'Test',
            'created_by' => 1,
        ]);

        SoilLayerType::factory()->create([
            'name' => 'Sand',
        ]);

        SoilLayerType::factory()->create([
            'name' => 'Clay',
        ]);

        SoilLayerType::factory()->create([
            'name' => 'Peat',
        ]);

        ProjectSpecialist::factory()->create([
            'project_id' => 1,
            'name' => 'Tyler S',
            'specialist_email' => 'email@email.com',
            'company_name' => 'Test Company',
            'address' => '123 Street Rd',
            'city' => 'Placeville',
            'state' => 'AL',
            'zip' => '88888',
            'remarks' => 'test s',
        ]);

        SoilProfile::factory()->create([
            'project_id' => 1,
            'maximum_depth' => 30.0,
            'water_table_depth' => 10.0,
            'soil_type' => 'non-cohesive',
        ]);

        SoilLayer::factory()->create([
                'soil_profile_id' => 1,
                'start_depth' => 0.0,
                'blow_count' => 5.0,
                'soil_layer_type_id' => 1,
                'cohesion' => 0.0,
                'coefficient_of_adhesion' => 0.0,
                'angle_of_internal_friction' => 28.0,
                'coefficient_of_external_friction' => 0.27,
                'moist_unit_weight' => 90.0,
                'saturated_unit_weight' => 100.0,
                'nc' => 0.0,
                'nq' => 15.2,
        ]);

        SoilLayer::factory()->create([
                'soil_profile_id' => 1,
                'start_depth' => 10.0,
                'blow_count' => 5.0,
                'soil_layer_type_id' => 2,
                'cohesion' => 500.0,
                'coefficient_of_adhesion' => 0.9,
                'angle_of_internal_friction' => 0.0,
                'coefficient_of_external_friction' => 0.0,
                'moist_unit_weight' => 100.0,
                'saturated_unit_weight' => 110.0,
                'nc' => 9.0,
                'nq' => 1.0,
        ]);

        SoilLayer::factory()->create([
                'soil_profile_id' => 1,
                'start_depth' => 20.0,
                'blow_count' => 15.0,
                'soil_layer_type_id' => 1,
                'cohesion' => 0.0,
                'coefficient_of_adhesion' => 0.0,
                'angle_of_internal_friction' => 32.0,
                'coefficient_of_external_friction' => 0.0,
                'moist_unit_weight' => 100.0,
                'saturated_unit_weight' => 110.0,
                'nc' => 0.0,
                'nq' => 24.0,
        ]);    

        Anchor::factory()->create([
            'project_id' => 1,
            'lead_shaft_od' => '2 7/8',
            'lead_shaft_length' => 10.0,
            'extension_shaft_od' => '2 7/8',
            'wall_thickness' => 0.217,
            'yield_strength' => 65.0,
            'tensile_strength' => 80.0,
            'empirical_torque_factor' => 9.0,
            'required_allowable_capacity' => 20.0,
            'anchor_type' => 2,
            'required_safety_factor' => 2.0,
            'anchor_declination_degree' => 30.0,
            'pile_head_position' => 5.0,
            'x1' => 0.0,
            'y1' => 5.0,
            'x2' => 0.0,
            'y2' => 0.0,
            'x3' => 0.0,
            'y3' => 0.0,
            'x4' => 0.0,
            'y4' => 0.0,
            'x5' => 0.0,
            'y5' => 0.0,
            'omit_shaft_resistance' => 0,
            'omit_helix_mechanical_strength_check' => 0,
            'omit_shaft_mechanical_strength_check' => 0,
            'field_notes' => null, // Assuming no field notes
        ]);

        Helix::factory()->create([
            'anchor_id' => 1,
            'description' => 'Size 8 Thickness 3/8',
            'size' => 8.0,
            'thickness' => 0.375,
            'rating' => 54000.0,
            'helix_count' => 1,
        ]);

        Helix::factory()->create([
            'anchor_id' => 1,
            'description' => 'Size 10 Thickness 3/8',
            'size' => 10.0,
            'thickness' => 0.375,
            'rating' => 45500.0,
            'helix_count' => 1,
        ]);

        Helix::factory()->create([
            'anchor_id' => 1,
            'description' => 'Size 12 Thickness 1/2',
            'size' => 12.0,
            'thickness' => 0.5,
            'rating' => 70500.0,
            'helix_count' => 1,
        ]);
    }
}
