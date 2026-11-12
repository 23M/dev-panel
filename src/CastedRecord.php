<?php

declare(strict_types=1);

namespace TTM\DevPanel;

use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Lets a form field work with a custom cast: the field holds the stored text, the cast turns it into its value.
 *
 * Casts get the model they work on. The create page has no record yet, so they get a blank instance
 * that carries the values typed into the form so far (a cast may depend on a sibling attribute).
 */
final class CastedRecord
{
    /**
     * @param CastsAttributes<mixed, mixed> $cast
     * @param class-string<Model> $modelClass
     */
    public static function format(CastsAttributes $cast, ?Model $record, string $modelClass, Component $component, string $name, mixed $state): ?string
    {
        if ($state === null || $state === '') {
            return null;
        }

        $model = self::recordFor($record, $modelClass, $component);

        try {
            $stored = $cast->set($model, $name, $state, $model->getAttributes());
        } catch (InvalidArgumentException $exception) {
            // The state may already be the stored text (e.g. typed in), then it has nothing to convert.
            if (is_string($state)) {
                return $state;
            }

            throw $exception;
        }

        return is_scalar($stored) ? (string) $stored : null;
    }

    /**
     * @param CastsAttributes<mixed, mixed> $cast
     * @param class-string<Model> $modelClass
     */
    public static function parse(CastsAttributes $cast, ?Model $record, string $modelClass, Component $component, string $name, mixed $state): mixed
    {
        if ($state === null || $state === '') {
            return null;
        }

        $model = self::recordFor($record, $modelClass, $component);

        return $cast->get($model, $name, $state, $model->getAttributes());
    }

    /** @param class-string<Model> $modelClass */
    private static function recordFor(?Model $record, string $modelClass, Component $component): Model
    {
        if ($record instanceof Model) {
            return $record;
        }

        // Raw attributes: setting them through the model would run the very casts that are being asked.
        return (new $modelClass())->setRawAttributes(array_filter($component->getContainer()->getRawState(), 'is_scalar'));
    }
}
