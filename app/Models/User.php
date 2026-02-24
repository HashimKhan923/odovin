<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

   public function expenses()
    {
        return $this->hasManyThrough(Expense::class, Vehicle::class);
    }

    public function serviceBookings()
    {
        return $this->hasManyThrough(ServiceBooking::class, Vehicle::class);
    }

    public function reminders()
    {
        return $this->hasManyThrough(Reminder::class, Vehicle::class);
    }

    public function alerts()
    {
        return $this->hasManyThrough(Alert::class, Vehicle::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // public function isAdmin()
    // {
    //     return in_array($this->user_type, ['admin', 'support']);
    // }

    public function serviceProvider(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\ServiceProvider::class);
    }

    // Helper scopes:
    public function isProvider(): bool
    {
        return $this->role === 'provider';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }


    

}
