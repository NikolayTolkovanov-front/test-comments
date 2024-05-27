<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/comments/page/(:num)', 'CommentsController::index/$1');
$routes->get('/comments/page/(:num)/sort_by/(:any)/sort_order/(:any)', 'CommentsController::getComments/$1/$2/$3');
$routes->post('/comments/create', 'CommentsController::create');
$routes->delete('/comments/delete/(:num)', 'CommentsController::delete/$1');
