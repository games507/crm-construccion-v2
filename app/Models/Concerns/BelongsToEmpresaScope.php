<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait BelongsToEmpresaScope
{
    protected static function bootBelongsToEmpresaScope(): void
    {
        // No aplicar en consola (migrate/seed/tinker)
        if (app()->runningInConsole()) return;

        static::addGlobalScope('empresa', function (Builder $q) {

            if (!auth()->check()) return;

            $user = auth()->user();

            // Admin ve todo
            if ($user->can('admin.ver')) return;

            // Si el user no tiene empresa, no filtramos (evita bloquearte por error)
            if (empty($user->empresa_id)) return;

            // Solo aplica si la tabla realmente tiene empresa_id
            $table = (new static)->getTable();
            if (!Schema::hasColumn($table, 'empresa_id')) return;

            $q->where($table.'.empresa_id', (int)$user->empresa_id);
        });
    }
}
