<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/server/database/Conector.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/server/handlers/Util.php");

class Admin {
  private $idAdmin;
  private $nombreAdmin;
  private $contrasenhaAdmin;
  private $conexionDB;

  function __construct($idAdmin, $nombreAdmin = null, $contrasenhaAdmin = null) {
    $this->conexionDB = new Conector;
  
    $this->idAdmin;

    if ($idAdmin == "idTemporal") {
      $this->idAdmin = $idAdmin;

      $this->nombreAdmin = $nombreAdmin;
      $this->contrasenhaAdmin = $contrasenhaAdmin;
    } else {
      $this->idAdmin          = $idAdmin;
      $camposDB               = $this->getCamposDB();

      $this->nombreAdmin      = $camposDB["nombreAdminDB"];
    }
  }

  // getCamposDB() por la posibilidad de añadir nuevos campos en la tabla de admins (como admin_pic, fecha_registro...)
  private function getCamposDB() {
    $camposDB = [];

    $queryDB = $this->conexionDB->conn->prepare("SELECT nombre_admin
    FROM admins WHERE id_admin = :idAdmin");
    $queryDB->execute(array(":idAdmin" => $this->getIdAdmin()));
    $row = $queryDB->fetch();

    $camposDB["nombreAdminDB"] = $row[0];

    return $camposDB;
  }

   // --- GETTERS ---
   function getIdAdmin() {
    return $this->idAdmin;
  }

  function getNombreAdmin() {
    return $this->nombreAdmin;
  }

  function getContrasenhaAdmin() {
    return $this->contrasenhaAdmin;
  }

  /* MÉTODOS DE LA CLASE CONECTORES CON DB */
  function loginAdminDB() {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT nombre_admin, contrasenha_admin FROM admins
      WHERE nombre_admin = :nombreAdmin");
      
      $query->bindParam(":nombreAdmin", $this->nombreAdmin, PDO::PARAM_STR);
      $query->execute();
  
      $adminEncontrado = $query->rowCount();

      if ($adminEncontrado == 1) {
        $passwordDB = $query->fetchColumn(1);
        return password_verify($this->getContrasenhaAdmin(), $passwordDB);
      }
    } catch (PDOException $exception) {
      echo "Ocurrió un error durante el login [admin]. ". $exception->getMessage();
      return false;
    }
  }

  function getAllUsuariosIDs() {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT id FROM usuarios ORDER BY fecha_ultimo_login ASC");
      
      $query->execute();
    } catch (PDOException $exception) {
      echo "Ocurrió un error al cargar los IDs de tod@s l@s usuari@s de la BD. ". $exception->getMessage();
    }

    return $query->fetchAll(PDO::FETCH_COLUMN);
  }

  function eliminarUsuario($idUsuario) {
    try {
      $query = $this->conexionDB->conn->prepare("DELETE FROM usuarios WHERE id = :idUsuario");
      
      $query->bindParam(":idUsuario", $idUsuario);
      $query->execute();

    } catch (PDOException $exception) {
      echo "Ocurrió un error al intentar eliminar al usuario con ID $idUsuario. ". $exception->getMessage();
    }

    return $query->rowCount() == 1;
  }
}