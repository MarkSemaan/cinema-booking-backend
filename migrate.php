<?php

require_once __DIR__ . '/migrations/base_migrator.php';
require_once __DIR__ . '/migrations/000_create_cinema_booking_db.php';
require_once __DIR__ . '/migrations/001_create_users_table.php';
require_once __DIR__ . '/migrations/002_create_movies_table.php';
require_once __DIR__ . '/migrations/003_create_auditoriums_table.php';
require_once __DIR__ . '/migrations/004_create_showtimes_table.php';
require_once __DIR__ . '/migrations/005_create_bookings_table.php';
require_once __DIR__ . '/migrations/006_create_bookedseats_table.php';


echo "Migrating database...\n";

(new database_migration())->run();
(new user_migration())->run();
(new movie_migration())->run();
(new auditorium_migration())->run();
(new showtime_migration())->run();
(new booking_migration())->run();
(new bookedseat_migration())->run();

echo "--- Database Migration Complete ---";
