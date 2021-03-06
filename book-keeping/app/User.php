<?php

namespace App;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Lang;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Send the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        ResetPasswordNotification::toMailUsing(function ($notifiable, $token) {
            return (new MailMessage())
                ->subject(Lang::get('Reset Password Notification'))
                ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
                ->action(Lang::get('Reset Password'), url(config('app.url').route('password.reset', ['token' => $token, 'name' => $notifiable->getIndexForPasswordReset('name')], false)))
                ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.users.expire')]))
                ->line(Lang::get('If you did not request a password reset, no further action is required.'));
        });

        $this->notify((new ResetPasswordNotification($token)));
    }

    /**
     * Get the Index (e-mail address or name) of user who password reset links are sent.
     *
     * @param string $index_name
     *
     * @return string
     */
    public function getIndexForPasswordReset($index_name)
    {
        switch ($index_name) {
            case 'email':
                return $this->email;
                break;
            case 'name':
                return $this->name;
                break;
        }
    }
}
