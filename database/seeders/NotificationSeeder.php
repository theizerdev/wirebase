<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        $notifications = [
            [
                'title' => 'Congratulation Lettie 🎉',
                'message' => 'Won the monthly best seller gold badge',
                'type' => 'success',
                'data' => ['avatar' => '/materialize/assets/img/avatars/1.png', 'icon_type' => 'image']
            ],
            [
                'title' => 'Charles Franklin',
                'message' => 'Accepted your connection',
                'type' => 'info',
                'data' => ['avatar' => 'CF', 'icon_type' => 'initials']
            ],
            [
                'title' => 'New Message ✉️',
                'message' => 'You have new message from Natalie',
                'type' => 'info',
                'data' => ['avatar' => '/materialize/assets/img/avatars/2.png', 'icon_type' => 'image']
            ],
            [
                'title' => 'Whoo! You have new order 🛒',
                'message' => 'ACME Inc. made new order $1,154',
                'type' => 'warning',
                'data' => ['avatar' => 'SC', 'icon_type' => 'initials-icon', 'icon' => 'ri-shopping-cart-2-line']
            ],
            [
                'title' => 'Account Activated',
                'message' => 'Your account has been activated successfully',
                'type' => 'success',
                'data' => ['avatar' => 'AA', 'icon_type' => 'initials']
            ],
            [
                'title' => 'Payment Due',
                'message' => 'Payment of $299 is due today',
                'type' => 'error',
                'data' => ['avatar' => 'PD', 'icon_type' => 'initials-icon', 'icon' => 'ri-money-dollar-circle-line']
            ]
        ];

        foreach ($users as $user) {
            // Crear 3-4 notificaciones por usuario
            $userNotifications = collect($notifications)->random(rand(3, 4));

            foreach ($userNotifications as $notification) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $notification['title'],
                    'message' => $notification['message'],
                    'type' => $notification['type'],
                    'data' => $notification['data'],
                    'created_at' => now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
                    'read_at' => rand(0, 1) === 1 ? now()->subHours(rand(1, 24)) : null
                ]);
            }
        }
    }
}
