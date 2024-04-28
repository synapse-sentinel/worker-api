<?php

namespace App\Nova\Repeater;

use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

class Message extends Repeatable
{
    /**
     * Get the fields displayed by the repeatable.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            Select::make('role')->options([
                'user' => 'User',
                'assistant' => 'Assistant',
            ])->displayUsingLabels(),
            Trix::make('Content'),
        ];
    }
}
