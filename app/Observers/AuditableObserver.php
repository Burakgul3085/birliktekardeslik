<?php

namespace App\Observers;

use App\Models\AdminActivityLog;
use App\Support\AdminActivity;
use Illuminate\Database\Eloquent\Model;

class AuditableObserver
{
    public function created(Model $model): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        AdminActivity::log(
            AdminActivityLog::TYPE_MODEL_CREATED,
            class_basename($model) . ' kaydi olusturuldu',
            $user->id,
            $model::class,
            (int) $model->getKey(),
            ['attributes' => $model->getAttributes()]
        );
    }

    public function updated(Model $model): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        AdminActivity::log(
            AdminActivityLog::TYPE_MODEL_UPDATED,
            class_basename($model) . ' kaydi guncellendi',
            $user->id,
            $model::class,
            (int) $model->getKey(),
            [
                'changes' => $changes,
                'old' => array_intersect_key($model->getOriginal(), $changes),
            ]
        );
    }

    public function deleted(Model $model): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        AdminActivity::log(
            AdminActivityLog::TYPE_MODEL_DELETED,
            class_basename($model) . ' kaydi silindi',
            $user->id,
            $model::class,
            (int) $model->getKey(),
            ['attributes' => $model->getAttributes()]
        );
    }
}
