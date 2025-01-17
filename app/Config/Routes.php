<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth routes
$routes->group('auth', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('register', 'Auth::register');
    $routes->post('register', 'Auth::doRegister');
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::doLogin');
    $routes->get('logout', 'Auth::logout');
});

// Transaction routes
$routes->group('transactions', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'Transactions::index');
    $routes->get('create', 'Transactions::create');
    $routes->post('store', 'Transactions::store');
    $routes->get('transactions', 'Transactions::index');
    $routes->get('transactions/create', 'Transactions::create');
    $routes->post('transactions/store', 'Transactions::store');
});
$routes->setAutoRoute(true);

// Product routes
$routes->get('products', 'Products::index');
$routes->get('products/new', 'Products::new');
$routes->post('products/create', 'Products::create');
$routes->get('products/edit/(:num)', 'Products::edit/$1');
$routes->post('products/update/(:num)', 'Products::update/$1');
$routes->get('products/delete/(:num)', 'Products::delete/$1');

// Category routes
$routes->group('categories', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'Categories::index');
    $routes->get('new', 'Categories::new');          
    $routes->post('create', 'Categories::create');    
    $routes->get('edit/(:num)', 'Categories::edit/$1');
    $routes->post('update/(:num)', 'Categories::update/$1');
    $routes->get('delete/(:num)', 'Categories::delete/$1');
});

//Report routes
$routes->group('reports', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'Reports::index');
    $routes->get('low-stock', 'Reports::lowStock');
    $routes->get('transactions', 'Reports::transactions');
    $routes->get('top-products', 'Reports::topProducts');
});

// Users routes (admin only)
$routes->group('users', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'Users::index');
});

// API Routes
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    // Auth routes
    $routes->post('auth/login', 'Auth::login');
    
    // Protected routes (require JWT token)
    $routes->group('', ['filter' => 'apiauth'], function($routes) {
        // Product routes
        $routes->get('products', 'Products::index');
        $routes->post('products', 'Products::create');
        $routes->get('products/(:num)', 'Products::show/$1');
        $routes->put('products/(:num)', 'Products::update/$1');
        $routes->delete('products/(:num)', 'Products::delete/$1');
        $routes->post('products/import', 'Products::import');
        $routes->get('products/export', 'Products::export');
    });
});

// Language switcher route
$routes->get('language/(:segment)', 'Language::setLanguage/$1');