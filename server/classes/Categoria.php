<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bibliopocket/server/database/Conector.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bibliopocket/server/handlers/Util.php");

class Categoria {
  private $id;
  private $nombre;
  private $idLibro;
  private $conexionDB;


  function __construct($nombre) {
    $this->conexionDB = new Conector;

    $this->nombre = $nombre;
  }

  // --- GETTERS ---
  function getId() {
    return $this->id;
  }
  
  function getNombre() {
    return $this->nombre;
  }

  function getIdLibro() {
    return $this->idLibro;
  }


   /* MÉTODOS DE LA CLASE CONECTORES CON DB */
   // Métodos estáticos para permitir utilizarlos sin necesidad de instanciar la clase
  static function getCategoriasDe($idLibro) {
    try {
      $conexionDB = new Conector;
      $query = $conexionDB->conn->prepare("SELECT nombre FROM categorias
        WHERE id_libro = :idLibro ORDER BY fecha_adicion asc");
      
      $query->execute(array(
        ":idLibro"          => $idLibro
      ));

      return $query->fetchAll(PDO::FETCH_COLUMN);
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al buscar las categorías asociadas al libro con ID $idLibro ". $exception->getMessage();
    }
  }

  static function vaciarCategoriasDe($idLibro) {
    try {
      $conexionDB = new Conector;
      $query = $conexionDB->conn->prepare("DELETE FROM categorias
        WHERE id_libro = :idLibro");
      
      $query->bindParam(":idLibro", $idLibro, PDO::PARAM_STR);
      $query->execute();
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al intentar eliminar todas las categorías del libro $idLibro ". $exception->getMessage();
    }
  }


  function asociarA($idLibro) {
    try {
      $query = $this->conexionDB->conn->prepare("INSERT INTO categorias
        (id, nombre, id_libro) VALUES (:id, :nombre, :idLibro)");
      
      $query->execute(array(
        ":id"               => Util::generarId(),
        ":nombre"           => $this->getNombre(),
        ":idLibro"          => $idLibro
      ));
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al añadir la categoría ". $exception->getMessage();
    }
  }

  function sinAsociarEn($idLibro) {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT COUNT(nombre) FROM categorias
        WHERE nombre = :nombre AND id_libro = :idLibro");

      $query->execute(array(
        ":nombre"   => $this->getNombre(),
        ":idLibro"  => $idLibro
      ));
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al comprobar si la categoría es repetida ". $exception->getMessage();
    }

    return $query->fetchColumn() <= 0;
  }

  function eliminarCategoriaDe($idLibro) {
    try {
      $query = $this->conexionDB->conn->prepare("DELETE FROM categorias
        WHERE nombre = :nombre AND id_libro = :idLibro");
      
      $query->execute(array(
        ":nombre"           => $this->getNombre(),
        ":idLibro"          => $idLibro
      ));
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al añadir la categoría ". $exception->getMessage();
    }
  }
}