<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/server/database/Conector.php");

class Estanteria {
  private $idUsuario;
  private $libros;
  private $conexionDB;


  function __construct($idUsuario) {
    $this->conexionDB = new Conector;

    $this->idUsuario = $idUsuario;
    $this->libros = [];
  }

  // --- GETTERS ---
  function getIdUsuario() {
    return $this->idUsuario;
  }
  
  function getLibros() {
    return $this->libros;
  }


   /* MÉTODOS DE LA CLASE CONECTORES CON DB */
  function getLibrosIds($inicioRango, $limite = 10) {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT id FROM libros
        WHERE id_usuario = :idUsuario
        ORDER BY fecha_adicion DESC LIMIT :inicioRango, :limite");
      
      $query->bindParam(":idUsuario", $this->idUsuario, PDO::PARAM_STR);
      $query->bindParam(":inicioRango", $inicioRango, PDO::PARAM_INT);
      $query->bindParam(":limite", $limite, PDO::PARAM_INT);

      $query->execute();
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al cargar los libros filtrados. ". $exception->getMessage();
    }

    return $query->fetchAll(PDO::FETCH_COLUMN);
  }

  function registrarLibro($libro) {
    try {
      $query = $this->conexionDB->conn->prepare("INSERT INTO libros
        VALUES (:id, :titulo, :subtitulo, :autoria,
        :descripcion, :portada, :numPaginas, :editorial,
        :anhoPublicacion, :enlaceAPI, :estado, :fechaAdicion, :userID)");
      
      $query->execute(array(
        ":id"               => $libro->getId(),
        ":titulo"           => $libro->getTitulo(),
        ":subtitulo"        => $libro->getSubtitulo(),
        ":autoria"          => $libro->getAutoria(),
        ":descripcion"      => $libro->getDescripcion(),
        ":portada"          => $libro->getPortada(),
        ":numPaginas"       => $libro->getNumPaginas(),
        ":editorial"        => $libro->getEditorial(),
        ":anhoPublicacion"  => $libro->getAnhoPublicacion(),
        ":enlaceAPI"        => $libro->getEnlaceAPI(),
        ":estado"           => $libro->getEstado(),
        ":fechaAdicion"     => $libro->getFechaAdicion(),
        ":userID"           => $this->getIdUsuario()
      ));
      return true;
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al registrar el libro. ". $exception->getMessage();
      return false;
    }
  }

  function eliminarLibro($libro) {
    try {
        $query = $this->conexionDB->conn->prepare("DELETE FROM libros
            WHERE id = :id");
        $query->execute(array(":id" => $libro->getId()));
    }
    catch (PDOException $exception) {
        echo "Ocurrió un error al intentar eliminar el libro. ". $exception->getMessage();
    }

    return $query->rowCount() == 1;
}

  function getUltimosLibrosAnhadidos($numLibros) {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT id FROM libros
        WHERE id_usuario = :idUsuario ORDER BY fecha_adicion DESC LIMIT :numLibros");
      
      $query->bindParam(":idUsuario", $this->idUsuario, PDO::PARAM_STR);
      $query->bindParam(":numLibros", $numLibros, PDO::PARAM_INT);

      $query->execute();
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al cargar los últimos $numLibros añadidos. ". $exception->getMessage();
    }

    return $query->fetchAll(PDO::FETCH_COLUMN);
  }
}