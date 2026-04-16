<?php

use App\Models\User;

test('faculty notifications page renders the sidebar notifications link', function () {
    $faculty = User::factory()->faculty()->create();

    $response = $this
        ->actingAs($faculty)
        ->get(route('faculty.notifications'));

    $response->assertSuccessful();
    $response->assertSee('Notifications');
    $response->assertSee('Enable browser alerts');
    $response->assertSee('Allow notifications');
    $response->assertSee('autoRequestPermission', false);
    $response->assertSee(route('faculty.notifications'), false);
});
