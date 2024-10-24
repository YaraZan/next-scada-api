<?php

use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RootUserSeeder;
use Database\Seeders\RootUserWorkspacesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('User can get all his workspaces', function () {
    // Run all seeders
    $workspace = Workspace::factory()->create();

    $tokenResponse = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/users/login', [
        'email' => $workspace->owner->email,
        'password' => 'password',
        'device_name' => 'default'
    ]);

    $tokenResponse->assertStatus(200);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $tokenResponse->getContent(),
    ])->get('/api/workspaces');

    $response->assertStatus(200);

    $this->assertCount(1, (array) $response->getContent());
});

test('User can get one his workspace', function () {
    $workspace = Workspace::factory()->create();

    $tokenResponse = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/users/login', [
        'email' => $workspace->owner->email,
        'password' => 'password',
        'device_name' => 'default'
    ]);

    $tokenResponse->assertStatus(200);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $tokenResponse->getContent(),
    ])->get('/api/workspaces/' . $workspace->uuid);

    $response->assertStatus(200);

    $this->assertNotNull($response->getContent());
});

test('User can share workspace with other user', function () {
    $workspace = Workspace::factory()->create();
    $user = User::factory()->withRole('user')->create();

    $tokenResponse = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/users/login', [
        'email' => $workspace->owner->email,
        'password' => 'password',
        'device_name' => 'default'
    ]);

    $tokenResponse->assertStatus(200);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $tokenResponse->getContent(),
    ])->post('/api/workspaces/share', [
        'workspace' => $workspace->uuid,
        'user' => $user->uuid,
    ]);

    print_r($response->getContent());

    $response->assertStatus(200);

    $workspace = Workspace::findByUuid($workspace->uuid)->load(['users']);

    $this->assertNotEmpty($workspace->users);
});

test('User can unshare workspace with other user', function () {
    $workspace = Workspace::factory()->create();
    $user = User::factory()->withRole('user')->create();

    $tokenResponse = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/users/login', [
        'email' => $workspace->owner->email,
        'password' => 'password',
        'device_name' => 'default'
    ]);

    $tokenResponse->assertStatus(200);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $tokenResponse->getContent(),
    ])->post('/api/workspaces/share', [
        'workspace' => $workspace->uuid,
        'user' => $user->uuid,
    ]);

    $response->assertStatus(200);

    $workspace = Workspace::findByUuid($workspace->uuid)->load(['users']);

    $this->assertNotEmpty($workspace->users);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $tokenResponse->getContent(),
    ])->post('/api/workspaces/unshare', [
        'workspace' => $workspace->uuid,
        'user' => $user->uuid,
    ]);

    $workspace->load('users');

    $this->assertEmpty($workspace->users);
});

test('User can get one workspace, if he is invited', function () {
    $workspace = Workspace::factory()->create();
    $user = User::factory()->withRole('user')->create();

    // Authorize workspace owner
    $tokenResponse = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/users/login', [
        'email' => $workspace->owner->email,
        'password' => 'password',
        'device_name' => 'default'
    ]);
    $tokenResponse->assertStatus(200);

    // Share workspace with other user
    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $tokenResponse->getContent(),
    ])->post('/api/workspaces/share', [
        'workspace' => $workspace->uuid,
        'user' => $user->uuid,
    ]);
    $response->assertStatus(200);

    // Authorize other user
    $tokenResponse = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/users/login', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'default'
    ]);

    // Try to view shared workspace from other user
    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $tokenResponse->getContent(),
    ])->get('/api/workspaces/' . $workspace->uuid);
    $response->assertStatus(200);

    $this->assertNotNull($response->getContent());
});

test('User can get all workspaces where he is invited', function () {
    $workspaceOne = Workspace::factory()->create();
    $workspaceTwo = Workspace::factory()->create();
    $user = User::factory()->withRole('user')->create();

    // Authorize workspace one owner
    $tokenResponse = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/users/login', [
        'email' => $workspaceOne->owner->email,
        'password' => 'password',
        'device_name' => 'default'
    ]);
    $tokenResponse->assertStatus(200);

    // Share workspace one with other user
    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $tokenResponse->getContent(),
    ])->post('/api/workspaces/share', [
        'workspace' => $workspaceOne->uuid,
        'user' => $user->uuid,
    ]);
    $response->assertStatus(200);

    // Authorize workspace two owner
    $tokenResponse = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/users/login', [
        'email' => $workspaceTwo->owner->email,
        'password' => 'password',
        'device_name' => 'default'
    ]);
    $tokenResponse->assertStatus(200);

    // Share workspace two with other user
    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $tokenResponse->getContent(),
    ])->post('/api/workspaces/share', [
        'workspace' => $workspaceTwo->uuid,
        'user' => $user->uuid,
    ]);
    $response->assertStatus(200);

    // Authorize other user
    $tokenResponse = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('/api/users/login', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'default'
    ]);

    // Try to view all shared workspaces from other user
    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $tokenResponse->getContent(),
    ])->get('/api/workspaces/shared');
    $response->assertStatus(200);

    $this->assertCount(2, $response->getContent());
});
