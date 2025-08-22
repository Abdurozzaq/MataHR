<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['name' => 'Staff', 'description' => 'Staff umum'],
            ['name' => 'Supervisor', 'description' => 'Supervisor'],
            ['name' => 'Manager', 'description' => 'Manager'],
            ['name' => 'Assistant Manager', 'description' => 'Asisten Manager'],
            ['name' => 'Head of Department', 'description' => 'Kepala Departemen'],
            ['name' => 'Director', 'description' => 'Direktur'],
            ['name' => 'Intern', 'description' => 'Magang'],
        ];
        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
