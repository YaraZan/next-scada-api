<?php

namespace Database\Factories;

use App\Models\Schema;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MemberRole>
 */
class MemberRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'color' => $this->faker->hexColor,
            'description' => $this->faker->optional()->sentence,
            'can_write_tags' => $this->faker->boolean,
            'can_create_schemas' => $this->faker->boolean,
        ];
    }

    /**
     * Associate the member role with a specific workspace.
     */
    public function withWorkspace(Workspace $workspace): static
    {
        return $this->afterMaking(function ($memberRole) use ($workspace) {
            $memberRole->workspace()->associate($workspace);
        });
    }

    /**
     * Associate the schemas with a specific member role.
     */
    public function withSchemas(array $schemas): static
    {
        return $this->afterMaking(function ($memberRole) use ($schemas) {
            $memberRole->schemas()->attach($schemas);
        });
    }
}
