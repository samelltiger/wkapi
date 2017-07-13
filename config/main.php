<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-wkapi',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'wkapi\controllers',
    'modules' => [
        'v1' => [       //wkapi测试模型
            'class' => 'wkapi\modules\v1\Module',
        ],
    ],

    'components' => [
        'db' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=localhost;dbname=week',//dbname=easycollect',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8',
            ],
        'user' => [
            'identityClass' => 'wkapi\models\UserIdentity',/*'common\models\User',*/
            'enableAutoLogin' => true,
            'loginUrl'=>['/test/login'],
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
        'errorHandler' => [
            'errorAction' => 'user/error',
        ],
        'urlManager' =>[
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' =>true,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                            'b-task','department', 'group', 'month-plan', 'month-report',
                            'quarter-plan', 'quarter-report', 'role', 'role-rule', 'rule', 
                            'sorce', 's-task', 'task-check', 'task-type', 'user', 'user-department', 
                            'user-group', 'user-role', 'week-plan', 'week-report'
                            ],
                    'extraPatterns' => [
                            'GET ccc' => 'ccc',         //测试
                            'GET test' => 'test',       //测试
                            // 'GET ' => 'get-all',
                            "GET <email:[\w\d_-]+@[\w\d_-]+(\.[\w\d_-]+)+$>" => 'get-one',
                            'GET <id:\d+>' => 'get-one',
                            'POST ' => 'add',
                            'DELETE ' => 'del',
                            'PUT '  =>  'change',
                            // 'POST signin' => 'signin',
                        // 'POST login' =>'login',
                        // 'POST insert' => 'user',
                        // 'GET user-profile' => 'user-profile',
                        // 'DELETE test' => 'test',               //用于测试
                        // "GET test/<id:\d+>" => 'test',               //用于测试
                        // 'GET <id:\d+>' => 'get-one',               //用于测试
                        // // 'GET /' => 'get-all',               //用于测试
                    //     // 'GET user/<username:\.+>' => 'get-one',               //用于测试
                    //     'POST signin'  =>'signin',    //增加一个用户  (注册)
                    //     'POST login'   =>"login",     //登录
                    ],
                ],
                // [
                //     'class' => 'yii\rest\UrlRule',
                //     'controller'  =>['']
                // ],
            ],
        ],
        // 'response' =>[
        //     'class' => 'yii\web\response',
        //     'on beforeSend' => function($event){
        //         $response = $event ->sender;
        //         $response ->format = yii\web\Response::FORMAT_JSON;
        //     },
        //     'on beforeSend' =>function($event){
        //         $response = $event->sender;
        //         $code     = $response->getStatusCode();
        //         $msg      = $response->statusText;
        //         if($code == 404){
        //             !empty($response->data['message']) && $msg = $response->data['message'];
        //         }
        //         $data = [
        //             'code' => $code,
        //             'msg'  =>$msg,
        //         ];
        //         $code ==200 && $data['data'] = $response->data;
        //         $response ->data =$data;
        //         $response->format = yii\web\Response::FORMAT_JSON;
        //     }
        // ],
    ],
    'params' => $params,
    'homeUrl'=>'?r=test/index',
    'defaultRoute' =>'test',
];
