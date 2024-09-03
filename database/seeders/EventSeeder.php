<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;

class EventSeeder extends Seeder
{
    public function run()
    {
        $events = Event::factory()->count(3)->create();

        // Associar aleatoriamente os usuários aos eventos
        User::all()->each(function ($user) use ($events) {
            $user->events()->attach(
                $events->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
