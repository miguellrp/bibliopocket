<?php
session_start();
include_once "../server/classes/Usuario.php";

// Para conservar la sección activa dentro de "Mi perfil"
if (!isset($_SESSION["seccionActiva"])) $_SESSION["seccionActiva"] = 0;

if(!isset($toastOk)) $toastOk = false;
if(!isset($toastError)) $toastError = false;

// Para controlar feedback en cambios hechos por la persona usuaria:
if (!isset($_SESSION["showToastOk"])) $_SESSION["showToastOk"] = false;
if (!isset($_SESSION["showToastError"])) $_SESSION["showToastError"] = false;

if (isset($_SESSION["usuarioActivo"]))
  $usuarioActivo = new Usuario($_SESSION["usuarioActivo"]["id"]);

if (isset($usuarioActivo)) {
  if (isset($_POST["modificacion-datos-user"])) {
    if (is_uploaded_file($_FILES["userProfilePic"]["tmp_name"])) {
      $userID = $usuarioActivo->getId();
      $temp = explode(".", $_FILES["userProfilePic"]["name"]);
      
      // TODO: comprobar que no se está almacenando otra userpic con distinto formato (png, jpg...) para eliminarla previamente a subir la nueva
      $nombreArchivoImagen = $usuarioActivo->getNombreUsuario()."ProfilePic.".end($temp);
      $rutaImagen = "/bibliopocket/client/assets/images/user-pics/" . $nombreArchivoImagen;
  
      move_uploaded_file($_FILES["userProfilePic"]["tmp_name"], $rutaImagen);
      if($usuarioActivo->setUserPicPathDB($rutaImagen)) {
        $_SESSION["showToastOk"] = true;
      }
    }

    if (isset($_POST["username"]) && ($_POST["username"]) != $usuarioActivo->getNombreUsuario()) {
      $usuarioActivo->actualizarNombreUsuario($_POST["username"]);
      $_SESSION["showToastOk"] = true;
    }

    $_SESSION["seccionActiva"] = 1;
    header("Location: index.php");
    session_write_close();
  }

  if (isset($_POST["cambiar-correo"])) {
    if ($_POST["correo-nuevo"] != $usuarioActivo->getEmail()) {
      if ($usuarioActivo->actualizarCorreo($_POST["correo-nuevo"])) {
        $_SESSION["showToastOk"] = true;
      } else {
        $_SESSION["showToastError"] = true;
      }
  
      $_SESSION["seccionActiva"] = 1;
      header("Location: index.php");
      session_write_close();
    }
  }

  if (isset($_POST["cambiar-contrasenha"])) {
    if ($_POST["contrasenha-nueva"] === $_POST["contrasenha-nueva-confirmacion"]) {
      $contrasenhaAntigua = $_POST["contrasenha-antigua"];
      $contrasenhaNueva = $_POST["contrasenha-nueva"];

      if ($usuarioActivo->actualizarContrasenha($contrasenhaAntigua, $contrasenhaNueva)) {
        $_SESSION["showToastOk"] = true;
      } else {
        $_SESSION["showToastError"] = true;
      }

      $_SESSION["seccionActiva"] = 1;
      header("Location: index.php");
      session_write_close();
    }
  }
  
  if (isset($_POST["restablecer-cuenta"])) {
    $usuarioActivo->restablecerCuenta();

    $_SESSION["showToastOk"] = "true";
    $_SESSION["seccionActiva"] = 1;
    header("Location: index.php");
    session_write_close();
  }

  
  if (isset($_POST["eliminar-cuenta"])) {
    unset($_SESSION["usuarioActivo"]);
    $usuarioActivo->eliminarCuenta();

    header("Location: /bibliopocket/index.php");
    session_write_close();
  }
}
?>

<!DOCTYPE html>
<html lang="es-ES">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BiblioPocket | Mi perfil</title>
  <link rel="icon" type="image/png" href="/bibliopocket/client/assets/images/favicon.png">
  <link rel="stylesheet" href="/bibliopocket/client/styles/globals.css">
  <link rel="stylesheet" href="styles.css">
  <script src="/bibliopocket/client/components/CustomHeader.js"></script>
  <script src="/bibliopocket/client/components/CustomButton.js"></script>
  <script src="/bibliopocket/client/components/CustomToast.js"></script>
