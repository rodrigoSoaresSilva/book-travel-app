<?php

namespace App\Models;

class TravelRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'destination',
        'departure_date',
        'return_date',
        'status',
        'approved_by',
        'canceled_by',
    ];

    // Usuário solicitante
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Usuário aprovador
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Usuário que cancelou
    public function canceller()
    {
        return $this->belongsTo(User::class, 'canceled_by');
    }
}
