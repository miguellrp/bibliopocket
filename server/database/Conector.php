<?php
class Conector {
    public $servername;
    public $username;
    public $password;
    public $dbname;
    public $conn;


    function __construct() {
        $this->servername = "db";
        $this->username = "adminBP";
        $this->password = "passBP";
        $this->dbname = "bibliopocketDB";

        try {
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Conexión fallida ❌" . $exception->getMessage();
        }
    }

    function __destruct() {
        $this->conn = null;
    }

    function getUsuarioActualID($userName) {
        $userID = "";

        try {
            $query = $this->conn->prepare("SELECT id FROM usuarios WHERE nombre_usuario = :userName");
            $query->bindParam(":userName", $userName);
            $query->execute();

            $userID = $query->fetchColumn(0);
        } catch (PDOException $exception) {
            echo "Ocurrió un error al tratar de conseguir el ID de usuario: " . $exception->getMessage();
        }

        return $userID;
    }

    function eliminarLibro($id) {
        try {
            $query = $this->conn->prepare("DELETE FROM libros
                WHERE id = :id");

            $query->bindParam(":id", $id, PDO::PARAM_STR);
            $query->execute();
        }
        catch (PDOException $exception) {
            echo "Ocurrió un error al intentar eliminar el libro. ". $exception->getMessage();
        }
    }
}
?>