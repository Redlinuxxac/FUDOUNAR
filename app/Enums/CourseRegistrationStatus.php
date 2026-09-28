<?php

namespace App\Enums;

enum CourseRegistrationStatus: string
{
    case PENDING = 'pending';       // Preinscrito, correo sin validar (no resta cupo firme)
    case VERIFIED = 'verified';     // Correo validado (reserva cupo temporalmente)
    case ENROLLED = 'enrolled';     // Formalizado presencialmente en la institución
    case EXPIRED = 'expired';       // Plazo vencido sin presentarse (cupo liberado)
    case CANCELLED = 'cancelled';   // Cancelado voluntariamente

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente de Validación',
            self::VERIFIED => 'Reserva Activa (Por Formalizar)',
            self::ENROLLED => 'Inscripción Formalizada',
            self::EXPIRED => 'Reserva Vencida (Liberada)',
            self::CANCELLED => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::VERIFIED => 'blue',
            self::ENROLLED => 'green',
            self::EXPIRED => 'zinc',
            self::CANCELLED => 'red',
        };
    }

    public function badgeColor(): string
    {
        return $this->color();
    }
}
