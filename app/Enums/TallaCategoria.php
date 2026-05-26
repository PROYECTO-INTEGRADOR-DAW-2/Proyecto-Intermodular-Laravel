<?php
namespace App\Enums;

enum TallaCategoria: string
{
    case PRENDAS_INFANTIL = 'Prendas Infantil';
    case ZAPATILLAS_INFANTIL = 'Zapatillas Infantil';
    case PRENDAS_ADULTO = 'Prendas Adulto';
    case ZAPATILLAS_ADULTO = 'Zapatillas Adulto';


    // Puedes añadir métodos extra, tt
    public function label(): string {
        return match($this) {
            self::PRENDAS_INFANTIL => 'Prendas Infantil',
            self::ZAPATILLAS_INFANTIL => 'Zapatillas Infantil',
            self::PRENDAS_ADULTO => 'Prendas Adulto',
            self::ZAPATILLAS_ADULTO => 'Zapatillas Adulto',
        };
    }
}