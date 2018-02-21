<?php

use Pagekit\Application;
use Spqr\Notifyoncomment\Event\CommentListener;

return [
    'name' => 'spqr/notifyoncomment',
    'type' => 'extension',
    'main' => function (Application $app) {
    },
    
    'autoload' => [
        'Spqr\\Notifyoncomment\\' => 'src',
    ],
    
    'routes' => [],
    
    'widgets' => [],
    
    'menu' => [],
    
    'permissions' => [
        'notifyoncomment: manage settings' => [
            'title' => 'Manage settings',
        ],
    ],
    
    'settings' => 'notifyoncomment-settings',
    
    'resources' => [
        'spqr/notifyoncomment:' => '',
    ],
    
    'config' => [
        'users' => [],
    ],
    
    'events' => [
        'boot'         => function ($event, $app) {
            $app->subscribe(new CommentListener);
        },
        'site'         => function ($event, $app) {
        },
        'view.scripts' => function ($event, $scripts) use ($app) {
            $scripts->register('notifyoncomment-settings',
                'spqr/notifyoncomment:app/bundle/notifyoncomment-settings.js',
                ['~extensions']);
        },
    ],
];