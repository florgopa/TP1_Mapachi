<?php

class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $dbname = 'mapachibd';
    private $conn;

    // Constructor para crear la conexión
    public function __construct() {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->dbname, 3306); 

        // Verificar si la conexión es exitosa
        if ($this->conn->connect_error) {
            die("Conexión fallida: " . $this->conn->connect_error);
        }
    }

    // Método para obtener la conexión
    public function getConnection() {
        return $this->conn;
    }

    // Método para cerrar la conexión
    public function closeConnection() {
        $this->conn->close();
    }
}

// Crear una instancia de la clase Database para establecer la conexión
$db = new Database();
$conn = $db->getConnection();

// Aquí puedo utilizar la variable $conn para interactuar con la base de datos
// No olvidar cerrar la conexión cuando ya no la necesito
// $db->closeConnection();

$sql = "SELECT * FROM propiedad";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - Nombre: " . $row['nombre'] . "<br>";
    }
} else {
    echo "0 resultados";
}
?>