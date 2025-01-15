<?php
class EasySQL {
    private $conn;

    public function __construct($db_server, $db_user, $db_pass, $db_name) {
        try {
            $this->conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
            if (!$this->conn) {
                throw new Exception("Failed to connect to MySQL: " . mysqli_connect_error());
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            exit();
        }
    }

    public function db_Ins($dbTable, $data): void {
        if (!is_array($data) || empty($data)) {
            echo "Invalid data provided!";
            return;
        }

        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), '?'));
        $values = array_values($data);

        $query = "INSERT INTO `$dbTable` ($columns) VALUES ($placeholders)";

        try {
            $stmt = mysqli_prepare($this->conn, $query);
            if (!$stmt) {
                throw new Exception("Statement preparation failed: " . mysqli_error($this->conn));
            }

            $types = str_repeat("s", count($values));
            mysqli_stmt_bind_param($stmt, $types, ...$values);

            if (mysqli_stmt_execute($stmt)) {
                echo "Data inserted successfully!";
            } else {
                throw new Exception("Execution failed: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);
        } catch (Exception $e) {
            echo "An error occurred: " . $e->getMessage();
        }
    }

    public function db_Out($dbTable, $what, $where = null, $params = []): array {
        $sql = "SELECT $what FROM `$dbTable`";
        if ($where) {
            $sql .= " WHERE $where";
        }
    
        try {
            $stmt = mysqli_prepare($this->conn, $sql);
            if ($params) {
                $types = str_repeat("s", count($params));
                mysqli_stmt_bind_param($stmt, $types, ...$params);
            }
    
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Query execution failed: " . mysqli_stmt_error($stmt));
            }
    
            $result = mysqli_stmt_get_result($stmt);
            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
    
            mysqli_stmt_close($stmt);
            return $data;
        } catch (Exception $e) {
            echo "An error occurred: " . $e->getMessage();
            return [];
        }
    }
    

    public function closeConnection(): void {
        if ($this->conn) {
            mysqli_close($this->conn);
        }
    }
}
?>
