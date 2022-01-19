<?php

namespace EscolaLms\HeadlessH5P\Traits;

use Illuminate\Database\Eloquent\Builder;

trait QueryExtendable
{
    private static array $extensionsJoin = [];
    private static array $extensionsSelect = [];
    private static array $extendQueryGroupBy = [];

    public static function extendQueryJoin(callable $extension, string $tableName): void
    {
        self::$extensionsJoin[$tableName] = $extension;
    }

    public static function applyQueryJoin(Builder $query): Builder
    {
        foreach (self::$extensionsJoin as $tableName => $extension) {
            $query->leftJoin($tableName, $extension());
        }

        return $query;
    }

    public static function extendQuerySelect(callable $extension): void
    {
        self::$extensionsSelect[] = $extension;
    }

    public static function applyQuerySelect(Builder $query): Builder
    {
        foreach (self::$extensionsSelect as $extension) {
            $query->addSelect($extension());
        }

        return $query;
    }

    public static function extendQueryGroupBy(callable $extension): void
    {
        self::$extendQueryGroupBy[] = $extension;
    }

    public static function applyQueryGroupBy(Builder $query): Builder
    {
        $groupBy = '';
        foreach (self::$extendQueryGroupBy as $extension) {
            $groupBy .= ($groupBy ? ', ' : '' ) . $extension();
        }
        if ($groupBy) {
            $query->groupBy($groupBy);
        }

        return $query;
    }
}