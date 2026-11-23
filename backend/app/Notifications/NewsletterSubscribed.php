<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsletterSubscribed extends Notification implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct()
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function backoff(): array
    {
        return [30, 120];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Đăng ký nhận bản tin thành công — Nhà sách Thu Thanh')
            ->greeting('Chào bạn,')
            ->line('Bạn đã đăng ký nhận bản tin của Nhà sách Thu Thanh thành công.')
            ->line('Cảm ơn bạn đã quan tâm đến sách mới, chương trình ưu đãi và các thông tin từ nhà sách.')
            ->action('Ghé thăm Nhà sách Thu Thanh', rtrim(config('app.frontend_url'), '/'))
            ->line('Nếu bạn không thực hiện đăng ký này, hãy liên hệ nhà sách qua thông tin trên website để được hỗ trợ.')
            ->salutation('Nhà sách Thu Thanh');
    }
}
