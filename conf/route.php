<?php
//路由规则
$routeConfigs = array(
    array(
        'type' => 'rewrite',
        'match' => '/search/',
        'route' => array(
            'module' => 'Api',
            'controller' => 'Search',
            'action' => 'Index'
        )
    ) ,
);
