<?php

require_once __DIR__ . '/seeder.php';
require_once __DIR__ . '/../models/Showtime.php';
require_once __DIR__ . '/../models/Movie.php';
require_once __DIR__ . '/../models/Auditorium.php';

class ShowtimeSeeder extends Seeder
{
    public function run(): void
    {
        echo "Seeding Showtimes...\n";
        $showtimeModel = new Showtime();
        $movieModel = new Movie();
        $auditoriumModel = new Auditorium();

        // Assuming movie and auditorium data already exists from previous seeders
        $movie1_id = $movieModel->findAll()[0]['id']; // Get first movie ID
        $movie2_id = $movieModel->findAll()[1]['id']; // Get second movie ID
        $movie3_id = $movieModel->findAll()[2]['id']; // Get third movie ID
        $auditorium1_id = $auditoriumModel->findAll()[0]['id']; // Get first auditorium ID
        $auditorium2_id = $auditoriumModel->findAll()[1]['id']; // Get second auditorium ID

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
    }
}