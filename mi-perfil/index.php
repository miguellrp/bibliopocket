<?php
session_start();
include_once "../server/classes/Usuario.php";

$seccionActiva = 0;
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
      $usuarioActivo->setUserPicPathDB($rutaImagen);
      header("Location: index.php");
    }

    if (isset($_POST["username"]) && ($_POST["username"]) != $usuarioActivo->getNombreUsuario()) {
      $usuarioActivo->actualizarNombreUsuario($_POST["username"]);
      header("Location: index.php");
    }

    $seccionActiva = 1;
  }

  if (isset($_POST["cambiar-correo"])) {
    $usuarioActivo->actualizarCorreo($_POST["correo-nuevo"]);
    header("Location: index.php");
  }

  if (isset($_POST["cambiar-contrasenha"])) {
    if ($_POST["contrasenha-nueva"] === $_POST["contrasenha-nueva-confirmacion"])
    $contrasenhaAntigua = $_POST["contrasenha-antigua"];
    $contrasenhaNueva = $_POST["contrasenha-nueva"];


    $usuarioActivo->actualizarContrasenha($contrasenhaAntigua, $contrasenhaNueva);
    header("Location: index.php");
  }
  
  if (isset($_POST["restablecer-cuenta"])) {
    $usuarioActivo->restablecerCuenta();
    header("Location: index.php");
  }

  
  if (isset($_POST["eliminar-cuenta"])) {
    unset($_SESSION["usuarioActivo"]);
    $usuarioActivo->eliminarCuenta();
    header("Location: /bibliopocket/index.php");
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
      <input type="button" value="Detalles de la cuenta" <?= $seccionActiva == 0 ? "active" : null ?>>
      <input type="button" value="Configuración" <?= $seccionActiva == 1 ? "active" : null ?>>
    </nav>

    <section class="detalles-cuenta container" <?= $seccionActiva == 0 ? "active" : null ?> >
      <h2>Tus estadísticas</h2>
      <img class="userpic" src="<?= $usuarioActivo->getUserPicPathDB() ?>" class="preview" alt="Foto de perfil de <?= $usuarioActivo->getNombreUsuario() ?>" >
      <ul>
        <li>Total de libros leídos:
          <span class="detalle-user"><?= $usuarioActivo->getCountLibrosPorEstado("Leido") ?></span>
        </li>
        <li>Total de libros añadidos a la estantería:
          <span class="detalle-user"><?= $usuarioActivo->getCountLibrosRegistrados() ?></span>
        </li>
        <li>Tiempo transcurrido desde tu registro:
          <span class="detalle-user"><?= $usuarioActivo->getDiasRegistrado() ?></span>
        </li>
      </ul>
    </section>


    <section class="configuracion container" <?= $seccionActiva == 1 ? "active" : null ?>>
      <form class="datos-user" action="" method="POST" enctype="multipart/form-data">
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
  <script src="/bibliopocket/client/components/CustomHeader.js"></script>
  <script src="/bibliopocket/client/components/CustomButton.js"></script>
  <script src="/bibliopocket/client/handlers/themeHandler.js"></script>
  <script src="/bibliopocket/client/handlers/previewHandler.js" type="module"></script>
  <script src="script.js" type="module"></script>
</body>

</html>