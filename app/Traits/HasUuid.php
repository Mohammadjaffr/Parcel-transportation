<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return $this->where($field, $value)->firstOrFail();
        }

        return $this->where(function ($query) use ($value) {
            $query->where('uuid', $value);
            if (is_numeric($value)) {
                $query->orWhere($this->getRouteKeyName() ?? 'id', $value);
            }
        })->firstOrFail();
    }
}
