<?php

namespace App\Enums;

enum ProjectColor: string
{
    case Blue = 'blue';
    case Emerald = 'emerald';
    case Amber = 'amber';
    case Orange = 'orange';
    case Red = 'red';
    case Violet = 'violet';
    case Sky = 'sky';
    case Pink = 'pink';

    public function label(): string
    {
        return match ($this) {
            self::Blue => 'Azul',
            self::Emerald => 'Verde',
            self::Amber => 'Âmbar',
            self::Orange => 'Laranja',
            self::Red => 'Vermelho',
            self::Violet => 'Violeta',
            self::Sky => 'Céu',
            self::Pink => 'Rosa',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
