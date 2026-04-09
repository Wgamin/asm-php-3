<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserPasswordOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $otp,
        public int $expireMinutes
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Mã OTP đặt lại mật khẩu')
            ->greeting('Xin chào ' . ($notifiable->name ?? 'bạn') . '!')
            ->line('Bạn vừa yêu cầu đặt lại mật khẩu cho tài khoản của mình.')
            ->line('Mã OTP của bạn là: ' . $this->otp)
            ->line('Mã này có hiệu lực trong ' . $this->expireMinutes . ' phút.')
            ->line('Nếu bạn không yêu cầu thao tác này, hãy bỏ qua email này.');
    }
}
