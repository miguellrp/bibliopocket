<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/server/database/Conector.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/server/handlers/Util.php");

// Para controlar feedback en cambios hechos por la persona usuaria:
if (!isset($_SESSION["toast"])) $_SESSION["toast"]["showToast"] = false;

class Usuario {
  private $id;
  private $nombreUsuario;
  private $contrasenha;
  private $email;
  private $userPic;
  private $ultimoLogin;
  private $conexionDB;


  function __construct($id, $nombreUsuario = null, $contrasenha = null, $email = null) {
    $this->conexionDB = new Conector;
  
    // En caso de que se cree un Usuario de forma temporal (para validar registro o login):
    if ($id == "idTemporal" || $id == "idTemporalRegistro") {
      $this->nombreUsuario = $nombreUsuario;
      $this->contrasenha = $contrasenha;

      if ($id == "idTemporalRegistro") $this->email = $email;
    } 
    // En caso de ser un usuario con cuenta válida en la app, se crea un usuario con sus propiedades recogidas de la DB:
    else {
      $this->id             = $id;
      $camposDB             = $this->getCamposDB();

      $this->nombreUsuario  = $camposDB["nombreUsuarioDB"];
      $this->contrasenha    = $camposDB["contrasenhaDB"];
      $this->email          = $camposDB["emailDB"];
      $this->userPic        = $camposDB["userPicDB"];
      $this->ultimoLogin    = $camposDB["ultimoLoginDB"];
    }
  }

  private function getCamposDB() {
    $camposDB = [];

    $queryDB = $this->conexionDB->conn->prepare("SELECT
    nombre_usuario,
    contrasenha_usuario,
    email_usuario,
    user_pic,
    fecha_ultimo_login
    FROM usuarios WHERE id = :id");
    $queryDB->execute(array(":id" => $this->getId()));
    $row = $queryDB->fetch();

    $camposDB["nombreUsuarioDB"]  = $row[0];
    $camposDB["contrasenhaDB"]    = $row[1];
    $camposDB["emailDB"]          = $row[2];
    $camposDB["userPicDB"]        = $row[3];
    $camposDB["ultimoLoginDB"]    = $row[4];

    return $camposDB;
  }

  // --- GETTERS ---
  function getId() {
    return $this->id;
  }

  function getNombreUsuario() {
    return $this->nombreUsuario;
  }

  function getContrasenha() {
    return $this->contrasenha;
  }

  function getEmail() {
    return $this->email;
  }

  function getUserPic() {
    return $this->userPic??"/client/assets/images/user-pics/placeholderUserPic.webp";
  }

  function getUltimoLogin() {
    return $this->ultimoLogin;
  }

  /* MÉTODOS DE LA CLASE CONECTORES CON DB */
  function loginDB() {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT nombre_usuario, contrasenha_usuario FROM usuarios
      WHERE nombre_usuario = :user");
      
      $query->bindParam(":user", $this->nombreUsuario, PDO::PARAM_STR);
      $query->execute();
  
      $usuarioEncontrado = $query->rowCount();

      if ($usuarioEncontrado == 1) {
        $passwordDB = $query->fetchColumn(1);
        return password_verify($this->getContrasenha(), $passwordDB);
      }
    } catch (PDOException $exception) {
      echo "Ocurrió un error durante el login. ". $exception->getMessage();
      return false;
    }
  }

