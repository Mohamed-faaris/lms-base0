<?php

namespace Database\Seeders;

use App\Enums\College;
use App\Enums\Department;
use App\Models\ManagerScope;
use App\Models\User;
use Illuminate\Database\Seeder;

class KRCTManagerScopeSeeder extends Seeder
{
    public function run(): void
    {
        $college = College::KRCT;

        $principal = User::query()
            ->where('email', 'principal@krct.ac.in')
            ->firstOrFail();

        ManagerScope::firstOrCreate([
            'manager_user_id' => $principal->id,
            'college' => $college,
            'department' => null,
        ]);

        $hods = [
            'hodcse@krct.ac.in' => Department::CSE,
            'hodece@krct.ac.in' => Department::ECE,
            'hodeee@krct.ac.in' => Department::EEE,
            'hodmech@krct.ac.in' => Department::MECH,
            'hodcivil@krct.ac.in' => Department::CIVIL,
            'hodit@krct.ac.in' => Department::IT,
            'hodai@krct.ac.in' => Department::AI,
            'hodsh@krct.ac.in' => Department::SH,
        ];

        foreach ($hods as $email => $department) {
            $hod = User::query()
                ->where('email', $email)
                ->firstOrFail();

            ManagerScope::firstOrCreate([
                'manager_user_id' => $hod->id,
                'college' => $college,
                'department' => $department,
            ]);
        }
    }
}
