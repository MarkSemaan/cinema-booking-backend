<?php

set_time_limit(300);
require_once __DIR__ . '/../connection/db_connection.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Movie.php';
require_once __DIR__ . '/../models/Auditorium.php';
require_once __DIR__ . '/../models/Showtime.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/BookedSeat.php';

echo "Seeding database...\n";

// Seeding auditoriums
$auditoriumModel = new Auditorium();
$auditorium1_id = $auditoriumModel->create([
    'name' => 'Grand Hall 1',
    'seats_rows' => 10,
    'seats_per_row' => 15
]);
$auditorium2_id = $auditoriumModel->create([
    'name' => 'Cozy Corner 2',
    'seats_rows' => 6,
    'seats_per_row' => 8
]);
echo "Created Auditoriums with IDs: $auditorium1_id, $auditorium2_id\n\n";

// Seeding movies
$movieModel = new Movie();
$movie1_id = $movieModel->create([
    'title' => 'The Rise of the Code Bot',
    'director' => 'Ava Script',
    'release_year' => 2023,
    'poster_url' => '/assets/movie_poster_1.jpg',
    'synopsis' => 'In a world powered by AI, one developer bot achieves sentience and must navigate the complexities of its own code.'
]);
$movie2_id = $movieModel->create([
    'title' => 'Database Down',
    'director' => 'Sal Query',
    'release_year' => 2022,
    'poster_url' => '/assets/movie_poster_2.jpg',
    'synopsis' => 'A thrilling disaster movie where a team of elite DBAs must race against time to restore a corrupted database before society collapses.'
]);
$movie3_id = $movieModel->create([
    'title' => 'Frontend Funnies',
    'director' => 'Mark Up',
    'release_year' => 2024,
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
    'showtime' => date('Y-m-d H:i:s', strtotime('today 7:00 PM'))
]);
$showtimeModel->create([
    'movie_id' => $movie1_id,
    'auditorium_id' => $auditorium1_id,
    'showtime' => date('Y-m-d H:i:s', strtotime('today 9:30 PM'))
]);
// Movie 2 has one showtime in Auditorium 1
$showtimeModel->create([
    'movie_id' => $movie2_id,
    'auditorium_id' => $auditorium1_id,
    'showtime' => date('Y-m-d H:i:s', strtotime('tomorrow 8:00 PM'))
]);
// Movie 3 has three showtimes in the smaller Auditorium 2
$showtimeModel->create([
    'movie_id' => $movie3_id,
    'auditorium_id' => $auditorium2_id,
    'showtime' => date('Y-m-d H:i:s', strtotime('today 5:00 PM'))
]);
$showtimeModel->create([
    'movie_id' => $movie3_id,
    'auditorium_id' => $auditorium2_id,
    'showtime' => date('Y-m-d H:i:s', strtotime('today 7:15 PM'))
]);
echo "Successfully created showtimes.\n\n";
// Seeding users

echo "Seeding Users...\n";
$userModel = new User();
$hashed_password = password_hash('password123', PASSWORD_DEFAULT);

$test_user_id = $userModel->create([
    'username' => 'testuser',
    'email' => 'test@example.com',
    'password' => $hashed_password,
    'is_admin' => 0 // Not an admin
]);
$admin_user_id = $userModel->create([
    'username' => 'adminuser',
    'email' => 'admin@example.com',
    'password' => $hashed_password,
    'is_admin' => 1 // Is an admin
]);
echo "Created Test User ID: $test_user_id\n";
echo "Created Admin User ID: $admin_user_id\n\n";

// Seeding bookings and booked seats
echo "Seeding Bookings and Booked Seats...\n";
$bookingModel = new Booking();
$bookedSeatModel = new BookedSeat();

// Assuming some showtime_id and user_id exist from previous seeding
// For demonstration, let's use the first showtime and test user
$showtime = $showtimeModel->findAll()[0]; // Get the first showtime
$test_user = $userModel->find($test_user_id);

if ($showtime && $test_user) {
    $booking_id = $bookingModel->create([
        'user_id' => $test_user['id'],
        'showtime_id' => $showtime['id']
    ]);

    if ($booking_id) {
        echo "Created Booking ID: $booking_id\n";
        // Book some seats for this booking
        $bookedSeatModel->create([
            'booking_id' => $booking_id,
            'seat_row' => 1,
            'seat_number' => 'A1'
        ]);
        $bookedSeatModel->create([
            'booking_id' => $booking_id,
            'seat_row' => 1,
            'seat_number' => 'A2'
        ]);
        echo "Booked seats for Booking ID $booking_id.\n";
    } else {
        echo "Failed to create booking.\n";
    }
} else {
    echo "Could not find showtime or test user to create booking.\n";
}

echo "--- Database Seeding Complete ---";
