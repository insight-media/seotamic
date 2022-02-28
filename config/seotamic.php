<?php

return [
    // filename to store the settings, each locale is stored in a separate file
    'file' => 'seotamic',

    // Social images asset container
    'container' => 'assets',

    // blueprints we dont add fields to
    'ignore_blueprints' => [],

    // title separator
    'title_separator' => env('TITLE_SEPARATOR', null)
];
