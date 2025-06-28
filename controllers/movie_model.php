<?php

require_once __DIR__ . '/../models/movie.php';
require_once __DIR__ . '/../controllers/base_controller.php';

class MovieController extends BaseController
{
    private $movie;
    public function __construct()
    {
        $this->movie = new Movie();
    }
    public function listMovies()
    {
        $movie = $this->movie->findAll();
        $this->sendResponse($movie);
    }
    public function getMovie(int $id)
    {
        $movie = $this->movie->find($id);
        if (!$movie) {
            $this->sendError("Movie not found");
        }
        $this->sendResponse($movie);
    }
    public function createMovie()
    {
        $data = $this->getRequestData();
        if (empty($data)) {
            $this->sendError('Invalid Input Data');
        }
        try {
            $movieID = $this->movie->create($data);
            $newMovie = $this->movie->find($movieID);
            $this->sendResponse($newMovie, 201);
        } catch (Exception $e) {
            $this->sendError('Failed to create movie:' . $e->getMessage(), 500);
        }
    }
    public function update(int $id)
    {
        $data = $this->getRequestData();
        if (empty($data)) {
            $this->sendError('Invalid input data.');
        }

        try {
            // Check if movie exists
            if (!$this->movie->find($id)) {
                $this->sendError('Movie not found.', 404);
            }

            $this->movie->update($id, $data);
            $updatedMovie = $this->movie->find($id);
            $this->sendResponse($updatedMovie);
        } catch (Exception $e) {
            $this->sendError('Failed to update movie: ' . $e->getMessage(), 500);
        }
    }
    public function delete(int $id)
    {
        try {
            // Check if movie exists
            if (!$this->movie->find($id)) {
                $this->sendError('Movie not found.', 404);
            }

            $this->movie->delete($id);
            $this->sendResponse(['message' => 'Movie deleted successfully.'], 200);
        } catch (Exception $e) {
            $this->sendError('Failed to delete movie: ' . $e->getMessage(), 500);
        }
    }
}
