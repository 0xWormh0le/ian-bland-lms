<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LMS Configs
    |--------------------------------------------------------------------------
    |
    |
    */
    /*
    |--------------------------------------------------------------------------
    | List of Enabled Course Module Options
    |--------------------------------------------------------------------------
    |
    | Available options :
    | - elearning : scorm module
    | - document : course attachments ex. company specific policy, guidelines, relevant HR contact details,
    */

    'enabled_modules' => env('ENABLED_MODULES', 'elearning;document'),

];
