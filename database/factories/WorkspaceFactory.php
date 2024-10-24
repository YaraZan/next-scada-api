<?php

namespace Database\Factories;

use App\Models\User;
use App\ProtocolEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workspace>
 */
class WorkspaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'protocol' => $this->faker->randomElement(ProtocolEnum::cases()),
            'opc_name' => $this->faker->word,
            'connection_string' => $this->faker->url,
            'host' => $this->faker->domainName,
            'owner_id' => User::factory()->withRole('user')->create(),
        ];
    }
}
