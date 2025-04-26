<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MFATokenMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * MFA令牌
     */
    public $token;

    /**
     * 创建一个新的消息实例
     */
    public function __construct($token)
    {
        $this -> token = $token;
    }

    /**
     * 获取消息信封
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject : '您的登录验证码',
        );
    }

    /**
     * 获取消息内容定义
     */
    public function content(): Content
    {
        return new Content(
            view : 'emails.mfa-token',
        );
    }

    /**
     * 获取消息的附件
     */
    public function attachments(): array
    {
        return [];
    }
}
