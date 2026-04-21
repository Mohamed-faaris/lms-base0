<?php

use App\Enums\College;
use App\Enums\Department;
use App\Enums\Role;
use App\Models\ManagerScope;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('krct seeder assigns hods and principal as managers with scopes', function () {
    $this->seed(DatabaseSeeder::class);

    $principal = User::query()
        ->where('email', 'principal@krct.ac.in')
        ->firstOrFail();

    expect($principal->role)->toBe(Role::Manager);

    $principalScope = ManagerScope::query()
        ->where('manager_user_id', $principal->id)
        ->firstOrFail();

    expect($principalScope->college)->toBe(College::KRCT);
    expect($principalScope->department)->toBeNull();

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

        expect($hod->role)->toBe(Role::Manager);

        $scope = ManagerScope::query()
            ->where('manager_user_id', $hod->id)
            ->firstOrFail();

        expect($scope->college)->toBe(College::KRCT);
        expect($scope->department)->toBe($department);
    }
});
