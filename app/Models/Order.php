<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, LogsActivity;

    protected $table = 'orders';
    protected $fillable = [
        'order_number',
        'title',
        'description',
        'status',
    ];

    public function files()
    {
        return $this->hasMany(OrderFile::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getProgressAttribute()
    {
        return round(($this->completed_files_count / $this->files_count) * 100, 2);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('original_files')
            ->useDisk('original');

        $this->addMediaCollection('completed_files')
            ->useDisk('processed');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status'])
            ->logOnlyDirty();
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($query) {
            if (Auth::check()) {
                $query->created_by = Auth::user()->id;
            }
        });
        static::updating(function ($query) {
            if (Auth::check()) {
                $query->updated_by = Auth::user()->id;
            }
        });
    }
}
