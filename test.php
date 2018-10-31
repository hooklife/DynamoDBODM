<?php

require 'vendor/autoload.php';
use Hoooklife\DynamodbPodm\DB;


DB::config([
    'default' =>[
        'driver'  => 'dynamedb',
        'debug'   => false,

        // S3 SDK 参数
        // https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html
        'S3Config' => [
            'region'   => 'ap-southeast-1',
            'version'  => '2012-08-10',
            'credentials' => [
                'key' => "####",
                'secret'  => "####",
            ],
        ]
    ]
]);
DB::table('test')->all();

