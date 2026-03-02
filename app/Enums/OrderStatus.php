<?php
namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    // Puedes añadir métodos extra, tt
    public function label(): string {
        return match($this) {
            self::PENDING => 'Pendiente de pago',
            self::PAID => 'Pagado, preparando envío',
            self::SHIPPED => 'Comprado',
            self::DELIVERED => 'Enviado',
            self::CANCELLED => 'Cancelado'
        };
    }
}