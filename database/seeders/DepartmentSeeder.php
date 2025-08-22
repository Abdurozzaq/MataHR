<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        Department::insert([
            [
                'name' => 'HRD',
                'description' => 'Human Resource Department',
            ],
            [
                'name' => 'IT',
                'description' => 'Information Technology',
            ],
            [
                'name' => 'Finance',
                'description' => 'Finance and Accounting',
            ],
            [
                'name' => 'Marketing',
                'description' => 'Marketing and Promotion',
            ],
            [
                'name' => 'Sales',
                'description' => 'Sales Department',
            ],
            [
                'name' => 'Production',
                'description' => 'Production and Manufacturing',
            ],
            [
                'name' => 'Logistics',
                'description' => 'Logistics and Supply Chain',
            ],
            [
                'name' => 'Procurement',
                'description' => 'Procurement and Purchasing',
            ],
            [
                'name' => 'R&D',
                'description' => 'Research and Development',
            ],
            [
                'name' => 'Customer Service',
                'description' => 'Customer Service and Support',
            ],
            [
                'name' => 'General Affairs',
                'description' => 'General Affairs',
            ],
            [
                'name' => 'Legal',
                'description' => 'Legal and Compliance',
            ],
            [
                'name' => 'Quality Assurance',
                'description' => 'Quality Assurance and Control',
            ],
            [
                'name' => 'Public Relations',
                'description' => 'Public Relations',
            ],
            [
                'name' => 'Maintenance',
                'description' => 'Maintenance and Facility',
            ],
            [
                'name' => 'Security',
                'description' => 'Security Department',
            ],
            [
                'name' => 'Warehouse',
                'description' => 'Warehouse and Inventory',
            ],
            [
                'name' => 'Design',
                'description' => 'Design and Creative',
            ],
            [
                'name' => 'Export Import',
                'description' => 'Export and Import',
            ],
            [
                'name' => 'Health and Safety',
                'description' => 'Health and Safety',
            ],
        ]);
    }
}
