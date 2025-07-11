<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelRequest extends Model
{
    use HasFactory;

    public const STATUS_SOLICITADO = 'S';
    public const STATUS_APROVADO   = 'A';
    public const STATUS_CANCELADO  = 'C';

    protected $fillable = [
        'user_id',
        'destination',
        'departure_date',
        'return_date',
        'status',
        'approved_by',
        'canceled_by',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SOLICITADO => 'Solicitado',
            self::STATUS_APROVADO   => 'Aprovado',
            self::STATUS_CANCELADO  => 'Cancelado',
            default                 => 'Desconhecido',
        };
    }

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
