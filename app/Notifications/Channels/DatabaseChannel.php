<?php
namespace App\Notifications\Channels;

use App\Models\User;
use Illuminate\Notifications\Notification;

class DatabaseChannel
{
    public function send(User $notifiable, Notification $notification)
    {
        try {
            $data = $notification->toDatabase($notifiable);
            $this->store_db($notification, $notifiable, $data);
        } catch (\Exception $exception) {
            report($exception);
        }
    }

    public function store_db(Notification $notification, User $notifiable, array $data)
    {
        // dd($notifiable, $data, $notification);
       /*  activity('create')
        ->performedOn($notifiable) // Entry add in table. model name(subject_type) & id(subject_id)
        ->causedBy($notification->notifyBy) //causer_id = admin id, causer type = admin model
        ->log($data['message']); */

        activity("Send Notification Mail")
            ->performedOn($notifiable) // Entry add in table. model name(subject_type) & id(subject_id)
            ->causedBy($notification->notifyBy) //causer_id = admin id, causer type = admin model
            ->event(__METHOD__)
            ->withProperties(['notify_from_user' => $notification->notifyBy, 'notify_to_user' => $notifiable])
            ->log($data['message']);

        return $notifiable->routeNotificationFor('database')->create([
            "id" => $notification->id,
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