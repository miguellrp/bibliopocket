# BiblioPocket 📚
_Biblioteca virtual en la que la persona usuaria podrá buscar libros, guardarlos, modificar sus datos y categorizarlos. Creado con PHP, CSS y JS nativo (y haciendo uso de WebComponents)._

<img src="demo/demo_pantalla_inicial.png" alt="Captura de pantalla de la vista inicial de BiblioPocket 📚" >

## 🚀 Desplegar demo:
1. Descargar Docker Desktop desde su [página oficial](https://www.docker.com/products/docker-desktop/) (necesario para obtener las herramientas necesarias para la _dockerización_ de la aplicación).
2. Descargar el respositorio como ZIP (_Code > Download ZIP_) o abrir la terminal en el directorio deseado y clonarlo directamente con ```git clone https://github.com/miguellrp/bibliopocket.git```.
3. Una vez instalado Docker Desktop y con éste abierto (será necesario reiniciar el equipo), acceder al directorio raíz del repositorio clonado (``` /bibliopocket ```) y abrir la terminal desde allí.
4. Introducir el comando ```docker compose up --build``` (importante incluír el parámetro ```--build``` para que al _dockerizar_ tenga en cuenta la instalación de la extensión de la clase conectora PDO de PHP con la base de datos).
5. Finalmente, se podrá acceder a la demo de la aplicación en http://localhost:80 y a la base de datos a través de phpMyAdmin en http://localhost:8001.

Debido a que el proceso de registro de una persona usuaria todavía está en construcción (queda por implementar el envío de un mensaje con el código generado para confirmar el correo aportado), se ha creado <a href="#credenciales">un perfil</a> para testear la aplicación.

Por último, para borrar todo lo relativo a la aplicación web de _BiblioPocket_:
1. En la terminal abierta, presionar CTRL + C para detener el _docker_ y, a continuación, introducir el comando ```docker compose down``` para eliminar los contenedores generados.
2. Desde la aplicación gráfica de Docker Desktop, eliminar las imágenes y volúmenes creados.


### Credenciales:
| Contenedor | Nombre de usuario | Contraseña | Puerto |
|:---:|:---:|:---:|:---:|
| _BiblioPocket_ | testing | passtesting | :80 |
| _phpMyAdmin_ | adminBP | passBP | :8001 |


## ✍️ TO-DO:
| Sección | Caso de uso | Operativo |
|:---:|:---:|:---:|
| _Landing_ | La persona usuaria puede crear una cuenta e iniciar sesión | ✅ |
| _Landing_ | La persona usuaria, al crear una cuenta, recibirá un correo con el código de verificación que será requerido para darse de alta | ❌ |
| _Landing_ | La persona usuaria, cuando quiere iniciar sesión pero se ha olvidado su contraseña, se le enviará un código de recuperación | ❌ |
| Inicio | La persona usuaria puede visualizar sus últimos libros añadidos | ✅ |
| Mi estantería | La persona usuaria puede buscar un libro a través de la API de _Google Books_ y añadirlo a su estantería | ✅ |
| Mi estantería | La persona usuaria puede crear un nuevo libro desde 0 y añadirlo a su estantería | ✅ |
| Mi estantería | La persona usuaria puede consultar los libros añadidos a su estantería | ✅ |
| Mi estantería | La persona usuaria puede modificar los datos de sus libros o eliminarlos de su estantería | ✅ |
| Mi estantería | La persona usuaria puede consultar en base a determinados filtros los libros de su estantería | ✅ |
| Mi estantería | La persona usuaria puede compartir una valoración (numérica [⭐] + reseña) de un libro leído | ❌ |
| Mi estantería | La persona usuaria puede guardar anotaciones asociadas a cualquier libro de su estantería | ❌ |
| Mi perfil | La persona usuaria puede modificar datos relativos a su cuenta personal y darse de baja | ✅ |
| Mi perfil | La persona usuaria puede consultar estadísticas relativas a la actividad de su cuenta en _BiblioPocket_ | ✏️ |