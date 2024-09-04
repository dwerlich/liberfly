<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = User::factory()->count(10)->create();

        // Gerar token de acesso para cada usuário
        foreach ($users as $user) {
            $hashedToken = hash('sha256', 'test-token-liberfly-' . $user->id);
            DB::table('personal_access_tokens')->insert([
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => $user->id,
                'name' => 'Manual Token',
                'token' => $hashedToken,
                'abilities' => json_encode(['*']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

