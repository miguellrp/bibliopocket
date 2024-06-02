<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/server/classes/Bloqueo.php");

// Clase con métodos estáticos con utilidades que se podrán emplear en diversas partes del código
class Util {
  static function generarId() {
    // ID único a partir de: https://stackoverflow.com/questions/29235481/generate-a-readable-random-unique-id
    return join('-', str_split(bin2hex(openssl_random_pseudo_bytes(10)), 5));
  }
  
  static function anhadirDiferenciaId () {
    return "-".bin2hex(openssl_random_pseudo_bytes(2));
  }

  static function getExtensionArchivo($nombreArchivo) {
    $infoArchivo = pathinfo($nombreArchivo);
    
    return strtolower($infoArchivo['extension']);
}

  static function mostrarPantallaUsuarioBloqueado($idUsuario) {
    $listaBloqueosUsuario = Bloqueo::getBloqueosDe($idUsuario);
    $razonesBloqueoHTML = "";

    foreach($listaBloqueosUsuario as $bloqueo) {
      $fechaExpiracion = date_create($bloqueo["fecha_expiracion"]);
      $fechaExpiracionStr = $fechaExpiracion->format("d-m-Y")." a las ".$fechaExpiracion->format("H:m");
      $bloqueo = new Bloqueo($bloqueo["id_bloqueo"]);

      $razonesBloqueoHTML .= "
      <li>".$bloqueo->getDescripcion()."
        <ul>
          <li>Expira el: <span>".$fechaExpiracionStr."</span></li>
        </ul>
      </li>";
    }

    echo "
      <section class='acceso-bloqueado'>
        <h2>⛔ Acceso no permitido ⛔</h2>
        <p>Tu cuenta ha sido <strong>bloqueada temporalmente</strong> de <i>BiblioPocket</i> por razones de: </p>
        <ul>".
          $razonesBloqueoHTML
        ."</ul>
        <p class='extra-info'>Vuelve a <a href='/index.php'>iniciar sesión</a> cuando los bloqueos hayan expirado</p>
      </section>
    ";
  }
}