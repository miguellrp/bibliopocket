<?php
session_start();
require_once($_SERVER["DOCUMENT_ROOT"]."/server/classes/Usuario.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/server/classes/Email.php");

// Para conservar la sección activa dentro de "Mi perfil"
if (!isset($_SESSION["seccionActiva"])) $_SESSION["seccionActiva"] = 0;

// Para controlar feedback en cambios hechos por la persona usuaria:
if (!isset($_SESSION["toast"])) $_SESSION["toast"]["showToast"] = false;

if (isset($_SESSION["usuarioActivo"]))
  $usuarioActivo = new Usuario($_SESSION["usuarioActivo"]["id"]);

if (isset($usuarioActivo)) {
  if (isset($_POST["modificacion-datos-user"])) {
    if (isset($_FILES["userProfilePic"]) && is_uploaded_file($_FILES["userProfilePic"]["tmp_name"])) {
      $usuarioActivo->actualizarUserPic();
    }

    if (isset($_POST["username"]) && ($_POST["username"]) != $usuarioActivo->getNombreUsuario()) {
      $usuarioActivo->actualizarNombreUsuario($_POST["username"]);
    }
    
    $_SESSION["seccionActiva"] = 1;
    header("Location: index.php");
    session_write_close();
  }

  if (isset($_POST["cambiar-correo"])) {
    $nuevoCorreo = $_POST["correo-nuevo"];
    if ($nuevoCorreo != $usuarioActivo->getEmail() && Usuario::esEmailUnico($nuevoCorreo)) {
      $codigoGenerado = rand(1000, 9999);
      $tiempoExpiracionCodigo = time() + 300;
      $_SESSION["codigoConfirmacion"] = $codigoGenerado;

      $customData = ["nombreUsuario" => $usuarioActivo->getNombreUsuario(), "codigoConfirmacion" => $_SESSION["codigoConfirmacion"]];

      $emailCambioCorreo = new Email($nuevoCorreo, $usuarioActivo->getNombreUsuario(), 2, $customData);
      if ($emailCambioCorreo->sendMail()) {
        $_SESSION["toast"]["tipo"] = "ok";
        $_SESSION["toast"]["mensaje"] = "Se ha enviado el correo correctamente";
        $_SESSION["toast"]["showToast"] = "true";

        echo "<dialog class='modal' id='configuracion-modal' style='z-index:15; margin-top: 200px' open>
          <form action='' method='POST'>
            <label for='codigo'>Introduce el código enviado a tu nuevo correo: </label>
            <input type='text' class='input-text' minlength=4 maxlength=4 id='codigo' name='codigo' required>
            <input type='submit' class='submit-btn' name='confirm-nuevo-correo' value='Enviar'>
            <input type='hidden' name='usuario' value='".$usuarioActivo->getNombreUsuario()."'>
            <input type='hidden' name='correo-nuevo' value='".$nuevoCorreo."'>
          </form>
        </dialog>";
      }
    } else {
      $_SESSION["toast"]["tipo"] = "warning";
      $_SESSION["toast"]["mensaje"] = "El correo electrónico introducido ya está registrado en BiblioPocket";
      $_SESSION["toast"]["showToast"] = "true";
    }
  }

  if (isset($_POST["confirm-nuevo-correo"]) && $_POST["codigo"] == $_SESSION["codigoConfirmacion"]) {
    $usuarioActivo->actualizarCorreo($_POST["correo-nuevo"]);

    $_SESSION["seccionActiva"] = 1;
    header("Location: index.php");
    session_write_close();
  }

  if (isset($_POST["cambiar-contrasenha"])) {
    if ($_POST["contrasenha-nueva"] === $_POST["contrasenha-nueva-confirmacion"]) {
      $contrasenhaAntigua = $_POST["contrasenha-antigua"];
      $contrasenhaNueva = $_POST["contrasenha-nueva"];
      $usuarioActivo->actualizarContrasenha($contrasenhaAntigua, $contrasenhaNueva);

      $_SESSION["seccionActiva"] = 1;
      header("Location: index.php");
      session_write_close();
    }
  }
  
  if (isset($_POST["restablecer-cuenta"])) {
    $usuarioActivo->restablecerCuenta();

    $_SESSION["seccionActiva"] = 1;
    header("Location: index.php");
    session_write_close();
  }

  
  if (isset($_POST["eliminar-cuenta"])) {
    // Primero se eliminan todos los libros asociados a su cuenta para no violar la constraint de la relación Libros<->Usuario
    $usuarioActivo->restablecerCuenta();
    $usuarioActivo->eliminarCuenta();
    unset($_SESSION["usuarioActivo"]);

    header("Location: /index.php");
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
  <link rel="icon" type="image/png" href="/client/assets/images/favicon.png">
  <link rel="stylesheet" href="/client/styles/globals.css">
  <link rel="stylesheet" href="styles.css">
  <script src="/client/components/CustomButton.js"></script>
  <script src="/client/components/CustomHeader.js"></script>
  <script src="/client/components/CustomImageUploader.js"></script>
  <script src="/client/components/CustomToast.js"></script>
</head>

<body>
  <?php if (!isset($usuarioActivo)): ?>
    <div class="caja-contenido">
      <h2>👀 ¿A dónde quieres ir?</h2>
      <h3>Parece que primero tienes que <a href="../index.php">iniciar sesión</a></h3>
    </div>
  <?php elseif($usuarioActivo->estaBloqueado()): Util::mostrarPantallaUsuarioBloqueado($usuarioActivo->getId()); ?>
  <?php else: ?>
    <custom-header pagina-activa="mi-perfil"></custom-header>
    <nav class="nav-perfil">
      <input type="button" value="Detalles de la cuenta" <?= $_SESSION["seccionActiva"] == 0 ? "active" : null ?>>
      <input type="button" value="Configuración" <?= $_SESSION["seccionActiva"] == 1 ? "active" : null ?>>
    </nav>

    <section class="detalles-cuenta container" <?= $_SESSION["seccionActiva"] == 0 ? "active" : null ?> >
      <h2>Tus estadísticas</h2>
      <img class="userpic" src="<?= $usuarioActivo->getUserPic() ?>" class="preview" alt="Foto de perfil de <?= $usuarioActivo->getNombreUsuario() ?>" >
      <ul>
        <li>Total de libros leídos:
          <span class="detalle-user"><?= $usuarioActivo->getCountLibrosPorEstado(2) ?></span>
        </li>
        <li>Total de libros añadidos a la estantería:
          <span class="detalle-user"><?= $usuarioActivo->getCountLibrosRegistrados() ?></span>
        </li>
        <li>Tiempo transcurrido desde tu registro:
          <span class="detalle-user"><?= $usuarioActivo->getTiempoRegistrado() ?></span>
        </li>
      </ul>
    </section>

    <section class="configuracion container" <?= $_SESSION["seccionActiva"] == 1 ? "active" : null ?>>
      <form class="datos-user" action="" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="form-fields">
          <custom-image-uploader data-src=<?= $usuarioActivo->getUserPic()?>></custom-image-uploader>
          <div class="middle">
            <input type="text" class="username" name="username" value="<?= $usuarioActivo->getNombreUsuario() ?>">
            <button type="submit" name="modificacion-datos-user">
              <svg class="button-icon">
                <use xlink:href="/client/assets/images/floppy-disk-icon.svg#floppy-disk"></use>
              </svg>
                Guardar cambios
            </button>
          </div>
          <img src="/client/assets/images/moon-icon.png" class="icon darklight sun" tabindex="1"
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
  <script src="script.js" type="module"></script>
</body>

</html>