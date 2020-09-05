<?php

use api\components\ErrorResponseHelper;
use common\models\User;
use yii\rest\UrlRule;
use yii\web\Response;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);


/** @noinspection PhpComposerExtensionStubsInspection */
return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'basePath' => '@api/modules/v1',
            'class' => 'api\modules\v1\Module'
        ]
    ],

    'components' => [

        'request' => [
            'enableCookieValidation' => false,
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],


        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => UrlRule::class,
                    'pluralize' => false,
                    'controller' => [
                        'v1/user',
                        'v1/friend',
                    ],
                    'extraPatterns' => [
                        'POST login' => "login",
                        'POST code' => "send-code",
                        'POST verify-code' => "verify-code",
                        'POST account-data' => "account-data",
                        'GET profile' => "profile",
                        'POST add' => "add",
                        'GET upcoming-birthdays' => "upcoming-birthdays",
                        'GET friend-list' => "friend-list",
                        'POST friend-details' => "friend-details",
                        'GET delete-user' => "delete-user",
                        'GET test-notify' => "test-notify",
                    ]
                ],
            ],
        ],
        'response' => [
            // ...
            'formatters' => [
                Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    // ...
                ],
            ],
            'on beforeSend' => [
                ErrorResponseHelper::class,
                'beforeResponseSend',
            ],
        ],
    ],
    'params' => $params,
];
