<?php

set_time_limit(300);

require_once __DIR__ . '/AuditoriumSeeder.php';
require_once __DIR__ . '/MovieSeeder.php';
require_once __DIR__ . '/ShowtimeSeeder.php';
require_once __DIR__ . '/UserSeeder.php';
require_once __DIR__ . '/BookingSeeder.php';
require_once __DIR__ . '/BookedSeatSeeder.php';

echo "Seeding database...\n";

(new AuditoriumSeeder())->run();
(new MovieSeeder())->run();
(new ShowtimeSeeder())->run();
(new UserSeeder())->run();
(new BookingSeeder())->run();
(new BookedSeatSeeder())->run();

echo "--- Database Seeding Complete ---";
