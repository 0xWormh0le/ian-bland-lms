<?php

return [

    /**
     * Service API URL
     */
    'url' => env('SCORM_URL', 'https://scormdispatch.co.uk/api/v1'),

    /**
     * Application ID
     */
    'id' => env('SCORM_ID', ''),

    /**
     * Application Secret Key
     */
    'secret' => env('SCORM_SECRET', ''),

    /**
     * BBB Server URL
     */
    'bbb_url' => env('BBB_SERVER_BASE_URL', ''),

    /**
     * BBB Security Salt
     */
    'bbb_secret' => env('BBB_SECURITY_SALT', ''),
  
];
