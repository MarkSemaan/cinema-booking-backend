<?php

require_once __DIR__ . '/../models/Showtime.php';

class ShowtimesController
{
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

    private function requireAdmin()
    {
        $userId = $_GET['user_id'] ?? $_POST['user_id'] ?? null;
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
            $showtime = new Showtime();
            $showtimes = $showtime->findAll();

            http_response_code(200);
            echo json_encode(['showtimes' => $showtimes]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve showtimes']);
        }
    }

    public function get()
    {
        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid showtime ID is required']);
            return;
        }

        try {
            $showtime = new Showtime();
            $showtimeData = $showtime->find((int)$id);

            if ($showtimeData) {
                http_response_code(200);
                echo json_encode(['showtime' => $showtimeData]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Showtime not found']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve showtime']);
        }
    }

    public function byMovie()
    {
        $movieId = $_GET['movie_id'] ?? null;

        if (!$movieId || !is_numeric($movieId)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid movie ID is required']);
            return;
        }

        try {
            require_once __DIR__ . '/../connection/db_connection.php';
            $mysqli = DBConnection::getInstance()->getConnection();

            $stmt = $mysqli->prepare("SELECT * FROM showtimes WHERE movie_id = ? ORDER BY showtime ASC");
            $stmt->bind_param("i", $movieId);
            $stmt->execute();
            $result = $stmt->get_result();
            $showtimes = $result->fetch_all(MYSQLI_ASSOC);

            http_response_code(200);
            echo json_encode(['showtimes' => $showtimes]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve showtimes']);
        }
    }

    public function create()
    {
        if (!$this->requireAdmin()) return;

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['movie_id']) || empty($data['auditorium_id']) || empty($data['showtime'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Movie ID, auditorium ID, and showtime are required']);
            return;
        }

        try {
            $showtime = new Showtime();
            $showtimeId = $showtime->create([
                'movie_id' => (int)$data['movie_id'],
                'auditorium_id' => (int)$data['auditorium_id'],
                'showtime' => $data['showtime']
            ]);

            if ($showtimeId) {
                http_response_code(201);
                echo json_encode(['message' => 'Showtime created successfully', 'showtime_id' => $showtimeId]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create showtime']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create showtime']);
        }
    }

    public function update()
    {
        if (!$this->requireAdmin()) return;

        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid showtime ID is required']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'No data provided for update']);
            return;
        }

        try {
            $showtime = new Showtime();

            // Check if showtime exists
            if (!$showtime->find((int)$id)) {
                http_response_code(404);
                echo json_encode(['error' => 'Showtime not found']);
                return;
            }

            $updateData = [];
            if (isset($data['movie_id'])) $updateData['movie_id'] = (int)$data['movie_id'];
            if (isset($data['auditorium_id'])) $updateData['auditorium_id'] = (int)$data['auditorium_id'];
            if (isset($data['showtime'])) $updateData['showtime'] = $data['showtime'];

            if (empty($updateData)) {
                http_response_code(400);
                echo json_encode(['error' => 'No valid fields provided for update']);
                return;
            }

            // The update method returns false if no rows were affected, but query might still be successful
            $updateResult = $showtime->update((int)$id, $updateData);

            // Always return success if no exception was thrown
            http_response_code(200);
            if ($updateResult === false) {
                echo json_encode(['message' => 'Showtime updated successfully (no changes made)']);
            } else {
                echo json_encode(['message' => 'Showtime updated successfully']);
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
            echo json_encode(['error' => 'Valid showtime ID is required']);
            return;
        }

        try {
            $showtime = new Showtime();

            // Check if showtime exists
            if (!$showtime->find((int)$id)) {
                http_response_code(404);
                echo json_encode(['error' => 'Showtime not found']);
                return;
            }

            if ($showtime->delete((int)$id)) {
                http_response_code(200);
                echo json_encode(['message' => 'Showtime deleted successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete showtime']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete showtime']);
        }
    }

    // Method for MoviesController to call when deleting a movie
    public function deleteByMovieId(int $movieId)
    {
        try {
            require_once __DIR__ . '/../connection/db_connection.php';
            $mysqli = DBConnection::getInstance()->getConnection();

            $stmt = $mysqli->prepare("DELETE FROM showtimes WHERE movie_id = ?");
            $stmt->bind_param("i", $movieId);

            if ($stmt->execute()) {
                return $stmt->affected_rows;
            } else {
                throw new Exception("Error deleting showtimes: " . $stmt->error);
            }
        } catch (Exception $e) {
            throw new Exception("Failed to delete showtimes for movie: " . $e->getMessage());
        }
    }
}
