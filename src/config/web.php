<?php
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone' => 'America/Los_Angeles',
    'modules' => [
        'api' => app\modules\api\Module::class,
        'user' => app\modules\user\Module::class,
        'jobs' => app\modules\jobs\Module::class,
        'notifications' => app\modules\notifications\Module::class,
        'reports' => app\modules\reports\Module::class,
        'gridview' => kartik\grid\Module::class
    ],
    'components' => [
        'formatter' => [
            'timeZone' => 'America/Los_Angeles',
            'defaultTimeZone' => 'America/Los_Angeles'
        ],
        'request' => [
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY'),
            'parsers' => [
                'application/json' => yii\web\JsonParser::class,
                'application/json; charset=UTF-8' => yii\web\JsonParser::class,
            ]
        ],
        'cache' => [
            'class' => yii\caching\MemCache::class,
            'servers' => [
                [
                    'host' => getenv('MEMCACHE_HOST'),
                    'port' => getenv('MEMCACHE_PORT')
                ]
            ]
        ],
        'pusher' => [
            'class' => \app\components\PusherComponent::class,
            'auth_key' => getenv('PUSHER_APP_KEY'),
            'secret' => getenv('PUSHER_APP_SECRET'),
            'app_id' => getenv('PUSHER_APP_ID')
        ],
        'user' => [
            'identityClass' => app\models\User::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['/user/auth/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'error/error',
        ],
        'mailer' => [
            'class' => yii\swiftmailer\Mailer::class,
            'useFileTransport' => getenv('MAILER_FILE_TRANSFER'),
            //'htmlLayout' => '@mail/layouts',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => getenv('SMTP_HOST'),
                'username' => getenv('SMTP_USERNAME'),
                'password' => getenv('SMTP_PASSWORD'),
                'port' => getenv('SMTP_PORT'),
                'encryption' => getenv('SMTP_ENCRYPTION')
            ]
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'baseUrl' => getenv('MAIN_DOMAIN'),
            'rules' => [
                [
                    'class' => \yii\rest\UrlRule::class,
                    'controller' => ['cost-centers' => 'api/job-cost-center'],
                    'prefix' => 'api/jobs/<job_id>/'
                ],
                [
                    'class' => yii\web\GroupUrlRule::class,
                    'prefix' => 'api',
                    'routePrefix' => 'api/',
                    'rules' => [
                        '/' => 'site/index',
                        ['class' => \yii\rest\UrlRule::class, 'controller' => 'users'],
                        ['class' => \yii\rest\UrlRule::class, 'controller' => 'work-type'],
                        ['class' => \yii\rest\UrlRule::class, 'controller' => ['agencies' => 'agency']],
                        ['class' => \yii\rest\UrlRule::class, 'controller' => 'jobs', 'pluralize' => false],
                        ['class' => \yii\rest\UrlRule::class, 'controller' => 'saved-searches'],
                        ['class' => \yii\rest\UrlRule::class, 'controller' => 'cost-center'],
                        ['class' => \yii\rest\UrlRule::class, 'controller' => 'comment-types'],

                        [
                            'class' => \yii\rest\UrlRule::class,
                            'controller' => ['comments' => 'job-comments'],
                            'prefix' => 'jobs/<job_id>/'
                        ],
                        [
                            'class' => \yii\rest\UrlRule::class,
                            'controller' => ['translations' => 'job-translations'],
                            'prefix' => 'jobs/<job_id>/'
                        ],
                        [
                            'class' => \yii\rest\UrlRule::class,
                            'controller' => ['invoices' => 'job-invoices'],
                            'prefix' => 'jobs/<job_id>/'
                        ],
                        [
                            'class' => \yii\rest\UrlRule::class,
                            'patterns' => [
                                'PATCH {id}' => 'update',
                                'PUT {id}' => 'upload',
                                'DELETE {id}' => 'delete',
                                'GET,HEAD {id}' => 'view',
                                'POST' => 'create',
                                'GET,HEAD' => 'index',
                                '{id}' => 'options',
                                '' => 'options',
                            ],
                            'controller' => ['files' => 'job-files'],
                            'prefix' => 'jobs/<job_id>/'
                        ],
                        'jobs/<job_id>/logs' => 'job-logs/index',
                        'jobs/<job_id>/logs/<a>' => 'job-logs/<a>',
                        '<c>' => '<c>/index',
                        '<c>/<a>' => '<c>/<a>',
                    ],
                ],
                '/' => 'jobs/jobs/index',
                '<c>' => '<c>/index',
                '<c>/<a>' => '<c>/<a>',
                '<m>/<c>/<a>' => '<m>/<c>/<a>',
            ],
        ],
        'assetManager' => [
            'appendTimestamp' => true
        ],
    ],
    'params' => $params,
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ]
];

if (!empty(getenv('SENTRY_DSN'))) {
    $config['components']['sentry'] = [
        'class' => \mito\sentry\Component::class,
        'dsn' => getenv('SENTRY_DSN'),
        'environment' => getenv('YII_ENV'),
    ];
    $config['components']['log'] = [
        'targets' => [
            [
                'class' => \mito\sentry\Target::class,
                'levels' => ['error', 'warning'],
                'except' => [
                    \yii\web\NotFoundHttpException::class,
                    \yii\base\InvalidRouteException::class,
                    'yii\web\HttpException:404',
                    'yii\web\User::getIdentityAndDurationFromCookie'
                ]
            ]
        ],
    ];
}

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = \app\components\ModelEventCatcherComponent::class;
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => yii\debug\Module::class,
        'allowedIPs' => ['*.*.*.*']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
        'allowedIPs' => ['*.*.*.*']
    ];
}

return $config;
