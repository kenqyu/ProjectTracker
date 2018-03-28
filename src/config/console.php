<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
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
        'db' => $db,
    ],
    'params' => $params,
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ]
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
    ];
}

return $config;
