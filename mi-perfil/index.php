<?php
session_start();
include_once "../server/classes/Usuario.php";

$usuarioActivo = new Usuario($_SESSION["usuarioActivo"]["id"]);

if (isset($_POST["modificacion-datos-user"])) {
  if (is_uploaded_file($_FILES["userProfilePic"]["tmp_name"])) {
    $userID = $usuarioActivo->getId();
    $temp = explode(".", $_FILES["userProfilePic"]["name"]);
    $nombreArchivoImagen = $usuarioActivo->getNombreUsuario()."ProfilePic.".end($temp);
    $rutaImagen = "../client/assets/images/user-pics/" . $nombreArchivoImagen;

    move_uploaded_file($_FILES["userProfilePic"]["tmp_name"], $rutaImagen);
    $usuarioActivo->setUserPicPathDB($rutaImagen);
    header("Location: index.php");
  }
}
?>

<!DOCTYPE html>
<html lang="es-ES">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bibliopocket | Mi perfil</title>
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
    <nav>
      <input type="button" value="Detalles de la cuenta">
      <input type="button" value="Configuración">
    </nav>

    <form class="datos-user" action="" method="POST" enctype="multipart/form-data">
      <section class="username-pic">
        <div class="wrap-image-uploader">
          <img src="<?= $usuarioActivo->getUserPicPathDB() ?>" class="preview" alt="Foto de perfil de <?= $usuarioActivo->getNombreUsuario() ?>" >
          <div class="wrap-uploader" >
            <label for="uploader-input">
              <img src="/bibliopocket/client/assets/images/pencil-icon.png" class="lapiz-icon">
            </label>
            <input id="uploader-input" type="file" accept="image/*" name="userProfilePic" />
          </div>
        </div>
        <input type="text" class="username" name="username" value="<?= $usuarioActivo->getNombreUsuario() ?>">
      </section>
      <label>Modo claro/oscuro:
        <img src="/bibliopocket/client/assets/images/moon-icon.png" class="icon darklight sun" tabindex="1"
          alt="Símbolo de una media luna (menguante) para representar el modo oscuro de la web">
      </label>
      <input type="submit" value="GUARDAR CAMBIOS" name="modificacion-datos-user">
    </form>

  <?php endif; ?>
  <script src="../client/components/CustomHeader.js"></script>
  <script src="../client/handlers/themeHandler.js"></script>
  <script src="../client/handlers/previewHandler.js" type="module"></script>
  <script src="script.js" type="module"></script>
</body>

</html>