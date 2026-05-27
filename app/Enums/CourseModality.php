<?php

namespace App\Enums;

enum CourseModality: string
{
    case PRESENTIAL = 'presencial';
    case ONLINE = 'online';
    case HYBRID = 'hybrid';

    public function label(): string
    {
        return match ($this) {
            self::PRESENTIAL => 'Presencial',
            self::ONLINE => 'En Línea',
            self::HYBRID => 'Híbrido',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PRESENTIAL => 'blue',
            self::ONLINE => 'purple',
            self::HYBRID => 'green',
        };
    }
}
