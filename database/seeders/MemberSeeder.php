<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los usuarios que tienen el rol 'member'
        $members = User::whereHas('roles', fn($query) => $query->where('name', 'Socio'))->get();

        foreach ($members as $member) {
            Member::create([
                'user_id' => $member->id,
                'member_number' => 1000 + $member->id
            ]);
        }
    }
}
