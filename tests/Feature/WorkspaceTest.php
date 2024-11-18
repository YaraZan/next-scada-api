<?php

use App\Models\User;
use App\Models\Workspace;
use App\ProtocolEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('User can get all his workspaces', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    Workspace::factory(3)->withOwner($owner)->create();

    // Authenticate and get workspaces
    $response = $this->actingAs($owner, 'sanctum')
        ->getJson('/api/workspaces');

    $response->assertStatus(200);
    $this->assertCount(3, $response->json());
});

test('User can get one of his workspaces', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();

    // Authenticate and get workspace
    $response = $this->actingAs($owner, 'sanctum')
        ->getJson('/api/workspaces/' . $workspace->uuid);

    $response->assertStatus(200);
    $this->assertNotNull($response->getContent());
});

test('User can share workspace with another user', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    $user = User::factory()->withRole('user')->create();

    // Authenticate owner and share workspace
    $this->actingAs($owner, 'sanctum')
        ->postJson('/api/workspaces/share', [
            'workspace' => $workspace->uuid,
            'user' => $user->uuid,
            'member_role' => 'guest'
        ])
        ->assertStatus(200);

/*     print_r($response->getContent()); */

    $workspace->refresh();
    $this->assertNotEmpty($workspace->users);
});

test('User can unshare workspace with another user', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    $user = User::factory()->withRole('user')->create();

    // Authenticate owner and share workspace
    $this->actingAs($owner, 'sanctum')
        ->postJson('/api/workspaces/share', [
            'workspace' => $workspace->uuid,
            'user' => $user->uuid,
            'member_role' => 'guest'
        ])->assertStatus(200);

    $workspace->refresh();
    $this->assertNotEmpty($workspace->users);

    // Unshare workspace
    $this->actingAs($owner, 'sanctum')
        ->postJson('/api/workspaces/unshare', [
            'workspace' => $workspace->uuid,
            'user' => $user->uuid,
        ])->assertStatus(200);

    $workspace->refresh();
    $this->assertEmpty($workspace->users);
});

test('User can get one workspace if they are invited', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    $user = User::factory()->withRole('user')->create();

    // Owner shares the workspace with user
    $this->actingAs($owner, 'sanctum')
        ->postJson('/api/workspaces/share', [
            'workspace' => $workspace->uuid,
            'user' => $user->uuid,
            'member_role' => 'guest'
        ])->assertStatus(200);

    // Invited user retrieves the shared workspace
    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/workspaces/' . $workspace->uuid);

    $response->assertStatus(200);

    $this->assertNotNull($response->json());
});

test('User can get all workspaces where they are invited', function () {
    $this->seed();

    $userOne = User::factory()->withRole('user')->create();
    $userTwo = User::factory()->withRole('user')->create();
    $workspaceOne = Workspace::factory()->withOwner($userOne)->create();
    $workspaceTwo = Workspace::factory()->withOwner($userTwo)->create();
    $invitedUser = User::factory()->withRole('user')->create();

    // User One shares workspaceOne with invitedUser
    $this->actingAs($userOne, 'sanctum')
        ->postJson('/api/workspaces/share', [
            'workspace' => $workspaceOne->uuid,
            'user' => $invitedUser->uuid,
            'member_role' => 'guest'
        ])->assertStatus(200);

    // User Two shares workspaceTwo with invitedUser
    $this->actingAs($userTwo, 'sanctum')
        ->postJson('/api/workspaces/share', [
            'workspace' => $workspaceTwo->uuid,
            'user' => $invitedUser->uuid,
            'member_role' => 'guest'
        ])->assertStatus(200);

    // Invited user retrieves all shared workspaces
    $response = $this->actingAs($invitedUser, 'sanctum')
        ->getJson('/api/workspaces/shared');

    $response->assertStatus(200);
    $this->assertCount(2, $response->json());
});

test('User can create workspace', function () {
    $this->seed();

    $user = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->make()->toArray();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/workspaces/', $workspace)
        ->assertStatus(200);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/workspaces/')
        ->assertStatus(200);

    $this->assertCount(1, $response->json());
});

test('User can update workspace', function () {
    $this->seed();

    $user = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($user)->create();

    $fakeName = fake()->company();

    $this->actingAs($user, 'sanctum')
        ->putJson('/api/workspaces/' . $workspace->uuid, [
            'name' => $fakeName
        ])
        ->assertStatus(200);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/workspaces/' . $workspace->uuid)
        ->assertStatus(200);

    $this->assertTrue($workspace->name !== $response['name']);
    $this->assertTrue($fakeName === $response['name']);
});

test('User can delete workspace', function () {
    $this->seed();

    $user = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($user)->create();

    $this->actingAs($user, 'sanctum')
        ->deleteJson('/api/workspaces/' . $workspace->uuid)
        ->assertStatus(200);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/workspaces/')
        ->assertStatus(200);

    $this->assertEmpty($response->json());
});

test('User cannot get workspace if he is not invited', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    $user = User::factory()->withRole('user')->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/workspaces/' . $workspace->uuid)
        ->assertStatus(403);
});

test('User cannot update workspace if he is not owner', function () {
    $this->seed();

    $user = User::factory()->withRole('user')->create();
    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();

    $fakeName = fake()->company();

    $this->actingAs($user, 'sanctum')
        ->putJson('/api/workspaces/' . $workspace->uuid, [
            'name' => $fakeName
        ])
        ->assertStatus(403);
});

test('User cannot delete workspace if he is not owner', function () {
    $this->seed();

    $user = User::factory()->withRole('user')->create();
    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();

    $this->actingAs($user, 'sanctum')
        ->deleteJson('/api/workspaces/' . $workspace->uuid)
        ->assertStatus(403);
});

test('User cannot share workspace if he is not owner', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    $user = User::factory()->withRole('user')->create();
    $otherUser = User::factory()->withRole('user')->create();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/workspaces/share', [
            'workspace' => $workspace->uuid,
            'user' => $otherUser->uuid,
            'member_role' => 'guest'
        ])
        ->assertStatus(403);
});

test('User cannot unshare workspace if he is not owner', function () {
    $this->seed();

    $owner = User::factory()->withRole('user')->create();
    $workspace = Workspace::factory()->withOwner($owner)->create();
    $user = User::factory()->withRole('user')->create();
    $otherUser = User::factory()->withRole('user')->create();

    // Authenticate owner and share workspace
    $this->actingAs($owner, 'sanctum')
        ->postJson('/api/workspaces/share', [
            'workspace' => $workspace->uuid,
            'user' => $otherUser->uuid,
            'member_role' => 'guest'
        ])->assertStatus(200);

    $workspace->refresh();
    $this->assertNotEmpty($workspace->users);

    // Unshare workspace
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/workspaces/unshare', [
            'workspace' => $workspace->uuid,
            'user' => $otherUser->uuid,
        ])->assertStatus(403);
});
