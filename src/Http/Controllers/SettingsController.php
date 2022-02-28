<?php

namespace Cnj\Seotamic\Http\Controllers;

use Statamic\Facades\Config;
use Statamic\Support\Arr;
use Cnj\Seotamic\File\File;
use Statamic\Facades\Site;
use Illuminate\Http\Request;
use Statamic\Facades\Blueprint;
use Illuminate\Support\Facades\Session;

use Statamic\Http\Controllers\CP\CpController;

class SettingsController extends CpController
{
    /**
     * @var File
     */
    protected $file;

    public function __construct(Request $request, File $file)
    {
        $this->file = $file;

        parent::__construct($request);
    }

    public function index(Request $request) {
        $this->setLocale();

        $blueprint = $this->formBlueprint();
        $fields = $blueprint->fields();

        $values = $this->file->read(false);

        $fields = $fields->addValues($values);

        $fields = $fields->preProcess();

        return view('seotamic::settings', [
            'blueprint' => $blueprint->toPublishArray(),
            'values'    => $fields->values(),
            'meta'      => $fields->meta(),
        ]);
    }

    public function update(Request $request) {
        $this->setLocale();

        $blueprint = $this->formBlueprint();
        $fields = $blueprint->fields()->addValues($request->all());

        // Perform validation. Like Laravel's standard validation, if it fails,
        // a 422 response will be sent back with all the validation errors.
        $fields->validate();

        // Perform post-processing. This will convert values the Vue components
        // were using into values suitable for putting into storage.
        $values = $fields->process()->values();

        $this->file->write($values->toArray());
    }

    /**
     * Since we are accessing the files via CP, we need to fetch the
     * current language via a session variable, and set the locale
     *
     * @return void
     */
    private function setLocale() {
        $this->file->setLocale(
            session('statamic.cp.selected-site') ?
                Site::get(session('statamic.cp.selected-site'))->locale() :
                Site::current()->locale());
    }

    protected function formBlueprint()
    {
        return Blueprint::makeFromSections([
            'name' => [
                'display' => __('seotamic::general.meta_section'),
                'fields' => [
                    'section_title' => [
                        'type' => 'section',
                        'display' => __('seotamic::general.title'),
                        'instructions' => __('seotamic::general.title_instructions')
                    ],
                    'title_prepend' => [
                        'type' => 'text',
                        'character_limit' => '25',
                        'display' => __('seotamic::general.title_prepend'),
                        'instructions' => __('seotamic::general.title_prepend_instructions'),
                    ],
                    'title_append' => [
                        'type' => 'text',
                        'character_limit' => '25',
                        'display' => __('seotamic::general.title_append'),
                        'instructions' => __('seotamic::general.title_append_instructions'),
                    ],
                    'section_description' => [
                        'type' => 'section',
                        'display' => __('seotamic::general.description_section'),
                        'instructions' => __('seotamic::general.description_section_instructions'),
                    ],
                    'meta_description' => [
                        'type' => 'textarea',
                        'character_limit' => '200',
                        'display' => __('seotamic::general.meta_description'),
                        'instructions' => __('seotamic::general.meta_description_instructions'),
                    ],
                ],
            ],
            'social' => [
                'display' => 'Social',
                'fields' => [
                    'social_image' => [
                        'type' => 'assets',
                        'container' => config('seotamic.container'),
                        'max_files' => 1,
                        'display' => __('seotamic::general.social_image'),
                        'instructions' => __('seotamic::general.social_image_instructions'),
                    ],
                    'section_og' => [
                        'type' => 'section',
                        'display' => __('seotamic::general.social_og'),
                        'instructions' => __('seotamic::general.social_og_instructions')
                    ],
                    'open_graph_display' => [
                        'type' => 'toggle',
                        'display' => __('seotamic::general.social_og_display'),
                        'default' => true,
                    ],
                    'open_graph_site_name' => [
                        'type' => 'text',
                        'character_limit' => '50',
                        'display' => __('seotamic::general.social_site_name'),
                        'show_when' => [
                            'open_graph_display' => true
                        ]
                    ],
                    'open_graph_title' => [
                        'type' => 'text',
                        'character_limit' => '100',
                        'display' => __('seotamic::general.title'),
                        'show_when' => [
                            'open_graph_display' => true
                        ],
                        'instructions' => __('seotamic::general.social_og_title_instructions'),
                    ],
                    'open_graph_description' => [
                        'type' => 'textarea',
                        'character_limit' => '200',
                        'display' => __('seotamic::general.description_section'),
                        'show_when' => [
                            'open_graph_display' => true
                        ],
                        'instructions' => __('seotamic::general.social_og_description_instructions'),
                    ],
                    'section_twitter' => [
                        'type' => 'section',
                        'display' => 'Twitter',
                    ],
                    'twitter_display' => [
                        'type' => 'toggle',
                        'display' => __('seotamic::general.social_twitter_display'),
                        'default' => false,
                    ],
                    'twitter_title' => [
                        'type' => 'text',
                        'character_limit' => '100',
                        'display' => __('seotamic::general.title'),
                        'instructions' => '',
                        'show_when' => [
                            'twitter_display' => true
                        ],
                    ],
                    'twitter_description' => [
                        'type' => 'textarea',
                        'character_limit' => '200',
                        'display' => __('seotamic::general.description_section'),
                        'instructions' => '',
                        'show_when' => [
                            'twitter_display' => true
                        ],
                    ],
                ]
            ],
            // 'settings' => [
            //     'display' => 'Settings',
            //     'fields' => [
            //         'section_social' => [
            //             'type' => 'section',
            //             'display' => 'Social',
            //         ],
            //         'facebook_app_id' => [
            //             'type' => 'text',
            //             'display' => 'Facebook App ID',
            //             'instructions' => 'Not implemented yet',
            //         ],
            //         'facebook_publisher_page' => [
            //             'type' => 'text',
            //             'display' => 'Facebook Publisher Page',
            //             'instructions' => 'Facebook Business page - Not implemented yet',
            //         ],
            //         'twitter_profile' => [
            //             'type' => 'text',
            //             'display' => 'Twitter Profile',
            //             'placeholder' => '@your-profile-name',
            //             'instructions' => 'Link your twitter profile - Not implemented yet',
            //         ],
            //     ]
            // ]
        ]);
    }
}
