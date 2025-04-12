<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Department::create([
            'name' => 'ادارة تصميم مشاريع الشبكات والمنشآت المائية',
        ]);

        User::factory()->create([
            'name' => 'زينب مهدي عبد الله القلاف',
            'email' => 'zineb.elgendy@gmail.com',
            'role' => 'supervisor',
            'cid' => '000000000000',
            'file_number' => '0000000000',
            'replacement_id' => null,
            'department_id' => 1,
        ]);
        User::factory()->create([
            'name' => 'حازم محمد اسماعيل',
            'email' => 'hazem.elgendy@gmail.com',
            'role' => 'admin',
            'cid' => '282102800373',
            'file_number' => '1234567890',
            'replacement_id' => null,
            'department_id' => 1,
            'supervisor_id' => 1,
        ]);
    }
}
