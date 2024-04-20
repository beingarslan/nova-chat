<?php

namespace App\Nova;

use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasOne;

class MessageResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\MessageResource>
     */
    public static $model = Message::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            BelongsTo::make('To', 'recipient', Chat::class)
                ->hideWhenCreating(),

            BelongsTo::make('Sender', 'sender', Chat::class)
                ->hideWhenCreating(),


            Heading::make('<p class="text-info">Message to <strong>' . optional(User::find($request->viaResourceId))->name . '</strong></p>')->asHtml()
                ->hideFromDetail(),

            Textarea::make('Message', 'body'),

            Text::make('Message', function () {
                return Str::substr(e($this->body), 0, 100);
            })->onlyOnIndex(),

            Text::make('', 'to_id')->default(function ($request) {
                return $request->viaResourceId;
            })->withMeta(['type' => 'hidden']),

            Text::make('When', function () {
                return Carbon::make($this->created_at)->diffForHumans();
            }),

            Text::make('Seen At', function () {
                return $this->seen_at ? Carbon::make($this->seen_at)->diffForHumans() : 'Not yet';
            }),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }

    public static function label()
    {
        return 'Messages';
    }

    public static function uriKey()
    {
        return 'messages';
    }


    public static function indexQuery(NovaRequest $request, $query)
    {
        if (!$request->user()->is_admin) {
            return $query->where('to_id', $request->user()->id)
                ->orWhere('from_id', $request->user()->id);
        }

        return $query;
    }

    // on view message update the message to read
    public static function detailQuery(NovaRequest $request, $query)
    {
        if (!$request->user()->is_admin) {
            $query->where('to_id', $request->user()->id)
                ->orWhere('from_id', $request->user()->id)
                ->update([
                    'seen_at' => now()
                ]);
        }

        return $query;
    }
}
