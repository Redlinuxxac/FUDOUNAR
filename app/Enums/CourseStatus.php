<?php

namespace App\Enums;

enum CourseStatus: string
{
    case DRAFT = 'draft';
    case UPCOMING = 'upcoming';
    case OPEN = 'open';
    case FINISHED = 'finished';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::UPCOMING => 'Programado',
            self::OPEN => 'Inscripciones Abiertas',
            self::FINISHED => 'Finalizado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::UPCOMING => 'blue',
            self::OPEN => 'green',
            self::FINISHED => 'red',
        };
    }
}
