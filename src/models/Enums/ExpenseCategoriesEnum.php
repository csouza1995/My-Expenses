<?php

namespace app\models\Enums;

enum ExpenseCategoriesEnum: int
{
    case OUTROS = 0;
    case ALIMENTACAO = 1;
    case TRANSPORTE = 2;
    case MORADIA = 3;
    case SAUDE = 4;
    case LAZER = 5;

    public static function getValues(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function getLabels(): array
    {
        return [
            self::OUTROS->value => 'Outros',
            self::ALIMENTACAO->value => 'Alimentação',
            self::TRANSPORTE->value => 'Transporte',
            self::MORADIA->value => 'Moradia',
            self::SAUDE->value => 'Saúde',
            self::LAZER->value => 'Lazer',
        ];
    }

    public static function getLabel(int $value): string
    {
        $labels = self::getLabels();
        return $labels[$value] ?? 'Desconhecido';
    }

    public static function getList(): array
    {
        $list = [];
        foreach (self::cases() as $case) {
            $list[$case->value] = self::getLabel($case->value);
        }
        return $list;
    }
}
