<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午3:33
 */

define("WAKA_ENV", "production");
define('WAKA_DOMAIN', 'iwakasoccer.com');

return [

    'loadPath' => [
        APP_ROOT . '/components',
        APP_ROOT . '/controllers',
        APP_ROOT . '/controllers/admin',
        APP_ROOT . '/models',
        APP_ROOT . '/models/ActiveRecord',
        APP_ROOT . '/models/ApiResponse',
        APP_ROOT . '/../extlib',
    ],

    'router' => 'SimpleRouterParser',
    // log config
    'log' => [
        'use_syslog'=>false,
        'path_prefix'=>'/var/log/waka/',
        'err_path'=>'/var/log/waka/err',
        'info_path'=>'/var/log/waka/log',
        'ct_log' => '/var/log/waka/ct_log/',
        'rotate'=>true
    ],

    // db config
    'db' => [
        'default' => [
            'host'=>'127.0.0.1',
            'port'=>3306,
            'username'=> 'root',
            'password'=>'swerfvcx',
            'db'=>'waka'
        ]
    ],

    //mongo config
//    'mongo' => [
//        'default' => [
//            'server' => 'mongodb://10.1.25.60:27017',
//            'serverRead' => 'mongodb://10.1.25.60:27017'
//        ]
//    ],

    // memcache config
//    'memcache' => [
//        'default'=>[
//            'host'=>'10.1.25.59',
//            'port'=>11211,
//            'prefix'=>'Rs_'
//        ]
//    ],

    // redis config
    'redis' => [
        'default'=>[
            'host'=>'10.1.25.59',
            'port'=>6379,
            'prefix'=>''
        ]
    ],

//    'solr' => [
//        'default'=>[
//            'host'=>'10.1.25.82',
//            'port'=>8983,
//            'prefix'=>'/solr/'
//        ],
//        'writer'=>[
//            'host'=>'10.1.25.82',
//            'port'=>8983,
//            'prefix'=>'/solr/'
//        ],
//        'comment_solr_read' => [
//            'host'=>'10.1.25.39',
//            'port'=>8983,
//            'prefix'=>'/solr/'
//        ],
//        'comment_solr_writer' => [
//            'host'=>'10.1.25.39',
//            'port'=>8983,
//            'prefix'=>'/solr/'
//        ]
//    ],

    // session config
    'session' => [
        'dir'=>'/tmp/tzls_web_sessions'
    ],

    'wechat' => [
        "TOKEN" => 'tzls_MakeThingsHappen',
        "WX_AKEY" => 'wx8e0de1168e5643a6',
        "WX_SKEY" => '42b6f5c4f387a8e9465a205b46a9468a',
    ],

];
