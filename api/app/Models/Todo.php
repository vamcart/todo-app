<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    use HasFactory;

    // Fields that can be mass assigned
    protected $fillable = [
        'title',
        'description',
        'is_completed',
        'user_id',
    ];

    // Fields to always append to model instances
    protected $appends = ['completed_at'];

    // Accessor for completed_at timestamp (when todo was marked complete)
    public function getCompletedAtAttribute(): ?string
    {
        if ($this->is_completed === true && $this->updated_at !== null) {
            return $this->updated_at;
        }
        return null;
    }

    // Relationship: A todo belongs to a user (optional)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope for completed todos
    public function scopeCompleted($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_completed', true);
    }

    // Scope for pending (incomplete) todos
    public function scopePending($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_completed', false);
    }
}
