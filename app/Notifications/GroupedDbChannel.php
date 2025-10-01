<?php
namespace App\Notifications;


use Illuminate\Notifications\Notification;

class GroupedDbChannel
{
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toDatabase($notifiable);
        // dd($notifiable, $notification, $data);
        return $notifiable->routeNotificationFor('database')->create([
            'id' => $notification->id,
            'to' => $data['to'] ?? null,
            'from' => $data['from'] ?? null,
            'message' => $data['message'] ?? 'The introduction to the notification for database.',
            'notifiable_id' => $notifiable->id,
            'type' => get_class($notification),
            'notifiable_type' => get_class($notifiable),
            'data' => $data,
            'read_at' => null,
        ]);
    }
}