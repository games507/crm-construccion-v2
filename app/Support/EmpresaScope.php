<?php

namespace App\Support;

class EmpresaScope
{
    private const KEY = 'empresa_context_id';

    public static function getId(): int
    {
        return (int) (session(self::KEY, 0) ?: 0);
    }

    public static function has(): bool
    {
        return self::getId() > 0;
    }

    public static function set(int $id): void
    {
        session([self::KEY => $id]);
    }

    public static function clear(): void
    {
        session()->forget(self::KEY);
    }
}
