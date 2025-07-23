<?php

declare(strict_types=1);

$router->get('/', 'ShopController@index');
$router->get('/admin', 'DashboardController@index');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->get('/register', 'RegisterController@showRegister');
$router->post('/register', 'RegisterController@register');

$router->group('/produtos', function($router) {
    $router->get('/', 'ProductController@index');
    $router->get('/create', 'ProductController@create');
    $router->post('/create', 'ProductController@store');
    $router->get('/edit/{id}', 'ProductController@edit');
    $router->post('/edit/{id}', 'ProductController@update');
    $router->post('/delete/{id}', 'ProductController@delete');
});

$router->group('/clientes', function($router) {
    $router->get('/', 'CustomerController@index');
    $router->get('/create', 'CustomerController@create');
    $router->post('/create', 'CustomerController@store');
    $router->get('/edit/{id}', 'CustomerController@edit');
    $router->post('/edit/{id}', 'CustomerController@update');
    $router->post('/delete/{id}', 'CustomerController@delete');
});

$router->group('/fornecedores', function($router) {
    $router->get('/', 'SupplierController@index');
    $router->get('/create', 'SupplierController@create');
    $router->post('/create', 'SupplierController@store');
    $router->get('/edit/{id}', 'SupplierController@edit');
    $router->post('/edit/{id}', 'SupplierController@update');
    $router->post('/delete/{id}', 'SupplierController@delete');
});

$router->group('/vendas', function($router) {
    $router->get('/', 'SaleController@index');
    $router->get('/create', 'SaleController@create');
    $router->post('/create', 'SaleController@store');
    $router->get('/view/{id}', 'SaleController@show');
});

$router->group('/usuarios', function($router) {
    $router->get('/', 'UserController@index');
    $router->get('/create', 'UserController@create');
    $router->post('/create', 'UserController@store');
    $router->get('/edit/{id}', 'UserController@edit');
    $router->post('/edit/{id}', 'UserController@update');
    $router->post('/delete/{id}', 'UserController@delete');
});

$router->group('/relatorios', function($router) {
    $router->get('/vendas', 'ReportController@sales');
    $router->get('/estoque', 'ReportController@stock');
    $router->get('/financeiro', 'ReportController@financial');
});

$router->group('/admin', function($router) {
    $router->get('/404-logs', 'AdminController@show404Logs');
    $router->get('/404-logs/{ticketId}', 'AdminController@show404LogDetail');
    $router->get('/sessions', 'AdminController@showActiveSessions');
    $router->get('/sessions/end/{sessionId}', 'AdminController@endSession');
    $router->get('/users/stats/{userId}', 'AdminController@getUserStats');
});

$router->group('/shop', function($router) {
    $router->get('/', 'ShopController@index');
    $router->post('/add-to-cart', 'ShopController@addToCart');
    $router->get('/cart', 'ShopController@cart');
    $router->post('/update-cart', 'ShopController@updateCart');
    $router->post('/apply-coupon', 'ShopController@applyCoupon');
    $router->get('/remove-coupon', 'ShopController@removeCoupon');
    $router->get('/checkout', 'ShopController@checkout');
    $router->post('/get-cep', 'ShopController@getCep');
    $router->post('/process-order', 'ShopController@processOrder');
    $router->get('/success/{orderId}', 'ShopController@success');
});

$router->post('/webhook/order-status', 'WebhookController@updateOrderStatus');
$router->get('/webhook/test', 'WebhookController@test');

$router->get('/migrate/run', 'MigrationController@run');
$router->get('/migrate/status', 'MigrationController@status');

$router->group('/first-run', function($router) {
    $router->get('/', 'FirstRunController@index');
    $router->get('/database', 'FirstRunController@database');
    $router->post('/database', 'FirstRunController@database');
    $router->get('/import', 'FirstRunController@import');
    $router->post('/import', 'FirstRunController@import');
    $router->get('/complete', 'FirstRunController@complete');
});

$router->group('/admin/backup', function($router) {
    $router->get('/', 'BackupController@index');
    $router->post('/create', 'BackupController@create');
    $router->get('/download/{filename}', 'BackupController@download');
    $router->post('/upload', 'BackupController@upload');
    $router->post('/restore', 'BackupController@restore');
    $router->post('/delete/{filename}', 'BackupController@delete');
});
