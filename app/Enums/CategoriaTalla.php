<?php
namespace App\Enums;

enum CategoriaTalla: string
{
    case INFANTIL = 'pending';
    case ADULTO = 'paid';


    // Puedes añadir métodos extra, tt
    public function label(): string {
        return match($this) {
            self::INFANTIL => 'Infantil',
            self::ADULTO => 'Adulto',
        };
    }
}