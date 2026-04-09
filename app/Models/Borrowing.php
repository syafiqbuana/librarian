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
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
    ];

    // protected static function booted()
    // {
    //     static::creating(function ($borrowing) {
    //         $borrowing->name = auth()->user();
    //         $borrowing->status = 'waiting';
    //         $borrowing->borrow_date = Carbon::now();
    //         $borrowing->due_date = Carbon::now()->addDays(14);
    //     });
    // }

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

    if ($endDate->lte($dueDate)) {
        return 0;
    }

    $daysLate = $dueDate->diffInDays($endDate);

    return $daysLate * 5000;
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
