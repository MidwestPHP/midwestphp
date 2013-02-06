<?php
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'dbs.options' => array(
        'mysql_read' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'phpfreeze_callforpapers',
            'user'      => 'phpfreeze',
            'password'  => 'timbuk22',
            'charset'   => 'utf8'
        )
    )
));
