<?php

namespace App\Notifications;

use App\Mail\UserNotifyEmail;
use App\Notifications\Channels\DatabaseChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SendNotificationMail extends Notification
{
    use Queueable;
    public $notifyBy;
    public $notifyTo;
    public $notifyData;
    public $subject;
    public $attchments;
    public $emailTemplate;
    public $message;
    public $user_cc;
    public $user_bcc;

    /**
     * Create a new notification instance.
     */
    public function __construct($data, $notifyData)
    {
        $this->notifyBy = $data;
        $this->notifyData = $notifyData;
        $this->subject = !empty($this->notifyData['subject']) ? $this->notifyData['subject'] : null;
        $this->attchments = !empty($this->notifyData['fileToAttachment']) ? $this->notifyData['fileToAttachment'] : null;
        $this->emailTemplate = !empty($this->notifyData['emailTemplate']) ? $this->notifyData['emailTemplate'] : null;
        $this->message = !empty($this->notifyData['message']) ? $this->notifyData['message'] : null;
        $this->user_cc = !empty($this->notifyData['user_cc']) ? $this->notifyData['user_cc'] : [];
        $this->user_bcc = !empty($this->notifyData['user_bcc']) ? $this->notifyData['user_bcc'] : [];
        // dd($this->notifyData, $this->message);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'mail',
            // 'database',
            DatabaseChannel::class
            // GroupedDbChannel::class
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        $this->notifyTo = $notifiable;
        $receiver = $notifiable;
        // dd($notifiable, "sender receiver");
        if (isset($this->notifyData['emailTemplate']) && !empty($this->notifyData['emailTemplate'])) {
            $this->emailTemplate = $this->notifyData['emailTemplate'];
        } else {
            $this->emailTemplate = 'mails.backend.common';
        }
        if (isset($this->notifyData['fileToAttachment']) && !empty($this->notifyData['fileToAttachment'])) {
            // $this->notifyData['attachmentName']
            $this->attchments = [
                storage_path() . '/slider2.jpg',
                storage_path() . '/slider1.jpg' => [
                    'as' => 'slider1.jpg',
                    // 'mime' => 'image/svg+xml',
                    'mime' => 'image/jpg',
                ]
            ];
        } else {
            $this->attchments = [];
        }
        // dd($notifiable, "sender receiver");
        // return (new MailMessage)
        //     ->line('The introduction to the notification.')
        //     ->action('Notification Action', url('/'))
        //     ->line('Thank you for using our application!');
        
        return (new MailMessage)
        ->subject($this->subject)
        ->cc($this->user_cc)
        ->bcc($this->user_bcc)
        ->from(env('MAIL_FROM_ADDRESS'))
        ->view($this->emailTemplate, $this->notifyData)
        ->attachMany($this->attchments);
        /* ->attach('/path/to/file', [
            'as' => 'name.pdf',
            'mime' => 'application/pdf',
        ]) */
        /* ->attachMany([
            storage_path().'/slider2.jpg',
            storage_path().'/slider1.jpg' => [
                'as' => 'slider1.jpg',
                // 'mime' => 'image/svg+xml',
                'mime' => 'image/jpg',
            ],
        ]); */
        // return Mail::to($receiver)->send(new UserNotifyEmail($this->notifyData));
        
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // 
        // $to = $this->notifyTo; 
        // dd($this->notifyBy, $notifiable);
        return [
            'to' => $notifiable->id,
            'from' => $this->notifyBy->id,
            'message' => $this->message,
            'notifiable_id' => $notifiable->id,
            'type' => get_class($notifiable),
            'notifiable_type' => get_class($this),
            'data' => $this->notifyData,
            'read_at' => null,
        ];
    }

    /*
     * It's important to define toDatabase method due 
     * it's used in notification channel. Of course, 
     * you can change it in GroupedDbChannel.
     */
    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }

    public function backoff(): array
    {
        return [ 3 ] ; // if notification fail try after [] seconds
    }

    
}
