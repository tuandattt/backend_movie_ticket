// config/Database.php
<?php
class Database {
    private $host = "localhost";
    private $db_name = "cinema_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
        return $this->conn;
    }
}

// models/Movie.php
<?php
class Movie {
    private $conn;
    private $table_name = "movies";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách phim đang chiếu
    public function getNowShowing() {
        $query = "SELECT m.*, GROUP_CONCAT(g.name) as genres 
                 FROM " . $this->table_name . " m
                 LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
                 LEFT JOIN genres g ON mg.genre_id = g.genre_id
                 WHERE m.status = 'now_showing'
                 AND EXISTS (
                     SELECT 1 FROM screenings s 
                     WHERE s.movie_id = m.movie_id 
                     AND s.screening_time >= CURRENT_DATE()
                 )
                 GROUP BY m.movie_id
                 ORDER BY m.release_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy danh sách phim sắp chiếu
    public function getComingSoon() {
        $query = "SELECT m.*, GROUP_CONCAT(g.name) as genres 
                 FROM " . $this->table_name . " m
                 LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
                 LEFT JOIN genres g ON mg.genre_id = g.genre_id
                 WHERE m.status = 'coming_soon'
                 AND m.release_date > CURRENT_DATE()
                 GROUP BY m.movie_id
                 ORDER BY m.release_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy chi tiết một phim
    public function getById($movieId) {
        $query = "SELECT m.*, GROUP_CONCAT(g.name) as genres 
                 FROM " . $this->table_name . " m
                 LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
                 LEFT JOIN genres g ON mg.genre_id = g.genre_id
                 WHERE m.movie_id = ?
                 GROUP BY m.movie_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $movieId);
        $stmt->execute();
        return $stmt;
    }
}

// models/Screening.php
<?php
class Screening {
    private $conn;
    private $table_name = "screenings";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy lịch chiếu theo phim và ngày
    public function getScreeningsByMovie($movieId, $date) {
        $query = "SELECT s.*, 
                        COUNT(DISTINCT st.seat_id) as total_seats,
                        COUNT(DISTINCT bs.booked_seat_id) as booked_seats
                 FROM " . $this->table_name . " s
                 LEFT JOIN seats st ON s.screening_id = st.screening_id
                 LEFT JOIN booked_seats bs ON st.seat_id = bs.seat_id
                 WHERE s.movie_id = ? 
                 AND DATE(s.screening_time) = ?
                 GROUP BY s.screening_id
                 ORDER BY s.screening_time ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $movieId);
        $stmt->bindParam(2, $date);
        $stmt->execute();
        return $stmt;
    }

    // Lấy các ngày có suất chiếu của một phim
    public function getAvailableDates($movieId) {
        $query = "SELECT DISTINCT DATE(screening_time) as available_date
                 FROM " . $this->table_name . "
                 WHERE movie_id = ?
                 AND screening_time >= CURRENT_DATE()
                 ORDER BY screening_time ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $movieId);
        $stmt->execute();
        return $stmt;
    }
}

// controllers/MovieController.php
<?php
class MovieController {
    private $db;
    private $movie;
    private $screening;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->movie = new Movie($this->db);
        $this->screening = new Screening($this->db);
    }

    // API để lấy danh sách phim đang chiếu và sắp chiếu
    public function getMoviesList() {
        try {
            $nowShowing = $this->movie->getNowShowing();
            $comingSoon = $this->movie->getComingSoon();

            $response = [
                'status' => 'success',
                'data' => [
                    'now_showing' => $nowShowing->fetchAll(PDO::FETCH_ASSOC),
                    'coming_soon' => $comingSoon->fetchAll(PDO::FETCH_ASSOC)
                ]
            ];

            return json_encode($response);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // API để lấy chi tiết phim và lịch chiếu
    public function getMovieSchedule($movieId, $date = null) {
        try {
            // Lấy thông tin phim
            $movieInfo = $this->movie->getById($movieId)->fetch(PDO::FETCH_ASSOC);
            if (!$movieInfo) {
                throw new Exception('Movie not found');
            }

            // Lấy các ngày có suất chiếu
            $availableDates = $this->screening->getAvailableDates($movieId)
                                            ->fetchAll(PDO::FETCH_ASSOC);

            // Nếu không có ngày cụ thể, lấy ngày đầu tiên có suất chiếu
            if (!$date && count($availableDates) > 0) {
                $date = $availableDates[0]['available_date'];
            }

            // Lấy các suất chiếu của ngày được chọn
            $screenings = $this->screening->getScreeningsByMovie($movieId, $date)
                                        ->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                'status' => 'success',
                'data' => [
                    'movie' => $movieInfo,
                    'available_dates' => $availableDates,
                    'screenings' => $screenings
                ]
            ];

            return json_encode($response);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}

// api/movies.php
<?php
header('Content-Type: application/json');
require_once '../config/Database.php';
require_once '../models/Movie.php';
require_once '../models/Screening.php';
require_once '../controllers/MovieController.php';

$controller = new MovieController();

// Router đơn giản
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($method) {
    case 'GET':
        if ($action === 'list') {
            echo $controller->getMoviesList();
        } elseif ($action === 'schedule') {
            $movieId = isset($_GET['movie_id']) ? $_GET['movie_id'] : null;
            $date = isset($_GET['date']) ? $_GET['date'] : null;
            if ($movieId) {
                echo $controller->getMovieSchedule($movieId, $date);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Movie ID required']);
            }
        }
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        break;
}
