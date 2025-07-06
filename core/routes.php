<?php

// API Routes
$apis = [
    // Movies routes
    '/movies' => [
        'controller' => 'MoviesController',
        'method' => 'list'
    ],
    '/movies/get' => [
        'controller' => 'MoviesController',
        'method' => 'get'
    ],
    '/movies/create' => [
        'controller' => 'MoviesController',
        'method' => 'create'
    ],
    '/movies/update' => [
        'controller' => 'MoviesController',
        'method' => 'update'
    ],
    '/movies/delete' => [
        'controller' => 'MoviesController',
        'method' => 'delete'
    ],

    // Users routes
    '/users/register' => [
        'controller' => 'UsersController',
        'method' => 'register'
    ],
    '/users/login' => [
        'controller' => 'UsersController',
        'method' => 'login'
    ],

    // Showtimes routes
    '/showtimes' => [
        'controller' => 'ShowtimesController',
        'method' => 'list'
    ],
    '/showtimes/get' => [
        'controller' => 'ShowtimesController',
        'method' => 'get'
    ],
    '/showtimes/by-movie' => [
        'controller' => 'ShowtimesController',
        'method' => 'byMovie'
    ],
    '/showtimes/create' => [
        'controller' => 'ShowtimesController',
        'method' => 'create'
    ],
    '/showtimes/update' => [
        'controller' => 'ShowtimesController',
        'method' => 'update'
    ],
    '/showtimes/delete' => [
        'controller' => 'ShowtimesController',
        'method' => 'delete'
    ],

    // Auditoriums routes
    '/auditoriums' => [
        'controller' => 'AuditoriumsController',
        'method' => 'list'
    ],
    '/auditoriums/get' => [
        'controller' => 'AuditoriumsController',
        'method' => 'get'
    ],
    '/auditoriums/create' => [
        'controller' => 'AuditoriumsController',
        'method' => 'create'
    ],

    // Bookings routes
    '/bookings/create' => [
        'controller' => 'BookingsController',
        'method' => 'create'
    ],
    '/bookings/get' => [
        'controller' => 'BookingsController',
        'method' => 'get'
    ],
    '/bookings/cancel' => [
        'controller' => 'BookingsController',
        'method' => 'cancel'
    ],
    '/bookings/user' => [
        'controller' => 'BookingsController',
        'method' => 'userBookings'
    ],
    '/bookings/available-seats' => [
        'controller' => 'BookingsController',
        'method' => 'availableSeats'
    ]
];
