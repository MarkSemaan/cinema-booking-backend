<?php

require_once __DIR__ . '/../models/Showtime.php';

class ShowtimesController
{
    //Method to check if the user is an admin
    private function isAdmin($userId)
    {
        if (!$userId) return false;
        //Create the user model
        try {
            require_once __DIR__ . '/../models/User.php';
            $user = new User();
            //Find the user by id
            $userData = $user->find((int)$userId);
            //Check if the user is an admin
            return $userData && (bool)$userData['is_admin'];
        } catch (Exception $e) {
            return false;
        }
    }
    //Method to prevent access to the API for non-admin users
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
    //Method to list all showtimes
    public function list()
    {
        try {
            //Create the showtime model and find all showtimes
            $showtime = new Showtime();
            $showtimes = $showtime->findAll();
            //Return the showtimes
            http_response_code(200);
            echo json_encode(['showtimes' => $showtimes]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve showtimes']);
        }
    }
    //Method to get a specific showtime
    public function get()
    {
        //Get the id from the request
        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid showtime ID is required']);
            return;
        }
        //Create the showtime model and find the showtime by id
        try {
            $showtime = new Showtime();
            $showtimeData = $showtime->find((int)$id);
            //Check if the showtime exists
            if ($showtimeData) {
                //If the showtime exists, return the showtime
                http_response_code(200);
                echo json_encode(['showtime' => $showtimeData]);
            } else {
                //If the showtime does not exist, return an error message
                http_response_code(404);
                echo json_encode(['error' => 'Showtime not found']);
            }
        } catch (Exception $e) {
            // Return a generic error message if showtime can't be found
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve showtime']);
        }
    }
    //Method to get all showtimes for a specific movie
    public function byMovie()
    {
        //Get the movie id from the request
        $movieId = $_GET['movie_id'] ?? null;

        if (!$movieId || !is_numeric($movieId)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid movie ID is required']);
            return;
        }
        //Create the showtime model and find all showtimes for the movie
        try {
            require_once __DIR__ . '/../connection/db_connection.php';
            $mysqli = DBConnection::getInstance()->getConnection();
            //Prepare the statement to find all showtimes for the movie
            $stmt = $mysqli->prepare("SELECT * FROM showtimes WHERE movie_id = ? ORDER BY showtime ASC");
            $stmt->bind_param("i", $movieId);
            $stmt->execute();
            $result = $stmt->get_result();
            $showtimes = $result->fetch_all(MYSQLI_ASSOC);
            //Return the showtimes
            http_response_code(200);
            echo json_encode(['showtimes' => $showtimes]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve showtimes']);
        }
    }
    //Method to create a new showtime
    public function create()
    {
        if (!$this->requireAdmin()) return;
        //Get the data from the request
        $data = json_decode(file_get_contents('php://input'), true);
        //Check if the movie id, auditorium id, and showtime are set
        if (empty($data['movie_id']) || empty($data['auditorium_id']) || empty($data['showtime'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Movie ID, auditorium ID, and showtime are required']);
            return;
        }
        //Create the showtime model and create the showtime
        try {
            $showtime = new Showtime();
            $showtimeId = $showtime->create([
                'movie_id' => (int)$data['movie_id'],
                'auditorium_id' => (int)$data['auditorium_id'],
                'showtime' => $data['showtime']
            ]);
            //Check if the showtime is created successfully
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
    //Method to update a showtime
    public function update()
    {
        if (!$this->requireAdmin()) return;
        //Get the id from the request
        $id = $_GET['id'] ?? null;
        //Check if the id is valid
        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid showtime ID is required']);
            return;
        }
        //Get the data from the request 
        $data = json_decode(file_get_contents('php://input'), true);
        //Check if the data is set
        if (empty($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'No data provided for update']);
            return;
        }
        //Create the showtime model and update the showtime
        try {
            $showtime = new Showtime();
            //Check if the showtime exists
            if (!$showtime->find((int)$id)) {
                http_response_code(404);
                echo json_encode(['error' => 'Showtime not found']);
                return;
            }
            //Create the update data and put it in an array, and check if the data is set
            $updateData = [];
            if (isset($data['movie_id'])) $updateData['movie_id'] = (int)$data['movie_id'];
            if (isset($data['auditorium_id'])) $updateData['auditorium_id'] = (int)$data['auditorium_id'];
            if (isset($data['showtime'])) $updateData['showtime'] = $data['showtime'];
            //Check if the update data is empty
            if (empty($updateData)) {
                http_response_code(400);
                echo json_encode(['error' => 'No valid fields provided for update']);
                return;
            }

            //Update the showtime, if changes were made, return a success message, otherwise return a message saying that no changes were made
            $updateResult = $showtime->update((int)$id, $updateData);
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
    //Method to delete a showtime
    public function delete()
    {
        if (!$this->requireAdmin()) return;
        //Get the id from the request
        $id = $_GET['id'] ?? null;
        //Check if the id is valid
        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid showtime ID is required']);
            return;
        }
        //Create the showtime model and delete the showtime
        try {
            $showtime = new Showtime();
            //Check if the showtime exists
            if (!$showtime->find((int)$id)) {
                http_response_code(404);
                echo json_encode(['error' => 'Showtime not found']);
                return;
            }
            //Delete the showtime
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

    // Method to delete all showtimes for a specific movie
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
