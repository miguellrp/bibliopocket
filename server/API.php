<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/server/classes/Bloqueo.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/server/classes/Estanteria.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/server/classes/Libro.php");


// "Enrutamiento" de los diferentes tipos de peticiones:

if(isset($_GET["tipoPeticion"])) {
  $tipoPeticion = $_GET["tipoPeticion"];
  switch($tipoPeticion) {
    case "getLibros":
      if(isset($_GET["idUsuario"])) {
        $idUsuario = $_GET["idUsuario"];
        $paginacion = $_GET["paginacion"];
        $limitado = $_GET["limitado"];

        getLibros($idUsuario, $paginacion, $limitado);
      }
      break;
    
      case "getBloqueos": 
        getBloqueos();
        break;

    default:
      throw new Exception($_GET["tipoPeticion"]);
  }
}


// -- "PETICIONES" de la "API" --
function getLibros($idUsuario, $paginacion, $limitado) {
  $idUsuario = $_GET["idUsuario"];
  $estanteria = new Estanteria($idUsuario);
  $numLibros = 10;

  if ($limitado) $idsLibros = $estanteria->getLibrosIds($paginacion, $numLibros);
  else $idsLibros = $estanteria->getLibrosIds($paginacion, 99999999999);

  foreach($idsLibros as $idLibro) {
    $libro = new Libro($idLibro);
    $libro->render();
  }
}

function getBloqueos() {
  $listaBloqueos = Bloqueo::getBloqueos();

  foreach($listaBloqueos as $idBloqueo => $tipoBloqueo) {
    $bloqueo = new Bloqueo($idBloqueo, $tipoBloqueo);
    $bloqueo->render();
  }
}