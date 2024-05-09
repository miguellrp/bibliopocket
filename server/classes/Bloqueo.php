<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/server/database/Conector.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/server/handlers/Util.php");

class Bloqueo {
  private $id;
  private $tipo;
  private $idUsuario;
  private $conexionDB;

  function __construct($id) {
    $this->conexionDB = new Conector;
  
    $this->id = $id;
    $this->tipo = $this->getTipoDB();
  }

   // --- GETTERS ---
   function getId() {
    return $this->id;
  }

  function getTipo() {
    return $this->tipo;
  }

  function getIdUsuario() {
    return $this->idUsuario;
  }

  // GETTER que se define desde front (TODO: portabilidad a nuevos idiomas)
  function getDescripcion() {
    $descripcion = "";

    switch($this->tipo) {
      case 1:
        $descripcion = "Violencia y amenazas";
        break;
      case 2:
        $descripcion = "Difusión de información privada sin autorización";
        break;
      case 3:
        $descripcion = "Incitación al odio";
        break;
      case 4:
        $descripcion = "Acoso";
        break;
      case 5:
        $descripcion = "Infracción de la ley";
    }

    return $descripcion;
  }

  function render() {
    echo "
      <option value=".$this->getId().">".$this->getDescripcion()."</option>
    ";
  }

  /* MÉTODOS DE LA CLASE CONECTORES CON DB */
  function asociarA($idUsuario, $fechaExpiracion) {
    $queryOk = false;

    try {
      $query = $this->conexionDB->conn->prepare("INSERT INTO bloqueos_usuarios
        VALUES (:id, :idBloqueo, :idUsuario, :fechaExpiracion)");
      
      $query->execute(array(
        ":id"               => Util::generarId(),
        ":idBloqueo"        => $this->getId(),
        ":idUsuario"        => $idUsuario,
        ":fechaExpiracion"  => $fechaExpiracion
      ));

      $queryOk = true;
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al asociar el bloqueo al usuario con ID $idUsuario". $exception->getMessage();
    }

    return $queryOk;
  }

  function getTipoDB() {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT tipo FROM bloqueos
        WHERE id = :idBloqueo");
      
      $query->bindParam(":idBloqueo", $this->id);
      $query->execute();
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al tratar de recoger el tipo del motivo de bloqueo con ID $this->id". $exception->getMessage();
    }

    return $query->fetchColumn();
  }


  static function getBloqueosDe($idUsuario, $incluirExpiradas = false) {
    if ($incluirExpiradas) $queryStr = "SELECT * FROM bloqueos_usuarios WHERE id_usuario = :idUsuario";
    else $queryStr = "SELECT * FROM bloqueos_usuarios WHERE id_usuario = :idUsuario AND fecha_expiracion >= NOW()";

    try {
      $conexionDB = new Conector;
      $query = $conexionDB->conn->prepare($queryStr);
      
      $query->bindParam(":idUsuario", $idUsuario);
      $query->execute();
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al recoger todas las infracciones del usuario con ID $idUsuario". $exception->getMessage();
    }
    
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  static function getBloqueos() {
    try {
      $conexionDB = new Conector;
      $query = $conexionDB->conn->prepare("SELECT id, tipo FROM bloqueos");
      $query->execute();
      return $query->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al recoger todos los motivos de bloqueo de la BD". $exception->getMessage();
    }

  }
}