<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            if (rand(0,2) === 0) continue;
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Welcome',
                'message' => 'This is a sample notification for '.$user->name,
                'type' => 'system_notification',
                'related_type' => null,
                'related_id' => null,
                'is_email_sent' => false,
            ]);
        }
    }
}
