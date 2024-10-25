<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RootUserWorkspacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rootUser = User::whereHas('role', function ($query) {
            $query->where('name', 'root');
        })->firstOrFail();

        $workspace = Workspace::make([
            'protocol' => 'DA',
            'name' => 'Test workspace 1',
            'opc_name' => 'AGG Software Simple OPC Server Simulator',
            'connection_string' => 'opcserversim.Instance.1',
            'host' => null
        ]);

        $workspace->owner()->associate($rootUser);
        $workspace->save();
    }
}
