<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OrderFile extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'order_files';
    protected $fillable = [
        'order_id',
        'filename',
        'filepath',
        'category',
        'status',
        'claimed_by',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'claimed_by']);
    }
}
