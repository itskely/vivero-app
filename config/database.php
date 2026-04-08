<?php
class Database
{
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "vivero_app";

    private $conn = null;

    public function connect()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->database", $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            echo "Conexion fallida: " . $e->getMessage();
            return null;
        }
    }
}
