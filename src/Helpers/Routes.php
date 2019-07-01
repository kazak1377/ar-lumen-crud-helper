<?php
/**
 * User: kazak
 * Email: mk@altrecipe.com
 * Date: 2019-07-01
 * Time: 16:22
 */

namespace ARCrud\Helpers;


use Illuminate\Routing\Router;

class Routes {
    const CRUD = [
        'create' => [
            'method' => 'post',
            'uri' => '/create',
        ],
        'read' => [
            'method' => 'get',
            'uri' => '/{id:[0-9]+}',
        ],
        'update' => [
            'method' => 'post',
            'uri' => '/update/{id:[0-9]+}'
        ],
        'delete' => [
            'method' => 'delete',
            'uri' => '/{id:[0-9]+}'
        ],
        'restore' => [
            'method' => 'post',
            'uri' => '/restore/{id:[0-9]+}'
        ],
        'getList' => [
            'method' => 'get',
            'uri' => '/lists'
        ],
    ];

    public static function crud(Router $router) {
        foreach (self::CRUD as $action => $params) {
            $controllerAction = "Api\LottoController@{$action}";
            $router->{$params['method']}($params['uri'], $controllerAction);
        }
    }
}
