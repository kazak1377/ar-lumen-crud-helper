<?php
/**
 * User: kazak
 * Email: mk@altrecipe.com
 * Date: 2019-07-01
 * Time: 16:22
 */

namespace ARCrud\Helpers;


use Laravel\Lumen\Routing\Router;

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
        'destroy' => [
            'method' => 'delete',
            'uri' => '/destroy/{id:[0-9]+}'
        ],
        'restore' => [
            'method' => 'post',
            'uri' => '/restore/{id:[0-9]+}'
        ],
        'getList' => [
            'method' => 'get',
            'uri' => '/lists'
        ],
        'listDeleted' => [
            'method' => 'get',
            'uri' => '/trash'
        ],
        'listByIds' => [
            'method' => 'post',
            'uri' => '/list-by-ids'
        ]
    ];

    public static function crud(Router $router, $controller) {
        foreach (self::CRUD as $action => $params) {
            $controllerAction = "{$controller}@{$action}";
            $router->{$params['method']}($params['uri'], $controllerAction);
        }
    }

    public static function uploadImage(Router $router, $controller) {
        $router->post('/upload-image',
            "{$controller}@uploadImage");
        $router->post('/delete-image',
            "{$controller}@deleteImage");
        $router->post('/upload-directly-image',
            "{$controller}@uploadWithoutResizing");
    }
}
