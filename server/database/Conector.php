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

    function getAdminActualID($adminName) {
        $adminID = "";

        try {
            $query = $this->conn->prepare("SELECT id_admin FROM admins WHERE nombre_admin = :adminName");
            $query->bindParam(":adminName", $adminName);
            $query->execute();

            $adminID = $query->fetchColumn(0);
        } catch (PDOException $exception) {
            echo "Ocurrió un error al tratar de conseguir el ID de admin: " . $exception->getMessage();
        }

        return $adminID;
    }
}