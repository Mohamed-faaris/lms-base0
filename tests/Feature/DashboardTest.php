<?php

use App\Enums\Role;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create([
        'role' => Role::Faculty,
    ]);
    $this->actingAs($user);

    $response = $this->get(route('faculty.dashboard'));
    $response->assertOk();
});
