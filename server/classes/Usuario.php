<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/bibliopocket/server/database/Conector.php");

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
    $id = $this->getId();
    $camposDB = [];

    $queryDB = $this->conexionDB->conn->prepare("SELECT * FROM usuarios WHERE id = :id");
    $queryDB->execute(array(":id" => $id));
    
    $camposDB["nombreUsuarioDB"]  = $queryDB->fetchColumn(1);
    $camposDB["contrasenhaDB"]    = $queryDB->fetchColumn(2);
    $camposDB["emailDB"]          = $queryDB->fetchColumn(3);
    $camposDB["userPicDB"]        = $queryDB->fetchColumn(4);
    $camposDB["ultimoLoginDB"]    = $queryDB->fetchColumn(5);

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
    return $this->userPic;
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
        return password_verify($this->contrasenha, $passwordDB);
      }
    } catch (PDOException $exception) {
      echo "Ocurrió un error durante el login. ". $exception->getMessage();
      return false;
    }
  }

  function registrarUsuarioDB() {
    // ID único "seguro" a partir de: https://stackoverflow.com/questions/29235481/generate-a-readable-random-unique-id
    $idGenerado = join('-', str_split(bin2hex(openssl_random_pseudo_bytes(10)), 5));
    $passHasheado = password_hash($this->contrasenha, PASSWORD_DEFAULT);

    try {
      $query = $this->conexionDB->conn->prepare("INSERT INTO usuarios
      (id, nombre_usuario, contrasenha_usuario, email_usuario, ultimo_login)
      VALUES (:id, :nombreUsuario, :contrasenhaHash, :email, :ultimoLogin)");
  
      $query->execute(array(
        ":id"               => $idGenerado,
        ":nombreUsuario"    => $this->nombreUsuario,
        ":contrasenhaHash"  => $passHasheado,
        ":email"            => $this->email,
        ":ultimoLogin"      => date('Y-m-d H:i:s')
      ));
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error durante el registro. ". $exception->getMessage();
      return false;
    }
    
    return true;
  }

  function setUltimoLoginDB() {
    try {
      $query = $this->conexionDB->conn->prepare("UPDATE usuarios
        SET ultimo_login = NOW() WHERE id = :id");

      $query->execute(array(":id" => $this->id));
      $this->ultimoLogin = date("Y-m-d H:i:s");
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al actualizar la fecha del último login. ". $exception->getMessage();
    }
  }

  function getUserPicPathDB() {
      try {
        $query = $this->conexionDB->conn->prepare("SELECT user_pic FROM usuarios
          WHERE nombre_usuario LIKE :nombreUsuario");
    
        $query->bindParam(":nombreUsuario", $this->nombreUsuario, PDO::PARAM_STR);
        $query->execute();
      }
      catch (PDOException $exception) {
        echo "Ocurrió un error al cargar la imagen. ". $exception->getMessage();
        return "placeholder-user-pic.webp";
      }
      
      // En el caso de que la persona usuaria no facilitase ninguna foto de perfil, se le da un placeholder genérico:
      if($query->fetchColumn() == "") return "placeholder-user-pic.webp";
      else return $query->fetchColumn();
  }

  
  function registrarLibro($libro) {
    try {
      $query = $this->conexionDB->conn->prepare("INSERT INTO libros
        VALUES (:id, :titulo, :subtitulo, :autoria,
        :descripcion, :portada, :numPaginas, :editorial,
        :anhoPublicacion, :enlaceAPI, :userID)");
      
      $query->execute(array(
        ":id"               => $libro["id"],
        ":titulo"           => $libro["titulo"],
        ":subtitulo"        => $libro["subtitulo"],
        ":autoria"          => $libro["autoria"],
        ":descripcion"      => $libro["descripcion"],
        ":portada"          => $libro["portada"],
        ":numPaginas"       => $libro["numPaginas"],
        ":editorial"        => $libro["editorial"],
        ":anhoPublicacion"  => $libro["anhoPublicacion"],
        ":enlaceAPI"        => $libro["enlaceAPI"],
        ":userID"           => $this->getId()
      ));
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al registrar el libro. ". $exception->getMessage();
    }
  }

  function getLibrosIDs() {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT id FROM libros
        WHERE id_usuario = :userID");

      $query->execute(array(":userID" => $this->getId()));
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al cargar la estantería. ". $exception->getMessage();
    }

    return $query->fetchAll(PDO::FETCH_COLUMN);
  }

  function libroGuardado($libro) {
    try {
      $query = $this->conexionDB->conn->prepare("SELECT * from libros
        WHERE id = :idLibro AND id_usuario = :idUsuario");
      $query->execute(array(
        ":idLibro"    => $libro["id"],
        ":idUsuario"  => $this->getId()
      ));
      return $query->rowCount() > 0;
    }
    catch (PDOException $exception) {
      echo "Ocurrió un error al comprobar si ya existe este libro en la estantería. ". $exception->getMessage();
    }
  }
}
?>