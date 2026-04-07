<?php

use App\Livewire\Faculty\Certificates;
use App\Models\User;
use Livewire\Livewire;

test('faculty certificates modal renders without crashing', function () {
    $staff = User::factory()->staff()->create();

    Livewire::actingAs($staff)
        ->test(Certificates::class)
        ->call('viewCertificate', 1)
        ->assertSet('showCertificateModal', true)
        ->assertSee('Download Image (PNG)')
        ->assertSee('Download PDF Document');
});
