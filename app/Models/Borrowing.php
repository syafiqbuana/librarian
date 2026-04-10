<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Borrowing extends Model
{
    protected $fillable = [
        'user_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'lost_fine',
        'damage_fine'
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
    ];

    // kondisi aktif (real-time)
    public function isOverdue()
    {
        return !$this->return_date
            && now()->gt($this->due_date);
    }

    public function getFineAttribute()
    {
        $endDate = ($this->return_date ?? now())->startOfDay();
        $dueDate = $this->due_date->startOfDay();

        $lateFine = 0;

        if ($endDate->gt($dueDate)) {
            $daysLate = $dueDate->diffInDays($endDate);
            $lateFine = $daysLate * 5000;
        }

        return $lateFine + ($this->damage_fine ?? 0) + ($this->lost_fine ?? 0);
    }

    // kondisi historis
    public function isReturnedLate()
    {
        return $this->return_date?->gt($this->due_date) ?? false;
    }

    public function isWaiting()
    {

        return $this->status === 'waiting';
    }

    public function isPendingReturn()
    {
        return $this->status === 'pending_return';
    }

    public function isReturned()
    {
        return $this->status === 'returned' || $this->status === 'returned_late';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function borrowingDetail()
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

}
