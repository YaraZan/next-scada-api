<?php

use App\Models\MemberRole;
use App\Models\Schema;
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
            'user' => $member->uuid,
            'member_role' => 'guest'
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
            'user' => $member->uuid,
            'member_role' => 'guest'
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
    $schemas = Schema::factory(3)->withWorkspace($workspace)->create()->toArray();

    $schemaUuids = array_map(fn($schema) => $schema['uuid'], $schemas);

    $memberRole = MemberRole::factory()->make()->toArray();
    $memberRole['workspace'] = $workspace->uuid;
    $memberRole['schemas'] = $schemaUuids;

    $this->actingAs($owner, 'sanctum')
        ->postJson('/api/memberRoles/', $memberRole)
        ->assertStatus(200);

    $response = $this->actingAs($owner, 'sanctum')
        ->getJson('/api/memberRoles/fromWorkspace/' . $workspace->uuid)
        ->assertStatus(200);

    $this->assertCount(1, $response->json());
});

test('Workspace owner can update member role', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    $schemas = Schema::factory(3)->withWorkspace($workspace)->create();

    $memberRole = MemberRole::factory()->withWorkspace($workspace)->withSchemas($schemas)->create();

    $fakeName = fake()->word;

    $this->actingAs($owner, 'sanctum')
        ->putJson('/api/memberRoles/' . $memberRole->uuid, [
            'name' => $fakeName,
        ])
        ->assertStatus(200);

    $response = $this->actingAs($owner, 'sanctum')
        ->getJson('/api/memberRoles/' . $memberRole->uuid)
        ->assertStatus(200);

    $this->assertTrue($memberRole->name !== $response['name']);
    $this->assertTrue($fakeName === $response['name']);
});

test('Workspace owner can delete member role', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    $schemas = Schema::factory(3)->withWorkspace($workspace)->create();

    $memberRole = MemberRole::factory()->withWorkspace($workspace)->withSchemas($schemas)->create();

    $workspace->refresh();

    $this->assertCount(1, $workspace->memberRoles);

    $this->actingAs($owner, 'sanctum')
        ->deleteJson('/api/memberRoles/' . $memberRole->uuid)
        ->assertStatus(200);

    $workspace->refresh();

    $this->assertEmpty($workspace->memberRoles);
});

test('Workspace owner can attach schema to member role', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    $schemas = Schema::factory(3)->withWorkspace($workspace)->create();
    $memberRole = MemberRole::factory()->withWorkspace($workspace)->withSchemas($schemas)->create();

    $memberRole->refresh();
    $this->assertCount(3, $memberRole->schemas);

    $newSchema = Schema::factory()->withWorkspace($workspace)->create();

    $this->actingAs($owner, 'sanctum')
        ->postJson('/api/memberRoles/attachSchema', [
            'member_role' => $memberRole->uuid,
            'schemas' => [$newSchema->uuid],
        ])
        ->assertStatus(200);

    $memberRole->refresh();

    $this->assertCount(4, $memberRole->schemas);
});

test('Workspace owner can detach schema from member role', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    $schema = Schema::factory()->withWorkspace($workspace)->create();
    $memberRole = MemberRole::factory()->withWorkspace($workspace)->create();

    $this->actingAs($owner, 'sanctum')
        ->postJson('/api/memberRoles/attachSchema', [
            'member_role' => $memberRole->uuid,
            'schemas' => [$schema->uuid],
        ])
        ->assertStatus(200);

    $memberRole->refresh();
    $this->assertCount(1, $memberRole->schemas);

    $this->actingAs($owner, 'sanctum')
    ->postJson('/api/memberRoles/detachSchema', [
        'member_role' => $memberRole->uuid,
        'schemas' => [$schema->uuid],
    ])
    ->assertStatus(200);

    $memberRole->refresh();
    $this->assertEmpty($memberRole->schemas);
});

test('Workspace owner can assign a role to user', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $participant = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    $schemas = Schema::factory(3)->withWorkspace($workspace)->create();
    $memberRole = MemberRole::factory()->withWorkspace($workspace)->withSchemas($schemas)->create();

    $this->actingAs($owner, 'sanctum')
        ->postJson('/api/workspaces/share', [
            'workspace' => $workspace->uuid,
            'user' => $participant->uuid,
            'member_role' => 'guest',
        ])
        ->assertStatus(200);

    $this->actingAs($owner, 'sanctum')
        ->postJson('/api/memberRoles/assignToUser', [
            'member_role' => $memberRole->uuid,
            'workspace' => $workspace->uuid,
            'user' => $participant->uuid
        ])
        ->assertStatus(200);

    $foundMemeberRole = $participant->memberRoleInWorkspace($workspace->id);

    $this->expect($foundMemeberRole->id)->toBe($memberRole->id);
});

test('Workspace owner can guestify user', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $participant = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    $schemas = Schema::factory(3)->withWorkspace($workspace)->create();
    $memberRole = MemberRole::factory()->withWorkspace($workspace)->withSchemas($schemas)->create();

    $this->actingAs($owner, 'sanctum')
        ->postJson('/api/workspaces/share', [
            'workspace' => $workspace->uuid,
            'user' => $participant->uuid,
            'member_role' => $memberRole->uuid,
        ])
        ->assertStatus(200);

    $this->actingAs($owner, 'sanctum')
        ->postJson('/api/memberRoles/assignToUser', [
            'member_role' => 'guest',
            'workspace' => $workspace->uuid,
            'user' => $participant->uuid
        ])
        ->assertStatus(200);

    $foundMemeberRole = $participant->memberRoleInWorkspace($workspace->id);

    $this->assertNull($foundMemeberRole);
});
