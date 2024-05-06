<?php

namespace App\Nova;

use App\Nova\Actions\GenerateAvatar;
use App\Nova\Actions\PurgeAssistants;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasManyThrough;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use SynapseSentinel\AskAgent\AskAgent;

class Assistant extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Assistant>
     */
    public static string $model = \App\Models\Assistant::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make(__('Name'), 'name')
                ->sortable()
                ->rules('required', 'max:255')
                ->creationRules('unique:assistants,name'),

            AskAgent::make(),

            Markdown::make(__('Instructions'), 'instructions')
                ->sortable()
                ->alwaysShow()
                ->rules('required'),

            HasManyThrough::make(__('Messages'), 'messages', Message::class),

            HasMany::make(__('Message Recommendations'), 'messageRecommendations', MessageRecommendation::class),

            BelongsTo::make(__('AI Model'), 'aiModel', AiModel::class)
                ->sortable()
                ->rules('required'),

            BelongsTo::make(__('User'), 'user', User::class)->showCreateRelationButton(),

            Text::make(__('Provider Value'), 'provider_value')->readonly()->onlyOnDetail(),

            Text::make(__('Provider'), 'provider')->readonly(),

            Date::make(__('Created At'), 'created_at')->sortable()->exceptOnForms(),

            Date::make(__('Updated At'), 'updated_at')->sortable()->exceptOnForms(),
        ];
    }

    /**
     * Get the cards available for the request.
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     */
    public function actions(NovaRequest $request): array
    {
        return [
            new GenerateAvatar(),
            new PurgeAssistants(),
        ];
    }
}
