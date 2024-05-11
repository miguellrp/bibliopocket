<?php

class Rol {
  private $id;
  private $idUsuario;
  private $pAnhadirLibros;
  private $pConsultarApiExterna;
  private $conexionDB;

  function __construct($idUsuario) {
    $this->conexionDB = new Conector;
    $camposDB = $this->getCamposDB($idUsuario);

    $this->idUsuario = $idUsuario;
    $this->id = $camposDB["id"];
    $this->pAnhadirLibros = $camposDB["p_anhadir_libros"];
    $this->pConsultarApiExterna = $camposDB["p_consultar_api_externa"];
  }

  private function getCamposDB($idUsuario) {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT * FROM roles
        WHERE id_usuario = :idUsuario");
      
      $query->bindParam(":idUsuario", $idUsuario);
      $query->execute();
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al identificar los permisos de la persona usuaria ". $exception->getMessage();
    }

    return $query->fetch(PDO::FETCH_ASSOC);
  }

  // --- GETTERS ---
  function getId() {
    return $this->id;
  }

  function getIdUsuario() {
    return $this->idUsuario;
  }

  function getPAnhadirLibros() {
    return $this->pAnhadirLibros;
  }

  function getPConsultarApiExterna() {
    return $this->pConsultarApiExterna;
  }

  function getPermisosAsCustomProperties() {
    $arrayPermisos = [
      "p-anhadir-libros"        => $this->getPAnhadirLibros() ? "true" : "false",
      "p-consultar-api-externa" => $this->getPConsultarApiExterna() ? "true" : "false",
    ];

    $permisosAsCustomProperties = "";
    foreach($arrayPermisos as $nombrePermiso => $valorPermiso) {
      $permisosAsCustomProperties .= "$nombrePermiso='$valorPermiso'";
    }

    return $permisosAsCustomProperties;
  }

  function editarPermisos(bool $pAnhadirLibros, bool $pConsultarApiExterna) {
    $this->pAnhadirLibros       = $pAnhadirLibros;
    $this->pConsultarApiExterna = $pConsultarApiExterna;

    try {
      $query = $this->conexionDB->conn->prepare("UPDATE roles SET
      p_anhadir_libros = :pAnhadirLibros,
      p_consultar_api_externa = :pConsultarApiExterna
      WHERE id_usuario = :idUsuario");

      $query->execute(array(
        ":pAnhadirLibros"       => (int)$this->getPAnhadirLibros(),
        ":pConsultarApiExterna" => (int)$this->getPConsultarApiExterna(),  
        ":idUsuario"            => $this->getIdUsuario()
      ));
      return true;
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al recoger tratar de actualizar los permisos de la persona usuaria". $exception->getMessage();
    }
    return false;
  }
}