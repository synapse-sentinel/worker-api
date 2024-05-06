<?php

namespace App\Nova\Repeater;

use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Http\Requests\NovaRequest;

class Message extends Repeatable
{
    /**
     * Get the fields displayed by the repeatable.
     */
    public function fields(NovaRequest $request): array
    {
        return [

            Markdown::make('Content'),
        ];
    }
}
