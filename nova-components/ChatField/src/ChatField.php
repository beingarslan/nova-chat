<?php

namespace Acme\ChatField;

use Laravel\Nova\Fields\Field;

class ChatField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'chat-field';

    public function __construct($name = "Chat", $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        // Optionally set a default resolve callback to fetch chat messages
        $this->resolveUsing(function ($value, $resource, $attribute) {
            // Assuming $resource is the user model instance
            // and you have a method or relationship to fetch messages
            $messages = $resource->chatMessages; // Adjust this based on your actual implementation

            // Format messages as needed for display
            return $messages->map(function ($message) {
                return $message->body; // Simplified, consider including timestamps, senders, etc.
            })->implode("\n");
        });
    }
}