</head>

<body>
  <?php if (!isset($usuarioActivo)): ?>
    <div class="caja-contenido">
      <h2>👀 ¿A dónde quieres ir?</h2>
      <h3>Parece que primero tienes que <a href="../index.php">iniciar sesión</a></h3>
    </div>
  <?php else: ?>
    <custom-header pagina-activa="mi-perfil"></custom-header>
    <nav class="nav-perfil">
      <input type="button" value="Detalles de la cuenta" <?= $_SESSION["seccionActiva"] == 0 ? "active" : null ?>>
      <input type="button" value="Configuración" <?= $_SESSION["seccionActiva"] == 1 ? "active" : null ?>>
    </nav>

    <section class="detalles-cuenta container" <?= $_SESSION["seccionActiva"] == 0 ? "active" : null ?> >
      <h2>Tus estadísticas</h2>
      <img class="userpic" src="<?= $usuarioActivo->getUserPicPathDB() ?>" class="preview" alt="Foto de perfil de <?= $usuarioActivo->getNombreUsuario() ?>" >
      <ul>
        <li>Total de libros leídos:
          <span class="detalle-user"><?= $usuarioActivo->getCountLibrosPorEstado(2) ?></span>
        </li>
        <li>Total de libros añadidos a la estantería:
          <span class="detalle-user"><?= $usuarioActivo->getCountLibrosRegistrados() ?></span>
        </li>
        <li>Tiempo transcurrido desde tu registro:
          <span class="detalle-user"><?= $usuarioActivo->getDiasRegistrado() ?></span>
        </li>
      </ul>
    </section>

    <section class="configuracion container" <?= $_SESSION["seccionActiva"] == 1 ? "active" : null ?>>
      <form class="datos-user" action="" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="form-fields">
          <div class="userpic">
            <img src="<?= $usuarioActivo->getUserPicPathDB() ?>" class="preview" alt="Foto de perfil de <?= $usuarioActivo->getNombreUsuario() ?>" >
            <div class="wrap-uploader" >
              <label for="uploader-input">
                <img src="/bibliopocket/client/assets/images/pencil-icon.png" class="lapiz-icon">
              </label>
              <input id="uploader-input" type="file" accept="image/*" name="userProfilePic" />
            </div>
          </div>
          <div class="middle">
            <input type="text" class="username" name="username" value="<?= $usuarioActivo->getNombreUsuario() ?>">
            <button type="submit" name="modificacion-datos-user">
              <svg class="button-icon">
                <use xlink:href="/bibliopocket/client/assets/images/floppy-disk-icon.svg#floppy-disk"></use>
              </svg>
                Guardar cambios
            </button>
          </div>
          <img src="/bibliopocket/client/assets/images/moon-icon.png" class="icon darklight sun" tabindex="1"
            alt="Símbolo de una media luna (menguante) para representar el modo oscuro de la web">
        </div>
        <input type="hidden" name="correo" value="<?= $usuarioActivo->getEmail() ?>">
      </form>
      <ul>
        <li>Cambiar correo electrónico</li>
        <li>Cambiar contraseña</li>
        <li>Restablecer cuenta</li>
        <li>Eliminar cuenta</li>
      </ul>
    </section>
  <?php endif; ?>
    <custom-toast></custom-toast>

  <?php
      $toastOk = $_SESSION["showToastOk"];
      $toastError = $_SESSION["showToastError"];

      if ($toastOk || $toastError) {
        if ($toastOk) {
          $mensaje = "Se han guardado los cambios correctamente";
          $tipo = "ok";
        } else {
          $mensaje = "No se han podido guardar los cambios";
          $tipo = "error";
        }

      echo '<script>
        const toast = document.querySelector("custom-toast");
        toast.setMensaje("'.$mensaje.'");
        toast.setTipo("'.$tipo.'");

        toast.showToast();
      </script>';
      }

      unset($_SESSION["showToastOk"], $_SESSION["showToastError"]);
    ?>


  <script src="/bibliopocket/client/handlers/themeHandler.js"></script>
  <script src="/bibliopocket/client/handlers/previewHandler.js" type="module"></script>
  <script src="script.js" type="module"></script>
</body>

</html>