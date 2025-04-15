<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderFile extends Model
{
    use HasFactory;

    protected $table = 'order_files';
    protected $fillable = [
        'order_id',
        'filename',
        'filepath',
        'category',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }
}
