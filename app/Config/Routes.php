<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Test Koneksi Database
$routes->get('/db_connection', 'TestDB::index');

// Halaman Utama/Landing Pages
$routes->get('/', 'Home::index');

// Login
$routes->get('/login', 'Login::index', ['filter' => 'guest']);
$routes->post('/login/processLogin', 'Login::processLogin', ['filter' => 'guest']);
$routes->get('/logout', 'Login::logout');
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);
