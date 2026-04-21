<?php

use App\Enums\College;
use App\Enums\Department;
use App\Livewire\Admin\Users\Profile as AdminUserProfile;
use App\Models\ManagerScope;
use App\Models\User;
use Livewire\Livewire;

test('manager is redirected to the manager dashboard', function () {
    $manager = User::factory()->manager()->create();

    $this->actingAs($manager)
        ->get(route('dashboard'))
        ->assertRedirect(route('manager.dashboard'));
});

test('manager sees only faculty inside assigned department scope', function () {
    $manager = User::factory()->manager()->create();
    $visibleFaculty = User::factory()->faculty()->create([
        'name' => 'Visible Faculty',
        'college' => College::KRCT,
        'department' => Department::CSE,
    ]);
    $hiddenFaculty = User::factory()->faculty()->create([
        'name' => 'Hidden Faculty',
        'college' => College::KRCT,
        'department' => Department::ECE,
    ]);

    ManagerScope::factory()->create([
        'manager_user_id' => $manager->id,
        'college' => College::KRCT,
        'department' => Department::CSE,
    ]);

    $this->actingAs($manager)
        ->get(route('manager.faculty.index'))
        ->assertSuccessful()
        ->assertSee('Visible Faculty')
        ->assertDontSee('Hidden Faculty');

    $this->actingAs($manager)
        ->get(route('manager.faculty.profile', $visibleFaculty))
        ->assertSuccessful()
        ->assertSee('Faculty Profile');

    $this->actingAs($manager)
        ->get(route('manager.faculty.profile', $hiddenFaculty))
        ->assertForbidden();
});

test('manager college scope includes every department in that college', function () {
    $manager = User::factory()->manager()->create();
    $cseFaculty = User::factory()->faculty()->create([
        'name' => 'CSE Faculty',
        'college' => College::KRCE,
        'department' => Department::CSE,
    ]);
    $eceFaculty = User::factory()->faculty()->create([
        'name' => 'ECE Faculty',
        'college' => College::KRCE,
        'department' => Department::ECE,
    ]);
    $otherCollegeFaculty = User::factory()->faculty()->create([
        'name' => 'Other College Faculty',
        'college' => College::MKCE,
        'department' => Department::ECE,
    ]);

    ManagerScope::factory()->collegeWide(College::KRCE)->create([
        'manager_user_id' => $manager->id,
    ]);

    $this->actingAs($manager)
        ->get(route('manager.faculty.index'))
        ->assertSuccessful()
        ->assertSee('CSE Faculty')
        ->assertSee('ECE Faculty')
        ->assertDontSee('Other College Faculty');

    expect($manager->canMonitorFaculty($cseFaculty))->toBeTrue();
    expect($manager->canMonitorFaculty($eceFaculty))->toBeTrue();
    expect($manager->canMonitorFaculty($otherCollegeFaculty))->toBeFalse();
});

test('manager without scopes sees empty state', function () {
    $manager = User::factory()->manager()->create();
    $faculty = User::factory()->faculty()->create();

    $this->actingAs($manager)
        ->get(route('manager.dashboard'))
        ->assertSuccessful()
        ->assertSee('No scopes assigned yet');

    $this->actingAs($manager)
        ->get(route('manager.faculty.index'))
        ->assertSuccessful()
        ->assertSee('No faculty users are available in your current scope.');

    $this->actingAs($manager)
        ->get(route('manager.faculty.profile', $faculty))
        ->assertForbidden();
});

test('non manager users cannot access manager pages', function () {
    $admin = User::factory()->admin()->create();
    $staff = User::factory()->staff()->create();
    $faculty = User::factory()->faculty()->create();

    $this->actingAs($admin)->get(route('manager.dashboard'))->assertForbidden();
    $this->actingAs($staff)->get(route('manager.dashboard'))->assertForbidden();
    $this->actingAs($faculty)->get(route('manager.dashboard'))->assertForbidden();
});

test('admin can assign and remove manager scopes from the user profile', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create();

    Livewire::actingAs($admin)
        ->test(AdminUserProfile::class, ['user' => $manager])
        ->set('scopeCollege', College::KRCT->value)
        ->set('scopeDepartment', Department::CSE->value)
        ->call('addManagerScope')
        ->assertHasNoErrors();

    $scope = $manager->fresh()->managerScopes()->first();

    expect($scope)->not->toBeNull();
    expect($scope->college)->toBe(College::KRCT);
    expect($scope->department)->toBe(Department::CSE);

    Livewire::actingAs($admin)
        ->test(AdminUserProfile::class, ['user' => $manager->fresh()])
        ->call('removeManagerScope', $scope->id);

    expect($manager->fresh()->managerScopes()->count())->toBe(0);
});
