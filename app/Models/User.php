<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
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
        'role',
        'phone',
        'address',
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

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role==='admin';
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Determine if the user is an admin.
     *
     * @return bool
     */
    /*******  e65a1f22-a997-4409-ba98-c87bffd2b41e  *******/
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStudent()
    {

        return $this->role === 'student';
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function studentDetail()
    {
        return $this->hasOne(StudentDetail::class);
    }
    
}
