<?php

require_once __DIR__ . '/../models/Movie.php';

class MoviesController
{
    //Check if user is admin
    private function isAdmin($userId)
    {
        if (!$userId) return false;

        try {
            require_once __DIR__ . '/../models/User.php';
            $user = new User();
            $userData = $user->find((int)$userId);
            return $userData && (bool)$userData['is_admin'];
        } catch (Exception $e) {
            return false;
        }
    }
    //Require admin access for create, update and delete methods
    private function requireAdmin()
    {
        // Check for user_id in GET, POST, or JSON body
        $userId = $_GET['user_id'] ?? $_POST['user_id'] ?? null;

        // If not found in GET/POST, try to get from JSON body
        if (!$userId) {
            $jsonData = json_decode(file_get_contents('php://input'), true);
            $userId = $jsonData['user_id'] ?? null;
        }

        if (!$this->isAdmin($userId)) {
            http_response_code(403);
            echo json_encode(['error' => 'Admin access required']);
            return false;
        }
        return true;
    }
    public function list()
    {
        try {
            $movie = new Movie();
            $movies = $movie->findAll();

            http_response_code(200);
            echo json_encode(['movies' => $movies]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve movies']);
        }
    }

    public function get()
    {
        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid movie ID is required']);
            return;
        }

        try {
            $movie = new Movie();
            $movieData = $movie->find((int)$id);

            if ($movieData) {
                http_response_code(200);
                echo json_encode(['movie' => $movieData]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Movie not found']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve movie']);
        }
    }

    public function create()
    {
        if (!$this->requireAdmin()) return;

        // Handle both JSON and multipart form data
        $data = [];
        $posterUrl = null;

        if ($_SERVER['CONTENT_TYPE'] && strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false) {
            // Handle multipart form data (file upload)
            $data = $_POST;

            // Handle poster file upload
            if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/posters/';

                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Generate unique filename
                $fileExtension = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . time() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $filename;

                // Validate file type (only allow images)
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['poster']['type'], $allowedTypes)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Only JPG, PNG, and GIF images are allowed']);
                    return;
                }

                // Validate file size (max 5MB)
                if ($_FILES['poster']['size'] > 5 * 1024 * 1024) {
                    http_response_code(400);
                    echo json_encode(['error' => 'File size must be less than 5MB']);
                    return;
                }

                // Move uploaded file
                if (move_uploaded_file($_FILES['poster']['tmp_name'], $uploadPath)) {
                    $posterUrl = 'uploads/posters/' . $filename;
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to upload poster file']);
                    return;
                }
            }
        } else {
            // Handle JSON data
            $data = json_decode(file_get_contents('php://input'), true);
            $posterUrl = $data['poster_url'] ?? null;
        }

        if (empty($data['title']) || empty($data['director']) || empty($data['release_year'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Title, director, and release year are required']);
            return;
        }

        try {
            $movie = new Movie();
            $movieId = $movie->create([
                'title' => $data['title'],
                'director' => $data['director'],
                'release_year' => (int)$data['release_year'],
                'genre' => $data['genre'] ?? null,
                'synopsis' => $data['synopsis'] ?? null,
                'poster_url' => $posterUrl
            ]);

            if ($movieId) {
                http_response_code(201);
                echo json_encode(['message' => 'Movie created successfully', 'movie_id' => $movieId]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create movie']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create movie']);
        }
    }

    public function update()
    {
        if (!$this->requireAdmin()) return;

        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid movie ID is required']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'No data provided for update']);
            return;
        }

        try {
            $movie = new Movie();

            // Check if movie exists
            if (!$movie->find((int)$id)) {
                http_response_code(404);
                echo json_encode(['error' => 'Movie not found']);
                return;
            }

            $updateData = [];
            if (isset($data['title'])) $updateData['title'] = $data['title'];
            if (isset($data['director'])) $updateData['director'] = $data['director'];
            if (isset($data['release_year'])) $updateData['release_year'] = (int)$data['release_year'];
            if (isset($data['genre'])) $updateData['genre'] = $data['genre'];
            if (isset($data['synopsis'])) $updateData['synopsis'] = $data['synopsis'];
            if (isset($data['poster_url'])) $updateData['poster_url'] = $data['poster_url'];

            if (empty($updateData)) {
                http_response_code(400);
                echo json_encode(['error' => 'No valid fields provided for update']);
                return;
            }


            $updateResult = $movie->update((int)$id, $updateData);


            http_response_code(200);
            if ($updateResult === false) {
                echo json_encode(['message' => 'Movie updated successfully (no changes made)']);
            } else {
                echo json_encode(['message' => 'Movie updated successfully']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function delete()
    {
        if (!$this->requireAdmin()) return;

        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid movie ID is required']);
            return;
        }

        try {
            $movie = new Movie();

            // Check if movie exists
            if (!$movie->find((int)$id)) {
                http_response_code(404);
                echo json_encode(['error' => 'Movie not found']);
                return;
            }

            // Delete related showtimes first using ShowtimesController
            require_once __DIR__ . '/ShowtimesController.php';
            $showtimesController = new ShowtimesController();
            $deletedShowtimes = $showtimesController->deleteByMovieId((int)$id);

            // Now delete the movie
            if ($movie->delete((int)$id)) {
                $message = 'Movie deleted successfully';
                if ($deletedShowtimes > 0) {
                    $message .= " (also deleted {$deletedShowtimes} related showtime(s))";
                }

                http_response_code(200);
                echo json_encode(['message' => $message]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete movie']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
