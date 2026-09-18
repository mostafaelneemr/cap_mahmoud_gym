<?php

namespace App\Models;

class JoinUsSubmission extends GlobalModel
{
    protected $table = 'join_us_submissions';

    protected $fillable = [
        'name',
        'phone',
        'age',
        'country',
        'governorate',
        'training_level',
        'goal',
        'injuries',
        'injury_details',
        'reason',
        'routine',
        'status',
        'notes',
    ];

    /**
     * Status label mapping for display in dashboard.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new'       => '<span class="badge badge-light-primary">New</span>',
            'contacted' => '<span class="badge badge-light-warning">Contacted</span>',
            'converted' => '<span class="badge badge-light-success">Converted</span>',
            'rejected'  => '<span class="badge badge-light-danger">Rejected</span>',
            default     => '<span class="badge badge-light-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}
