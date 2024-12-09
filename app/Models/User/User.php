<?php

namespace App\Models\User;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRole;
use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'email_verification_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'role' => UserRole::class,
            'password' => 'hashed',
        ];
    }

    protected static function newFactory(): UserFActory
    {
        return UserFactory::new();
    }


    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function School(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function CreditPurchaseRecords(): HasMany
    {
        return $this->hasMany(CreditPurchaseRecord::class);
    }

    public function Answers(): HasMany
    {
        return $this->hasMany(UserQuestionAnswered::class);
    }

    public function UserQuestionComments(): HasMany
    {
        return $this->hasMany(UserQuestionComment::class);
    }

    public function StudyPlain()
    {
        // return $this->hasOne(StudyPlain::class);
    }

    public function Archivements(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Archivements::class);
    }

    public function UserSimulationFinished()
    {
        // return $this->hasMany(UserSimulationFinished::class);
    }

    public function UserFlashCreated()
    {
        // return $this->hasMany(UserFlashCreated::class);
    }

    public function UserQuestionAnswereds(): HasMany
    {
        return $this->hasMany(UserQuestionAnswered::class);
    }

    public function UserFlashCardAnnotations()
    {
        // return $this->hasMany(UserFlashCardAnnotation::class);
    }

    public function UserQuestionAnnotations()
    {
        return $this->hasMany(UserQuestionAnnotation::class);
    }
}






