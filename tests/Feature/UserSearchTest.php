<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('users index filters by name or email search', function () {
    $this->actingAs(User::factory()->create());

    $matching = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
    User::factory()->create(['name' => 'Jane Roe', 'email' => 'jane@example.com']);

    $this->get('/users?search=john')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('custom/users/Index')
            ->has('users.data', 1)
            ->where('users.data.0.id', $matching->id)
            ->where('filters.search', 'john'));
});
