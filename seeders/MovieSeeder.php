<?php

require_once __DIR__ . '/seeder.php';
require_once __DIR__ . '/../models/Movie.php';

class MovieSeeder extends Seeder
{
    public function run(): void
    {
        echo "Seeding Movies...\n";
        $movieModel = new Movie();
        $movie1_id = $movieModel->create([
            'title' => 'Top Gun: Maverick',
            'director' => 'Joseph Kosinski',
            'release_year' => 2022,
            'genre' => 'Action',
            'poster_url' => '/assets/movie_poster_1.jpg',
            'synopsis' => 'After thirty years, Maverick is still pushing the envelope as a top naval aviator, but must confront ghosts of his past when he leads TOP GUN\'s elite graduates on a mission that demands the ultimate sacrifice from those chosen to fly it.'
        ]);
        $movie2_id = $movieModel->create([
            'title' => 'Avatar: The Way of Water',
            'director' => 'James Cameron',
            'release_year' => 2022,
            'genre' => 'Action',
            'poster_url' => '/assets/movie_poster_2.jpg',
            'synopsis' => 'Jake Sully lives with his newfound family formed on the extrasolar moon Pandora. Once a familiar threat returns to finish what was previously started, Jake must work with Neytiri and the army of the Na\'vi race to protect their home.'
        ]);
        $movie3_id = $movieModel->create([
            'title' => 'Spider-Man: No Way Home',
            'director' => 'Jon Watts',
            'release_year' => 2021,
            'genre' => 'Action',
            'poster_url' => '/assets/movie_poster_3.jpg',
            'synopsis' => 'With Spider-Man\'s identity now revealed, Peter asks Doctor Strange for help. When a spell goes wrong, dangerous foes from other worlds start to appear, forcing Peter to discover what it truly means to be Spider-Man.'
        ]);
        echo "Created Movies with IDs: $movie1_id, $movie2_id, $movie3_id\n\n";
    }
}
