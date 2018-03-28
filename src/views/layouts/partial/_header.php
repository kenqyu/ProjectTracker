<?php
/**
 * @var $this \yii\web\View
 * @var $user \app\models\User
 */

use app\models\enums\UserRoles;

$newUsers = \app\models\User::find()->needActivation()->count();
$user = Yii::$app->user->identity;
$notifications = \app\services\NotificationService::getInstance()->getUnreadCount($user);
?>
<header class="container-fluid">
    <div class="row top">
        <div class="col-md-2">
            <a href="/" class="logo">
                <img src="/static/images/logo.png" alt="Edison">
            </a>
        </div>
        <div class="col-md-10">
            <h1>SCE CX Requests and Project Tracker</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php if (Yii::$app->user->identity->role >= UserRoles::MANAGER) { ?>
                <?php
                \yii\bootstrap\Modal::begin([
                    'id' => 'search',
                    'header' => 'Advanced Search'
                ]);
                ?>
                <?= $this->render('_search_filters') ?>
                <?php \yii\bootstrap\Modal::end() ?>
            <?php } ?>
            <?= \yii\bootstrap\Nav::widget([
                'items' => [
                    [
                        'label' => 'Home',
                        'url' => ['/jobs/jobs/index']
                    ],
                    [
                        'label' => 'Archives',
                        'url' => ['/jobs/jobs/archive'],
                        'visible' => $user->role >= UserRoles::MANAGER
                    ],
                    [
                        'label' => 'Old Database',
                        'url' => ['/jobs/archive/old-index'],
                        'visible' => $user->role >= UserRoles::MANAGER
                    ],
                    [
                        'label' => 'Reports',
                        'url' => '#search',
                        'visible' => $user->role >= UserRoles::MANAGER,
                        'linkOptions' => [
                            'data-toggle' => 'modal'
                        ]
                    ],
                    [
                        'label' => 'Dashboard',
                        'url' => ['/reports/default/index'],
                        'visible' => $user->role >= UserRoles::MANAGER
                    ],
                    [
                        'label' => 'Users' . ($newUsers > 0 ? ' <span class="badge">' . $newUsers . '</span>' : ''),
                        'encode' => false,
                        'url' => ['/user/users/index'],
                        'visible' => $user->role >= UserRoles::ADMIN
                    ],
                    [
                        'label' => 'Configuration',
                        'items' => [
                            [
                                'label' => 'Processing Departments',
                                'url' => ['/jobs/processing-units/index']
                            ],
                            [
                                'label' => 'Work Types',
                                'url' => ['/jobs/work-type/index']
                            ],
                            [
                                'label' => 'Agencies',
                                'url' => ['/jobs/agency/index']
                            ],
                            [
                                'label' => 'Organization Units',
                                'url' => ['/jobs/organization-units/index']
                            ],
                            [
                                'label' => 'Justifications',
                                'url' => ['/jobs/justifications/index']
                            ],
                            [
                                'label' => 'CWA',
                                'url' => ['/jobs/cwa/index']
                            ],
                            [
                                'label' => 'Custom forms',
                                'url' => ['/jobs/custom-forms/index']
                            ],
                        ],
                        'visible' => $user->role >= UserRoles::ADMIN
                    ],
                    [
                        'label' => 'Logout',
                        'url' => ['/user/auth/logout'],
                        'options' => [
                            'class' => 'pull-right'
                        ]
                    ],
                    [
                        'label' => 'Notifications ' . ($notifications > 0 ? '<span class="badge notification-badge">' . $notifications . '</span>' : ''),
                        'url' => ['/notifications/notifications/index'],
                        'encode' => false,
                        'options' => [
                            'class' => 'pull-right'
                        ],
                        'linkOptions' => [
                            'class' => 'notifications-link'
                        ]
                    ]
                ]
            ]) ?>
        </div>
    </div>
</header>
