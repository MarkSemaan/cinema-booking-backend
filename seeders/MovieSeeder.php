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
            'genre' => 'Adventure',
            'poster_url' => '/assets/movie_poster_2.jpg',
            'synopsis' => 'Jake Sully lives with his newfound family formed on the extrasolar moon Pandora. Once a familiar threat returns to finish what was previously started, Jake must work with Neytiri and the army of the Na\'vi race to protect their home.'
        ]);
        $movie3_id = $movieModel->create([
            'title' => 'Spider-Man: No Way Home',
            'director' => 'Jon Watts',
            'release_year' => 2021,
            'genre' => 'Superhero',
            'poster_url' => '/assets/movie_poster_3.jpg',
            'synopsis' => 'With Spider-Man\'s identity now revealed, Peter asks Doctor Strange for help. When a spell goes wrong, dangerous foes from other worlds start to appear, forcing Peter to discover what it truly means to be Spider-Man.'
        ]);
        $movie4_id = $movieModel->create([
            'title' => 'The Dark Knight',
            'director' => 'Christopher Nolan',
            'release_year' => 2008,
            'genre' => 'Superhero',
            'poster_url' => '/assets/movie_poster_4.jpg',
            'synopsis' => 'When the menace known as the Joker emerges from his mysterious past, he wreaks havoc and chaos on the people of Gotham. The Dark Knight must accept one of the greatest psychological and physical tests of his ability to fight injustice.'
        ]);
        $movie5_id = $movieModel->create([
            'title' => 'Final Destination Bloodlines',
            'director' => 'Zach Lipovsky',
            'release_year' => 2025,
            'genre' => 'Horror',
            'poster_url' => '/assets/movie_poster_5.jpg',
            'synopsis' => 'Plagued by a violent recurring nightmare, college student Stefanie heads home to track down the one person who might be able to break the cycle and save her family from the grisly demise that inevitably awaits them all.'
        ]);
        $movie6_id = $movieModel->create([
            'title' => 'Fullstack Bootcamp',
            'director' => 'SE Factory',
            'release_year' => 2025,
            'genre' => 'Horror',
            'poster_url' => '/assets/movie_poster_6.jpg',
            'synopsis' => 'A brutal and  unforgiving bootcamp that will test your limits and push you to your breaking point, can you survive termination?'
        ]);
        echo "Created Movies with IDs: $movie1_id, $movie2_id, $movie3_id, $movie4_id, $movie5_id, $movie6_id\n\n";
    }
}
