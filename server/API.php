<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/server/classes/Estanteria.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/server/classes/Libro.php");


// "Enrutamiento" de los diferentes tipos de peticiones:
if(isset($_GET["idUsuario"])) {
  $idUsuario = $_GET["idUsuario"];
  if(isset($_GET["tipoPeticion"])) {
    $tipoPeticion = $_GET["tipoPeticion"];
    switch($tipoPeticion) {
      case "getLibros":
        $paginacion = $_GET["paginacion"];
        getLibros($idUsuario, $paginacion);
        break;

      default:
        throw new Exception("Tipo de petición no válida");
    }
  }
}

// -- "PETICIONES" de la "API" --
function getLibros($idUsuario, $paginacion) {
  $idUsuario = $_GET["idUsuario"];
  $estanteria = new Estanteria($idUsuario);
  $numLibros = 10;

  $idsLibros = $estanteria->getLibrosIds($paginacion, $numLibros);
  foreach($idsLibros as $idLibro) {
    $libro = new Libro($idLibro);
    $libro->render();
  }
}