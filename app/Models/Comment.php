<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    protected $fillable = ['class_id', 'user_id', 'parent_id', 'content', 'admin_approval'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'class_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // Direct relationship for children
    public function replies(): HasMany
    {
        return $this->children()->where('admin_approval', true);
    }

    // Recursive relationship helper
    public function approvedReplies(): HasMany
    {
        return $this->replies()->with(['user', 'approvedReplies']);
    }
}