  function registrarUsuarioDB() {
    $idGenerado = Util::generarId();
    $passHasheado = password_hash($this->getContrasenha(), PASSWORD_DEFAULT);
    $registroOk = true;

    try {
      $query = $this->conexionDB->conn->prepare("INSERT INTO usuarios
      (id, nombre_usuario, contrasenha_usuario, email_usuario, fecha_ultimo_login)
      VALUES (:id, :nombreUsuario, :contrasenhaHash, :email, :fechaUltimoLogin)");
  
      $query->execute(array(
        ":id"               => $idGenerado,
        ":nombreUsuario"    => $this->getNombreUsuario(),
        ":contrasenhaHash"  => $passHasheado,
        ":email"            => $this->getEmail(),
        ":fechaUltimoLogin" => date("Y-m-d H:i:s")
      ));
    }
    catch (PDOException $exception) {
      $registroOk = false;
    }
    
    return $registroOk;
  }

  function setUltimoLoginDB() {
    try {
      $query = $this->conexionDB->conn->prepare("UPDATE usuarios
        SET fecha_ultimo_login = NOW() WHERE id = :id");

      $query->execute(array(":id" => $this->id));
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al actualizar la fecha del último login. ". $exception->getMessage();
    }
  }

  function setUserPicPathDB($userPicPath) {
    $conexionDB = new Conector;
    
    try {
      $query = $conexionDB->conn->prepare("UPDATE usuarios
        SET user_pic = :user_pic WHERE id = :id");
  
      $query->execute(array(
        ":user_pic" => $userPicPath,
        ":id"       => $this->id
      ));
      return true;
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al guardar la nueva imagen. ". $exception->getMessage();
      return false;
    }
  }

  function esLibroGuardado($libro) {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT * from libros
        WHERE id = :idLibro AND id_usuario = :idUsuario");
      $query->execute(array(
        ":idLibro"    => $libro->getId(),
        ":idUsuario"  => $this->getId()
      ));
      return $query->rowCount() > 0;
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al comprobar si ya existe este libro en la estantería. ". $exception->getMessage();
    }
  }

  function actualizarNombreUsuario($nuevoNombreUsuario) {
    try {
      $query = $this->conexionDB->conn->prepare("UPDATE usuarios SET
        nombre_usuario = :nuevoNombreUsuario
        WHERE id = :id");
      $query->execute(array(
        ":id"  => $this->getId(),
        ":nuevoNombreUsuario" => $nuevoNombreUsuario
      ));

      $_SESSION["toast"]["tipo"] = "ok";
      $_SESSION["toast"]["mensaje"] = "Se ha actualizado tu perfil correctamente";
    }
    catch (PDOException $exception) {
      $_SESSION["toast"]["tipo"] = "warning";
      $_SESSION["toast"]["mensaje"] = "El nombre de usuario introducido ya está registrado en BiblioPocket";
    }

    $_SESSION["toast"]["showToast"] = true;

    return $query->rowCount() == 1;
  }

  function actualizarUserPic() {
    $extension = explode(".", $_FILES["userProfilePic"]["name"]);
    $extension = end($extension);
    $subidaOk = false;
    
    $nombreArchivoImagen = $this->getId().".".$extension;
    $rutaImagen = dirname(__DIR__, 2)."/client/assets/images/user-pics/" . $nombreArchivoImagen;
    $rutaSinExtension = explode(".", $rutaImagen);
    $rutaSinExtension = glob($rutaSinExtension[0].".*");
    
    if (!empty($rutaSinExtension)) unlink($rutaSinExtension[0]);

    if (move_uploaded_file($_FILES["userProfilePic"]["tmp_name"], $rutaImagen))
    $this->userPic = "http://localhost/client/assets/images/user-pics/" . $nombreArchivoImagen;

    try {
      $query = $this->conexionDB->conn->prepare("UPDATE usuarios SET
        user_pic = :nuevaPic
        WHERE id = :id");
      $query->execute(array(
        ":id"  => $this->getId(),
        ":nuevaPic" => $this->getUserPic()
      ));

      $_SESSION["toast"]["tipo"] = "ok";
      $_SESSION["toast"]["mensaje"] = "Se ha actualizado tu perfil correctamente";
    }
    catch (PDOException) {
      $_SESSION["toast"]["tipo"] = "error";
      $_SESSION["toast"]["mensaje"] = "Ha ocurrido un problema al intentar actualizar tu perfil";
    }

    $_SESSION["toast"]["showToast"] = true;
  }

  function actualizarCorreo($nuevoEmail) {
    try {
      $query = $this->conexionDB->conn->prepare("UPDATE usuarios SET
        email_usuario = :nuevoEmail
        WHERE id = :id");
      $query->execute(array(
        ":id"  => $this->getId(),
        ":nuevoEmail" => $nuevoEmail
      ));

      $_SESSION["toast"]["tipo"] = "ok";
      $_SESSION["toast"]["mensaje"] = "Se ha actualizado tu correo correctamente";
    }
    catch (PDOException) {
      $_SESSION["toast"]["tipo"] = "error";
      $_SESSION["toast"]["mensaje"] = "No se ha podido actualizar tu correo (correo no válido o ya registrado en BiblioPocket)";
    }

    $_SESSION["toast"]["showToast"] = true;
  }

  function actualizarContrasenha($contrasenhaAntigua, $nuevaContrasenha) {
    try {
      if (password_verify($contrasenhaAntigua, $this->getContrasenha())) {
        $nuevaContrasenhaHasheada = password_hash($nuevaContrasenha, PASSWORD_DEFAULT);
        $query = $this->conexionDB->conn->prepare("UPDATE usuarios SET
        contrasenha_usuario = :nuevaContrasenha
        WHERE id = :id");

        $query->execute(array(
          ":id"  => $this->getId(),
          ":nuevaContrasenha" => $nuevaContrasenhaHasheada
        ));

        $_SESSION["toast"]["tipo"] = "ok";
        $_SESSION["toast"]["mensaje"] = "Se ha actualizado tu contraseña correctamente";
      } else {
        $_SESSION["toast"]["tipo"] = "warning";
        $_SESSION["toast"]["mensaje"] = "La contraseña antigüa introducida no es correcta";
      }
    }
    catch (PDOException) {
      $_SESSION["toast"]["tipo"] = "error";
      $_SESSION["toast"]["mensaje"] = "No se ha podido actualizar tu contraseña";
    }

    $_SESSION["toast"]["showToast"] = true;
  }

  function restablecerCuenta() {
    try {
      $query = $this->conexionDB->conn->prepare("DELETE from libros
        WHERE id_usuario = :idUsuario");
      $query->execute(array(
        ":idUsuario"  => $this->getId()
      ));

      $_SESSION["toast"]["tipo"] = "ok";
      $_SESSION["toast"]["mensaje"] = "Se han restablecido los datos de tu cuenta correctamente";
    }
    catch (PDOException) {
      $_SESSION["toast"]["tipo"] = "error";
      $_SESSION["toast"]["mensaje"] = "No se ha podido restablecer tu cuenta";
    }

    $_SESSION["toast"]["showToast"] = true;
  }

  function eliminarCuenta() {
    try {
      $query = $this->conexionDB->conn->prepare("DELETE from usuarios
        WHERE id = :id");
      $query->execute(array(
        ":id"  => $this->getId()
      ));
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al tratar de eliminar la cuenta. ". $exception->getMessage();
    }
  }


  /* Métodos para analíticas */
  function getCountLibrosPorEstado($estado) {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT COUNT(id) from libros
        WHERE id_usuario = :idUsuario and estado = :estado");
      $query->execute(array(
        ":idUsuario"  => $this->getId(),
        ":estado"     => $estado
      ));
      return $query->fetchColumn();
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al recoger el número de libros por estado '$estado'. ". $exception->getMessage();
    }
  }

  function getCountLibrosRegistrados() {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT COUNT(id) from libros
        WHERE id_usuario = :idUsuario");
      $query->execute(array(
        ":idUsuario"  => $this->getId()
      ));
      return $query->fetchColumn();
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al recoger el número de libros registrados '. ". $exception->getMessage();
    }
  }

  function getTiempoRegistrado() {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT TIMEDIFF(NOW(), fecha_registro) from usuarios
        WHERE id = :id");
      $query->execute(array(
        ":id"  => $this->getId()
      ));
      return $query->fetchColumn();
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al contabilizar el número de días registrado '. ". $exception->getMessage();
    }
  }

  function estaBloqueado() {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT id from bloqueos_usuarios
        WHERE id_usuario = :idUsuario AND fecha_expiracion >= NOW() LIMIT 1");
      $query->execute(array(
        ":idUsuario"  => $this->getId()
      ));
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al comprobar si la cuenta ha sido bloqueada '. ". $exception->getMessage();
    }

    return $query->rowCount() > 0;
  }

  // Funciones estáticas utilizadas para cuando una persona usuaria olvidó su contraseña
  static function getNombreUsuarioDe($emailUsuario) {
    try {
      $conexionDB = new Conector;
      $query = $conexionDB->conn->prepare("SELECT nombre_usuario from usuarios
        WHERE email_usuario = :emailUsuario");
      $query->execute(array(
        ":emailUsuario"  => $emailUsuario
      ));
      return $query->fetchColumn();
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al tratar de recuperar el nombre de usuario con el correo $emailUsuario '. ". $exception->getMessage();
    }
  }

  static function setContrasenhaTemporalDe($emailUsuario, $contrasenhaTemporal) {
    $idGenerado = Util::generarId();
    $fechaActual = new DateTime();
    $fechaActual->add(new DateInterval('PT15M'));
    $fechaExpiracion = $fechaActual->format('Y-m-d H:i:s');

    try {
      Usuario::deleteContrasenhaTemporal($emailUsuario);
      
      $conexionDB = new Conector;
      $query = $conexionDB->conn->prepare("INSERT INTO contrasenhas_temporales
        VALUES (:id, :emailUsuario, :contrasenhaTemporal, :fechaExpiracion)");
      $query->execute(array(
        ":id"                   => $idGenerado,
        ":emailUsuario"         => $emailUsuario,
        ":contrasenhaTemporal"  => $contrasenhaTemporal,
        ":fechaExpiracion"      => $fechaExpiracion
      ));
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al intentar asignar una contraseña temporal. ". $exception->getMessage();
      return false;
    }
    
    return true;
  }

  static function deleteContrasenhaTemporal($emailUsuario) {
    try {
      $conexionDB = new Conector;
      $query = $conexionDB->conn->prepare("DELETE FROM contrasenhas_temporales
        WHERE email_usuario = :emailUsuario");
      $query->bindParam(":emailUsuario", $emailUsuario);
      $query->execute();
    } 
    catch (PDOException $exception) {
      echo "No había ninguna contraseña temporal asignada al correo $emailUsuario. ";
    }
  }
  
  static function loginTemporal($nombreUsuario, $contrasenhaTemporal) {
    try {
      $conexionDB = new Conector;
      $query = $conexionDB->conn->prepare("SELECT nombre_usuario
      FROM usuarios tu
      JOIN contrasenhas_temporales tct ON tct.email_usuario = tu.email_usuario
      WHERE tu.nombre_usuario = :nombreUsuario AND tct.contrasenha_temporal = :contrasenhaTemporal");
      
      $query->execute(array(
        ":nombreUsuario"          => $nombreUsuario,
        ":contrasenhaTemporal"    => $contrasenhaTemporal
      ));
  
      return $query->rowCount() == 1;

    } catch (PDOException $exception) {
      echo "Ocurrió un error durante el login con contraseña temporal. ". $exception->getMessage();
      return false;
    }
  }

  static function esEmailUnico($emailIntroducido) {
    try {
      $conexionDB = new Conector;
      $query = $conexionDB->conn->prepare("SELECT email_usuario FROM usuarios WHERE email_usuario = :emailIntroducido");
      
      $query->bindParam(":emailIntroducido", $emailIntroducido);
      $query->execute();

    } catch (PDOException $exception) {
      echo "Ocurrió un error al comprobar si el email introducido es único en la BD. ". $exception->getMessage();
    }

    return $query->rowCount() == 0;
  }

  static function esNombreUnico($nombreUsuario) {
    try {
      $conexionDB = new Conector;
      $query = $conexionDB->conn->prepare("SELECT nombre_usuario FROM usuarios WHERE nombre_usuario = :nombreUsuario");
      
      $query->bindParam(":nombreUsuario", $nombreUsuario);
      $query->execute();

    } catch (PDOException $exception) {
      echo "Ocurrió un error al comprobar si el nombre de usuario introducido es único en la BD. ". $exception->getMessage();
    }
    
    return $query->rowCount() == 0;
  }
}
