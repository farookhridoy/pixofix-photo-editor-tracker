<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'parent_id',
        'description',
        'status',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Recursive function to get a full tree path

    /**
     * @return string
     */
    public function getPathSlugs(): string
    {
        $segments = [];
        $category = $this;
        while ($category) {
            $segments[] = Str::slug($category->name);
            $category = $category->parent;
        }
        return implode('/', array_reverse($segments));
    }

    /**
     * @param $categories
     * @param $prefix
     * @param $result
     * @param $parentId
     * @return array|mixed
     */
    public static function treeList($categories = null, $prefix = '', &$result = [], $parentId = null)
    {
        $categories = $categories ?? Category::all();

        foreach ($categories->where('parent_id', $parentId) as $category) {
            $result[$category->id] = $prefix . $category->name;
            self::treeList($categories, $prefix . '- ', $result, $category->id);
        }

        return $result;
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
