<?php

namespace Cnj\Seotamic;

use Statamic\Events;

class Subscriber
{
    protected $blueprint;

    /**
     * List of subscribed events
     *
     * @var array
     */
    protected $events = [
        Events\EntryBlueprintFound::class => 'addFields',
    ];

    /**
     * Registers event listeners
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        foreach ($this->events as $event => $method) {
            $events->listen($event, self::class.'@'.$method);
        }
    }

    /**
     * Add SEOtamic fields to the collection blueprint
     *
     * @param mixed $event
     */
    public function addFields($event)
    {
        $this->blueprint = $event->blueprint;

        $ignoreBlueprints = config('seotamic.ignore_blueprints', []);
        if (in_array($this->blueprint->handle(), $ignoreBlueprints))
            return;

        $fields = $this->getFields();

        collect($fields)->each(function($field) {
            $this->blueprint->ensureFieldInSection($field['handle'], $field['field'] , 'SEO');
        });
    }

    /**
     * Array of SEOtamic fields
     *
     * @return array
     */
    private function getFields()
    {
        return [[
                'handle' => 'seotamic_meta',
                'field' => [
                    'display' => 'Meta',
                    'listable' => 'hidden',
                    'type' => 'section',
                    'localizable'=> false
                ],
            ],
            [
                'handle' => 'seotamic_title',
                'field' =>  [
                    'options' => [
                        'title' => __('seotamic::blueprint.title'),
                        'custom' => __('seotamic::blueprint.custom')
                    ],
                    'clearable' => false,
                    'multiple' => false,
                    'searchable' => true,
                    'taggable' => false,
                    'push_tags' => false,
                    'cast_booleans' => false,
                    'type' => 'select',
                    'localizable' => false,
                    'listable' => 'hidden',
                    'default' => 'title',
                    'display' => __('seotamic::blueprint.title'),
                    'instructions' => __('seotamic::blueprint.title_instructions')
                ]
            ],
            [
                'handle' => 'seotamic_custom_title',
                'field' =>  [
                    'input_type' => 'text',
                    'character_limit' => 100,
                    'type' => 'text',
                    'localizable' => false,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.custom_title'),
                    'if' => [
                        'seotamic_title'=> 'equals custom'
                    ]
                ]
            ],
            [
                'handle' => 'seotamic_title_prepend',
                'field' =>  [
                    'type' => 'toggle',
                    'instructions' => __('seotamic::blueprint.title_prepend_instructions'),
                    'localizable' => false,
                    'default' => true,
                    'width' => 50,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.title_prepend'),
                ]
            ],
            [
                'handle' => 'seotamic_title_append',
                'field' =>  [
                    'type' => 'toggle',
                    'localizable' => false,
                    'instructions' => __('seotamic::blueprint.title_append_instructions'),
                    'width' => 50,
                    'listable' => 'hidden',
                    'default' => true,
                    'display' => __('seotamic::blueprint.title_append'),
                ]
            ],
            [
                'handle' => 'seotamic_meta_description',
                'field' =>  [
                    'options' => [
                        'empty' => __('seotamic::blueprint.empty'),
                        'general' => __('seotamic::blueprint.general'),
                        'custom' => __('seotamic::blueprint.custom'),
                    ],
                    'clearable' => false,
                    'default' => 'empty',
                    'multiple' => false,
                    'searchable' => true,
                    'taggable' => false,
                    'push_tags' => false,
                    'cast_booleans' => false,
                    'type' => 'select',
                    'instructions' => __('seotamic::blueprint.description_instructions'),
                    'localizable' => false,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.description'),
                ]
            ],
            [
                'handle' => 'seotamic_custom_meta_description',
                'field' =>  [
                    'input_type' => 'text',
                    'character_limit' => 200,
                    'type' => 'textarea',
                    'localizable' => false,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.custom_description'),
                    'if' => [
                        'seotamic_meta_description' => 'equals custom'
                    ]
                ]
            ],
            [
                'handle' => 'seotamic_canonical',
                'field' =>  [
                    'type' => 'link',
                    'instructions' => __('seotamic::blueprint.canonical_instructions'),
                    'localizable' => false,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.canonical'),
                ]
            ],
            [
                'handle' => 'seotamic_social',
                'field' =>  [
                    'type' => 'section',
                    'localizable' => false,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.social'),
                ]
            ],
            [
                'handle' => 'seotamic_open_graph_title',
                'field' =>  [
                    'options' => [
                        'title' => __('seotamic::blueprint.title'),
                        'general' => __('seotamic::blueprint.general'),
                        'custom' => __('seotamic::blueprint.custom'),
                    ],
                    'clearable' => false,
                    'default' => 'title',
                    'multiple' => false,
                    'searchable' => true,
                    'taggable' => false,
                    'push_tags' => false,
                    'cast_booleans' => false,
                    'type' => 'select',
                    'localizable' => false,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.og_title'),
                ]
            ],
            [
                'handle' => 'seotamic_custom_open_graph_title',
                'field' =>  [
                    'input_type' => 'text',
                    'character_limit' => 100,
                    'type' => 'text',
                    'localizable' => false,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.custom_og_title'),
                    'if' => [
                        'seotamic_open_graph_title' => 'equals custom'
                    ]
                ]
            ],
            [
                'handle' => 'seotamic_open_graph_description',
                'field' =>  [
                    'options' => [
                        'meta' => __('seotamic::blueprint.description'),
                        'general' => __('seotamic::blueprint.general_description'),
                        'custom' => __('seotamic::blueprint.custom'),
                    ],
                    'clearable' => false,
                    'default' => 'general',
                    'multiple' => false,
                    'searchable' => true,
                    'taggable' => false,
                    'push_tags' => false,
                    'cast_booleans' => false,
                    'type' => 'select',
                    'localizable' => false,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.og_description'),
                ]
            ],
            [
                'handle' => 'seotamic_custom_open_graph_description',
                'field' =>  [
                    'input_type' => 'text',
                    'character_limit' => 200,
                    'type' => 'textarea',
                    'localizable' => false,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.custom_og_description'),
                    'if' => [
                        'seotamic_open_graph_description' => 'equals custom'
                    ]
                ]
            ],
            [
                'handle' => 'seotamic_twitter_title',
                'field' =>  [
                    'options' => [
                        'title' => __('seotamic::blueprint.title'),
                        'general' => __('seotamic::blueprint.general'),
                        'custom' => __('seotamic::blueprint.custom'),
                    ],
                    'clearable' => false,
                    'default' => 'title',
                    'multiple' => false,
                    'searchable' => true,
                    'taggable' => false,
                    'push_tags' => false,
                    'cast_booleans' => false,
                    'type' => 'select',
                    'localizable' => false,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.twitter_title'),
                ]
            ],
            [
                'handle' => 'seotamic_custom_twitter_title',
                'field' =>  [
                    'input_type' => 'text',
                    'character_limit' => 100,
                    'type' => 'text',
                    'localizable' => false,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.custom_twitter_title'),
                    'if' => [
                        'seotamic_twitter_title' => 'equals custom'
                    ]
                ]
            ],
            [
                'handle' => 'seotamic_twitter_description',
                'field' =>  [
                    'options' => [
                        'meta' => __('seotamic::blueprint.description'),
                        'general' => __('seotamic::blueprint.general_description'),
                        'custom' => __('seotamic::blueprint.custom'),
                    ],
                    'clearable' => false,
                    'default' => 'general',
                    'multiple' => false,
                    'searchable' => true,
                    'taggable' => false,
                    'push_tags' => false,
                    'cast_booleans' => false,
                    'type' => 'select',
                    'localizable' => false,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.twitter_description'),
                ]
            ],
            [
                'handle' => 'seotamic_custom_twitter_description',
                'field' =>  [
                    'input_type' => 'text',
                    'character_limit' => 200,
                    'type' => 'textarea',
                    'localizable' => false,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.custom_twitter_description'),
                    'if' => [
                        'seotamic_twitter_description' => 'equals custom'
                    ]
                ]
            ],
            [
                'handle' => 'seotamic_image',
                'field' =>  [
                    'container' => config('seotamic.container'),
                    'mode' => 'grid',
                    'restrict' => false,
                    'allow_uploads' => true,
                    'max_files' => 1,
                    'type' => 'assets',
                    'instructions' => __('seotamic::blueprint.image_instructions'),
                    'localizable' => false,
                    'listable' => 'hidden',
                    'display' => __('seotamic::blueprint.image'),
                ]
            ]
        ];
    }
}
