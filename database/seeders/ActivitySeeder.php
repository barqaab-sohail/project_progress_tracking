<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    public function run()
    {
        $activities = [
            // Foundation Works
            ['name' => 'Site Clearing & Excavation', 'category' => 'Foundation Works', 'description' => 'Clearing and excavation of the construction site'],
            ['name' => 'Footing Reinforcement', 'category' => 'Foundation Works', 'description' => 'Installation of footing reinforcement'],
            ['name' => 'Footing Concrete Pouring', 'category' => 'Foundation Works', 'description' => 'Pouring concrete for footings'],
            ['name' => 'Column Starter Bars Installation', 'category' => 'Foundation Works', 'description' => 'Installation of column starter bars'],
            ['name' => 'Backfilling & Compaction', 'category' => 'Foundation Works', 'description' => 'Backfilling and compaction work'],
            ['name' => 'Damp Proof Course (DPC)', 'category' => 'Foundation Works', 'description' => 'Installation of damp proof course'],

            // Ground Floor Structure
            ['name' => 'Ground Floor Column Reinforcement', 'category' => 'Structure', 'description' => 'Reinforcement work for ground floor columns'],
            ['name' => 'Ground Floor Column Formwork', 'category' => 'Structure', 'description' => 'Formwork for ground floor columns'],
            ['name' => 'Ground Floor Column Concrete Pouring', 'category' => 'Structure', 'description' => 'Concrete pouring for ground floor columns'],
            ['name' => 'Ground Floor Beam Reinforcement', 'category' => 'Structure', 'description' => 'Reinforcement work for ground floor beams'],
            ['name' => 'Ground Floor Beam Formwork', 'category' => 'Structure', 'description' => 'Formwork for ground floor beams'],
            ['name' => 'Ground Floor Beam Concrete Pouring', 'category' => 'Structure', 'description' => 'Concrete pouring for ground floor beams'],
            ['name' => 'Ground Floor Slab Reinforcement', 'category' => 'Structure', 'description' => 'Reinforcement work for ground floor slab'],
            ['name' => 'Ground Floor Slab Concrete Pouring', 'category' => 'Structure', 'description' => 'Concrete pouring for ground floor slab'],

            // First Floor Structure
            ['name' => 'First Floor Column Reinforcement', 'category' => 'Structure', 'description' => 'Reinforcement work for first floor columns'],
            ['name' => 'First Floor Column Formwork', 'category' => 'Structure', 'description' => 'Formwork for first floor columns'],
            ['name' => 'First Floor Column Concrete Pouring', 'category' => 'Structure', 'description' => 'Concrete pouring for first floor columns'],
            ['name' => 'First Floor Beam Reinforcement', 'category' => 'Structure', 'description' => 'Reinforcement work for first floor beams'],
            ['name' => 'First Floor Beam Formwork', 'category' => 'Structure', 'description' => 'Formwork for first floor beams'],
            ['name' => 'First Floor Beam Concrete Pouring', 'category' => 'Structure', 'description' => 'Concrete pouring for first floor beams'],
            ['name' => 'First Floor Slab Reinforcement', 'category' => 'Structure', 'description' => 'Reinforcement work for first floor slab'],
            ['name' => 'First Floor Slab Concrete Pouring', 'category' => 'Structure', 'description' => 'Concrete pouring for first floor slab'],

            // Masonry Works
            ['name' => 'Ground Floor Brickwork', 'category' => 'Masonry', 'description' => 'Brickwork for ground floor'],
            ['name' => 'First Floor Brickwork', 'category' => 'Masonry', 'description' => 'Brickwork for first floor'],
            ['name' => 'Lintel Installation', 'category' => 'Masonry', 'description' => 'Installation of lintels'],
            ['name' => 'Chajja/Projections Construction', 'category' => 'Masonry', 'description' => 'Construction of chajjas/projections'],

            // Roofing
            ['name' => 'Roof Beam Construction', 'category' => 'Roofing', 'description' => 'Construction of roof beams'],
            ['name' => 'Roof Slab Construction', 'category' => 'Roofing', 'description' => 'Construction of roof slab'],
            ['name' => 'Waterproofing', 'category' => 'Roofing', 'description' => 'Waterproofing work'],

            // Finishing Works
            ['name' => 'Plastering', 'category' => 'Finishing', 'description' => 'Plastering work'],
            ['name' => 'Plastering (First Floor)', 'category' => 'Finishing', 'description' => 'Plastering work for first floor'],
            ['name' => 'Flooring (Ground Floor)', 'category' => 'Finishing', 'description' => 'Flooring work for ground floor'],
            ['name' => 'Flooring (First Floor)', 'category' => 'Finishing', 'description' => 'Flooring work for first floor'],
            ['name' => 'Painting (Internal)', 'category' => 'Finishing', 'description' => 'Internal painting work'],
            ['name' => 'Painting (External)', 'category' => 'Finishing', 'description' => 'External painting work'],
            ['name' => 'False Ceiling', 'category' => 'Finishing', 'description' => 'False ceiling installation'],

            // MEP Works
            ['name' => 'Electrical Conduiting', 'category' => 'MEP', 'description' => 'Electrical conduiting work'],
            ['name' => 'Electrical Wiring & Fittings', 'category' => 'MEP', 'description' => 'Electrical wiring and fittings'],
            ['name' => 'Plumbing Pipe Installation', 'category' => 'MEP', 'description' => 'Installation of plumbing pipes'],
            ['name' => 'Sanitary Fixtures Installation', 'category' => 'MEP', 'description' => 'Installation of sanitary fixtures'],
            ['name' => 'HVAC Installation', 'category' => 'MEP', 'description' => 'HVAC system installation'],
            ['name' => 'Fire Fighting System', 'category' => 'MEP', 'description' => 'Fire fighting system installation'],

            // External Works
            ['name' => 'Compound Wall Construction', 'category' => 'External', 'description' => 'Construction of compound wall'],
            ['name' => 'Gate Installation', 'category' => 'External', 'description' => 'Installation of gates'],
            ['name' => 'Landscaping', 'category' => 'External', 'description' => 'Landscaping work'],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }
    }
}
