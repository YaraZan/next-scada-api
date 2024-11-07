<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schema>
 */
class SchemaFactory extends Factory
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
        ];
    }

    public function withWorkspace(Workspace $workspace): static
    {
        return $this->afterMaking(function ($schema) use ($workspace) {
            $schema->workspace()->associate($workspace);
        });
    }

    public function withCreator(User $user): static
    {
        return $this->afterMaking(function ($schema) use ($user) {
            $schema->creator()->associate($user);
        });
    }
}
