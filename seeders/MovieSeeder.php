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
    }
}