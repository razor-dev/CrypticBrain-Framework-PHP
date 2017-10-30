<?php

return array(
    // application data
    'name'=>'CrypticBrain',
    'version'=>'1.0.0',

    // password keys settings
    'password'=>array(
        'encryption'=>true,
        'encryptAlgorithm'=>'base64', /* md5, base64 */
        'hashKey'=>'CrypticBrain',
    ),

    // default email settings
    'email'=>array(
        'mailer'=>'phpMailer', /* 'phpMail', 'phpMailer', 'smtpMailer' */
        'from'=>'info@email.me',
        'isHtml'=>true,
        'smtp'=>array(
            'auth'=>1,
            'secure'=>'ssl', /* 'ssl', 'tls', '' */
            'host'=>'smtp.gmail.com',
            'port'=>'465',
            'username'=>'',
            'password'=>'',
        ),
    ),

    // validation and captcha
    'validation'=>array(
        'csrf'=>true,
        'bruteforce'=>array('enable'=>true, 'badLogins'=>5, 'redirectDelay'=>3),
        'captcha'=>array(
            'login'=>true,
            'join'=>true,
            'recovery'=>true,
            'length'=>6,
            'fontPath'=>'/fonts/Gorillaz/gorillaz_1.ttf'
        ),
    ),

    // session settings
    'session'=>array(
        'cacheLimiter'=>'' /* private,must-revalidate */
    ),

    // cookies settings
    'cookies'=>array(
        'domain'=>'',
        'path'=>'/'
    ),

    // cache settings
    'cache'=>array(
        'enable'=>true,
        'lifetime'=>5,
        'path'=>'protected/tmp/cache/',
    ),

    // time settings
    'defaultTimeZone'=>'Asia/Tokyo',

    // application settings
    'defaultTemplate'=>'default',
    'defaultController'=>'Index',
    'defaultAction'=>'index',

    // application components
    'components'=>array(
        //'sidebar'=>array('enable'=>true, 'class'=>'Sidebar'),
    ),

    // application modules
    'modules'=>array(
        //'admin'=>array('enable'=>true, 'classes'=>array('Admin')),
    ),

    // url manager
    'urlManager'=>array(
        'urlFormat'=>'shortPath', /* get | path | shortPath */
        'rules'=>array(
            //'cryptic-page'=>'controller/action',
        ),
    ),
);