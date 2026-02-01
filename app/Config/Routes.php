<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->post('auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('dashboard', 'Dashboard::index');
$routes->get('logout', 'Auth::logout');

$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('users', 'Admin::index');
    $routes->get('users/create', 'Admin::create');
    $routes->post('users/store', 'Admin::store');
    $routes->get('users/edit/(:num)', 'Admin::edit/$1');
    $routes->post('users/update/(:num)', 'Admin::update/$1');
    $routes->get('users/delete/(:num)', 'Admin::delete/$1');
    $routes->get('audit-logs', 'Admin::auditLogs');

    // Department Management
    $routes->get('departments', 'Departments::index');
    $routes->post('departments/update-manager', 'Departments::updateManager');
});

$routes->group('onboarding', ['filter' => 'auth'], function ($routes) {
    $routes->get('create', 'Onboarding::create');
    $routes->post('store', 'Onboarding::store');
    $routes->get('pending', 'Onboarding::pending');
    $routes->get('facility-tasks', 'Onboarding::facilitatorTasks');
    $routes->post('update-section-status', 'Onboarding::updateSectionStatus');
    $routes->get('fill-form/(:num)', 'Onboarding::fillForm/$1');
    $routes->post('save-form/(:num)', 'Onboarding::saveForm/$1');
});

$routes->group('inventory', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Inventory::index');
    $routes->post('store', 'Inventory::store');
    $routes->get('items', 'Inventory::items');
    $routes->get('view/(:num)', 'Inventory::view/$1');
    $routes->post('update', 'Inventory::updateAsset');
    $routes->get('generate-qr/(:num)', 'Inventory::generateQR/$1');
    $routes->get('get-details/(:num)', 'Inventory::getDetails/$1');
    $routes->get('scan', 'Inventory::scan');
});
