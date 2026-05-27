<?php

namespace App\Enums;

enum ActivityStatus: string
{
    case DRAFT = 'draft';
    case UPCOMING = 'upcoming';
    case ACTIVE = 'active';
    case FINISHED = 'finished';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::UPCOMING => 'Próximamente',
            self::ACTIVE => 'Activa',
            self::FINISHED => 'Finalizada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::UPCOMING => 'blue',
            self::ACTIVE => 'green',
            self::FINISHED => 'red',
        };
    }
}
