<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT']."/server/classes/Admin.php";
include_once $_SERVER['DOCUMENT_ROOT']."/server/classes/Bloqueo.php";
include_once $_SERVER['DOCUMENT_ROOT']."/server/classes/Rol.php";
include_once $_SERVER['DOCUMENT_ROOT']."/server/classes/Usuario.php";

if (isset($_SESSION["adminActivo"])) {
  $adminActivo = new Admin($_SESSION["adminActivo"]["idAdmin"]);
}

if (!isset($_SESSION["toast"])) $_SESSION["toast"]["showToast"] = false;

if (isset($adminActivo)) {
  if (!isset($listaUsuariosBPids)) {
    $listaUsuariosBPids = $adminActivo->getAllUsuariosIDs();
  }

  if (!empty($_POST)) {
    if (isset($_POST["editar-permisos-usuario"])) {
      $rolUsuario = new Rol($_POST["idUsuario"]);

    if ($rolUsuario->editarPermisos(
      pAnhadirLibros: ($_POST["pAnhadirLibros"] == "true"),
      pConsultarApiExterna: ($_POST["pConsultarApiExterna"] == "true"))) {
        $_SESSION["toast"]["tipo"] = "ok";
        $_SESSION["toast"]["mensaje"] = "Permisos actualizados correctamente";
      } else {
        $_SESSION["toast"]["tipo"] = "error";
        $_SESSION["toast"]["mensaje"] = "Ha ocurrido un error al tratar de actualizar los permisos de la persona usuaria";
      }
    }

    else if (isset($_POST["bloquear-usuario"])) {
      $bloqueEstablecido = new Bloqueo($_POST["motivo-bloqueo"]);
  
      if ($bloqueEstablecido->asociarA($_POST["idUsuario"], $_POST["fechaExpiracion"])) {
        $_SESSION["toast"]["tipo"] = "ok";
        $_SESSION["toast"]["mensaje"] = "Usuari@ bloquead@ temporalmente de BiblioPocket";
      } else {
        $_SESSION["toast"]["tipo"] = "error";
        $_SESSION["toast"]["mensaje"] = "Ha ocurrido un error al tratar de bloquear a la persona usuaria";
      }
    }

    if(isset($_POST["eliminar-usuario"])) {
      if ($adminActivo->eliminarUsuario($_POST["idUsuario"])) {
        $_SESSION["toast"]["tipo"] = "ok";
        $_SESSION["toast"]["mensaje"] = "Usuari@ eliminad@ correctamente de la base de datos";
      } else {
        $_SESSION["toast"]["tipo"] = "error";
        $_SESSION["toast"]["mensaje"] = "Ha ocurrido un error al tratar de eliminar a la persona usuaria de la base de datos";
      }
    }

    $_SESSION["toast"]["showToast"] = true;
    header("Location: index.php");
    session_write_close();
  }
}
  

?>
<!DOCTYPE html>
<html lang="es-ES">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioPocket | Panel de admin </title>
    <link rel="icon" type="image/png" href="/client/assets/images/favicon.png">
    <link rel="stylesheet" href="/client/styles/globals.css">
    <link rel="stylesheet" href="./styles.css">
    <script src="/client/components/CustomHeader.js"></script>
    <script src="/client/components/CustomSwitcher.js"></script>
    <script src="/client/components/CustomToast.js"></script>
  </head>

  <body>
    <?php if (!isset($adminActivo)): ?>
      <div class="caja-contenido" style="margin: 0 30px; text-wrap: balance; text-align: center">
        <h2>👀 ¿A dónde quieres ir?</h2>
        <h3>Parece que primero tienes que <a href="/index.php">iniciar sesión</a></h3>
        <small>(E intentaste hacerte pasar por admin 🥸)</small>
      </div>
    <?php else: ?>
      <custom-header site-type="admin"></custom-header>
      <h1>🔐 Panel de admin</h1>
      <div class="panel">
        <table>
          <tr>
            <th>Usuari@</th>
            <th>Correo electrónico</th>
            <th>Días<br/>inactivo</th>
            <th>Infracciones<br/>cometidas</th>
            <th>Libros añadidos<br/> a estantería</th>
            <th colspan="3">Opciones</th>
          </tr>
          <?php
            foreach($listaUsuariosBPids as $usuarioBPid) {
              $usuarioBP = new Usuario($usuarioBPid);
              $rolUsuario = new Rol($usuarioBPid);
              $listaPermisos = $rolUsuario->getPermisosAsCustomProperties();

              $diaActual = new DateTime;
              $diasInactivo = $diaActual->diff(new DateTime($usuarioBP->getUltimoLogin()))->days;
              $numInfracciones = count(Bloqueo::getBloqueosDe($usuarioBP->getId(), true));

              echo "
              <tr>
                <td data-id=".$usuarioBP->getId()." $listaPermisos>"
                .$usuarioBP->getNombreUsuario()."
                </td>
                <td>".$usuarioBP->getEmail()."</td>
                <td>$diasInactivo</td>
                <td>$numInfracciones</td>
                <td>".$usuarioBP->getCountLibrosRegistrados()."</td>
                <td class=admin-opcion onclick=getModalEditarPermisosUsuario(this)>✏️</td>
                <td class=admin-opcion onclick=getModalBloquearUsuario(this)>⛔</td>
                <td class=admin-opcion onclick=getModalEliminarUsuario(this)>❌</td>
              </tr>
              ";
            }
          ?>
          
        </table>
      </div>
  <?php endif; ?>
  <custom-toast></custom-toast>

  <?php
    $showToast = $_SESSION["toast"]["showToast"];

    if ($showToast) {
      $tipo = $_SESSION["toast"]["tipo"];
      $mensaje = $_SESSION["toast"]["mensaje"];

    echo '<script>
      const toast = document.querySelector("custom-toast");
      toast.setMensaje("'.$mensaje.'");
      toast.setTipo("'.$tipo.'");

      toast.showToast();
    </script>';
    }

    unset($_SESSION["toast"]);
  ?>
  <script src="/client/handlers/themeHandler.js"></script>
  <script src="/client/handlers/adminModalsHandler.js"></script>
  </body>
</html>