<?php

set_time_limit(300);

require_once __DIR__ . '/seeders/seeder.php';
require_once __DIR__ . '/seeders/AuditoriumSeeder.php';
require_once __DIR__ . '/seeders/MovieSeeder.php';
require_once __DIR__ . '/seeders/ShowtimeSeeder.php';
require_once __DIR__ . '/seeders/UserSeeder.php';
require_once __DIR__ . '/seeders/BookingSeeder.php';
require_once __DIR__ . '/seeders/BookedSeatSeeder.php';

echo "Seeding database...\n";

(new AuditoriumSeeder())->run();
(new MovieSeeder())->run();
(new ShowtimeSeeder())->run();
(new UserSeeder())->run();
(new BookingSeeder())->run();
(new BookedSeatSeeder())->run();

echo "--- Database Seeding Complete ---";
