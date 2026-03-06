<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_id',
        'completed_at',
        'issued_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'issued_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public static function generateCertificateId(int $userId, int $courseId, $completedAt): string
    {
        $timestamp = is_numeric($completedAt) 
            ? $completedAt 
            : (is_object($completedAt) ? $completedAt->timestamp : strtotime($completedAt));
        
        $raw = $userId . ':' . $courseId . ':' . $timestamp . ':' . config('app.key');
        $hash = hash_hmac('sha256', $raw, config('app.key'));
        
        return 'CERT-' . strtoupper(substr($hash, 0, 8)) . '-' . date('Y', $timestamp);
    }
}
