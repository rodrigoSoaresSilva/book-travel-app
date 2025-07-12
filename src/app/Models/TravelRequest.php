<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model responsável por representar um Pedido de Viagem no sistema.
 *
 * Cada pedido possui informações sobre o solicitante, destino, datas de ida e volta,
 * status atual e quem aprovou ou cancelou (caso aplicável).
 */
class TravelRequest extends Model
{
    use HasFactory;

    /**
     * Constantes que representam os status possíveis de um pedido de viagem.
     */
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

    /**
     * Retorna o rótulo (label) legível do status atual do pedido.
     * Este é um accessor que pode ser acessado como $model->status_label.
     *
     * @return string Texto correspondente ao status atual (ex: "Solicitado", "Aprovado", etc.).
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SOLICITADO => 'Solicitado',
            self::STATUS_APROVADO   => 'Aprovado',
            self::STATUS_CANCELADO  => 'Cancelado',
            default                 => 'Desconhecido',
        };
    }

    /**
     * Relacionamento com o usuário que solicitou o pedido de viagem.
     *
     * Define que o pedido pertence a um usuário.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com o usuário que aprovou o pedido de viagem.
     *
     * Define que o campo `approved_by` é uma chave estrangeira
     * que referencia o ID de um usuário que aprovou o pedido.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relacionamento com o usuário que cancelou o pedido de viagem.
     *
     * Define que o campo `canceled_by` é uma chave estrangeira
     * que referencia o ID de um usuário que cancelou o pedido.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function canceller()
    {
        return $this->belongsTo(User::class, 'canceled_by');
    }
}
