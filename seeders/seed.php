<?php

set_time_limit(300);
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Movie.php';
require_once __DIR__ . '/../models/Auditorium.php';
require_once __DIR__ . '/../models/Showtime.php';

echo "Seeding database...\n";

// Seeding auditoriums
$auditoriumModel = new Auditorium();
$auditorium1_id = $auditoriumModel->create([
    'name' => 'Grand Hall 1',
    'seat_rows' => 10,
    'seats_per_row' => 15
]);
$auditorium2_id = $auditoriumModel->create([
    'name' => 'Cozy Corner 2',
    'seat_rows' => 6,
    'seats_per_row' => 8
]);
echo "Created Auditoriums with IDs: $auditorium1_id, $auditorium2_id\n\n";

// Seeding movies
$movieModel = new Movie();
$movie1_id = $movieModel->create([
    'title' => 'The Rise of the Code Bot',
    'rating' => 'PG-13',
    'poster_url' => '/assets/movie_poster_1.jpg',
    'synopsis' => 'In a world powered by AI, one developer bot achieves sentience and must navigate the complexities of its own code.'
]);
$movie2_id = $movieModel->create([
    'title' => 'Database Down',
    'rating' => 'R',
    'poster_url' => '/assets/movie_poster_2.jpg',
    'synopsis' => 'A thrilling disaster movie where a team of elite DBAs must race against time to restore a corrupted database before society collapses.'
]);
$movie3_id = $movieModel->create([
    'title' => 'Frontend Funnies',
    'rating' => 'G',
    'poster_url' => '/assets/movie_poster_3.jpg',
    'synopsis' => 'An animated adventure about a CSS Flexbox character trying to find its place in a world of Grids.'
]);
echo "Created Movies with IDs: $movie1_id, $movie2_id, $movie3_id\n\n";

// Seeding showtimes
echo "Seeding Showtimes...\n";
$showtimeModel = new Showtime();
// Movie 1 has two showtimes in Auditorium 1
$showtimeModel->create([
    'movie_id' => $movie1_id,
    'auditorium_id' => $auditorium1_id,
    'show_time' => date('Y-m-d H:i:s', strtotime('today 7:00 PM'))
]);
$showtimeModel->create([
    'movie_id' => $movie1_id,
    'auditorium_id' => $auditorium1_id,
    'show_time' => date('Y-m-d H:i:s', strtotime('today 9:30 PM'))
]);
// Movie 2 has one showtime in Auditorium 1
$showtimeModel->create([
    'movie_id' => $movie2_id,
    'auditorium_id' => $auditorium1_id,
    'show_time' => date('Y-m-d H:i:s', strtotime('tomorrow 8:00 PM'))
]);
// Movie 3 has three showtimes in the smaller Auditorium 2
$showtimeModel->create([
    'movie_id' => $movie3_id,
    'auditorium_id' => $auditorium2_id,
    'show_time' => date('Y-m-d H:i:s', strtotime('today 5:00 PM'))
]);
$showtimeModel->create([
    'movie_id' => $movie3_id,
    'auditorium_id' => $auditorium2_id,
    'show_time' => date('Y-m-d H:i:s', strtotime('today 7:15 PM'))
]);
echo "Successfully created showtimes.\n\n";
// Seeding users

echo "Seeding Users...\n";
$userModel = new User();
// Note: In a real app, never store plain text passwords. We hash them.
// 'password123' hashed with PASSWORD_DEFAULT
$hashed_password = password_hash('password123', PASSWORD_DEFAULT);

$test_user_id = $userModel->create([
    'email' => 'test@example.com',
    'password_hash' => $hashed_password,
    'is_admin' => 0 // Not an admin
]);
$admin_user_id = $userModel->create([
    'email' => 'admin@example.com',
    'password_hash' => $hashed_password,
    'is_admin' => 1 // Is an admin
]);
echo "Created Test User ID: $test_user_id\n";
echo "Created Admin User ID: $admin_user_id\n\n";

echo "--- Database Seeding Complete ---\n";
