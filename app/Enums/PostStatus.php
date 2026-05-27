<?php

namespace App\Enums;

enum PostStatus: string
{
    case DRAFT = 'draft';
    case UPCOMING = 'upcoming';
    case PUBLISHED = 'published';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::UPCOMING => 'Programado',
            self::PUBLISHED => 'Publicado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::UPCOMING => 'blue',
            self::PUBLISHED => 'green',
        };
    }
}
