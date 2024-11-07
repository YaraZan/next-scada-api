<?php

use App\Models\MemberRole;
use App\Models\User;
use App\Models\Workspace;
use App\ProtocolEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Workspace owner can view list of member roles', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    MemberRole::factory(3)->withWorkspace($workspace)->create();

    $response = $this->actingAs($owner, 'sanctum')
        ->getJson('/api/memberRoles/fromWorkspace/' . $workspace->uuid)
        ->assertStatus(200);

    $this->assertCount(3, $response->json());
});

test('Workspace owner can view member role', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    $memberRole = MemberRole::factory()->withWorkspace($workspace)->create();

    $response = $this->actingAs($owner, 'sanctum')
        ->getJson('/api/memberRoles/' . $memberRole->uuid)
        ->assertStatus(200);

    $this->assertNotNull($response->json());
});

test('Workspace member can view list of member roles', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $member = User::factory()->withRole('user')->create();

    $workspace = Workspace::factory()->withOwner($owner)->create();
    MemberRole::factory(3)->withWorkspace($workspace)->create();

    $this->actingAs($owner, 'sanctum')
        ->postJson('/api/workspaces/share', [
            'workspace' => $workspace->uuid,
            'user' => $member->uuid
        ])
        ->assertStatus(200);

    $response = $this->actingAs($member, 'sanctum')
        ->getJson('/api/memberRoles/fromWorkspace/' . $workspace->uuid)
        ->assertStatus(200);

    $this->assertCount(3, $response->json());
});

test('Workspace member can view member role', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $member = User::factory()->withRole('user')->create();

    $workspace = Workspace::factory()->withOwner($owner)->create();
    $memberRole = MemberRole::factory()->withWorkspace($workspace)->create();

    $this->actingAs($owner, 'sanctum')
        ->postJson('/api/workspaces/share', [
            'workspace' => $workspace->uuid,
            'user' => $member->uuid
        ])
        ->assertStatus(200);

    $response = $this->actingAs($member, 'sanctum')
        ->getJson('/api/memberRoles/' . $memberRole->uuid)
        ->assertStatus(200);

    $this->assertNotNull($response->json());
});

test('Workspace owner can create member role', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    $memberRole = MemberRole::factory()->make();

    $response = $this->actingAs($owner, 'sanctum')
        ->getJson('/api/memberRoles/' . $memberRole->uuid)
        ->assertStatus(200);

    $this->assertNotNull($response->json());
});
