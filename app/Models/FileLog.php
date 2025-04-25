<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileLog extends Model
{
    use HasFactory;

    protected $table = 'file_logs';
    protected $fillable = [
        'order_file_id',
        'user_id',
        'action',
        'notes',
    ];

    public function file()
    {
        return $this->belongsTo(OrderFile::class, 'order_file_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
