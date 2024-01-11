const modalBusquedaAPI = document.getElementById("busqueda-libro-modal");
const buscarLibroBtn = document.getElementById("busqueda-button");

buscarLibroBtn.addEventListener("click", () => modalBusquedaAPI.showModal());
anhadirCierreModal(modalBusquedaAPI);


const eliminarGroupBtn = document.querySelectorAll(".icon.eliminar");
eliminarGroupBtn.forEach(eliminarButton => {
  const libroVinculado = eliminarButton.parentNode.parentNode;
  const datosLibro = libroVinculado.querySelector("form").elements;
  const idLibroSeleccionado = datosLibro.id.value;

  const modalEliminacion = generarModalEliminacion(idLibroSeleccionado);
  eliminarButton.addEventListener("click", () => {
    eliminarModalActivo();
    document.body.appendChild(modalEliminacion);
    modalEliminacion.showModal();
  });
});


const modificarGroupBtn = document.querySelectorAll(".icon.modificar");
modificarGroupBtn.forEach(modificarButton => {
  const libroVinculado = modificarButton.parentNode.parentNode;

  const modalModificacion = generarModalModificacion(libroVinculado);
  modificarButton.addEventListener("click", () => {
    eliminarModalActivo();
    document.body.appendChild(modalModificacion);
    modalModificacion.showModal();
  });
});


/* --- FUNCIONES GENERADORAS --- */
function generarModalEliminacion (idLibro) {
  const modalEliminacionLibro = document.createElement("dialog");
  modalEliminacionLibro.setAttribute("class", "modal");
  modalEliminacionLibro.setAttribute("id", "eliminar-libro-modal");

  modalEliminacionLibro.innerHTML = generarFormEliminacion(idLibro);
  anhadirCierreModal(modalEliminacionLibro);

  return modalEliminacionLibro;
}

function generarModalModificacion (libroVinculado) {
  const modalModificacionLibro = document.createElement("dialog");
  modalModificacionLibro.setAttribute("class", "modal");
  modalModificacionLibro.setAttribute("id", "modificar-libro-modal");

  modalModificacionLibro.innerHTML = generarFormModificacion(libroVinculado);
  anhadirCierreModal(modalModificacionLibro);

  return modalModificacionLibro;
}

function generarFormEliminacion (idLibro) {
  return /* html */`
    <form action="" method="POST">
      <p>¿Estás seguro de que quieres eliminar este libro de tu estantería?</p>
      <small>Todos sus datos modificados se perderán</small>
      <div class="grupo-buttons">
        <input type="submit" name="eliminar" value="Confirmar">
        <input type="button" value="Cancelar">
      </div>
      <input type="hidden" name="idLibroEstante" value="${idLibro}">
    </form>
  `;
}

function generarFormModificacion (libroVinculado) {
  libroVinculado = libroVinculado.querySelector("form").elements;
  const datosLibro = {
    "id": libroVinculado.id.value,
    "titulo": libroVinculado.titulo.value,
    "subtitulo": libroVinculado.subtitulo.value,
    "autoria": libroVinculado.autoria.value,
    "editorial": libroVinculado.editorial.value,
    "anhoPublicacion": libroVinculado.anhoPublicacion.value
  };

  return /* html */` 
    <h2>Modificar datos del libro ✍️</h2>
    <form action="" method="POST">
      <label for="titulo">Título:</label>
      <input type="text" id="titulo" class="input-txt" name="titulo" value="${datosLibro.titulo}">

      <label for="subtitulo">Subtítulo:</label>
      <input type="text" id="subtitulo" class="input-txt" name="subtitulo" value="${datosLibro.subtitulo}">

      <label for="autoria">Autoría:</label>
      <input type="text" id="autoria" class="input-txt" name="autoria" value="${datosLibro.autoria}">

      <label for="editorial">Editorial:</label>
      <input type="text" id="editorial" class="input-txt" name="editorial" value="${datosLibro.editorial}">

      <label for="anho-publicacion">Año de publicación:</label>
      <input type="text" id="anho-publicacion" class="input-txt" name="anhoPublicacion" value="${datosLibro.anhoPublicacion}">

      <label>Estado:</label>
      <div class="grupo-estados-libro">
        <input type="radio" name="estado" id="leido" value="leido">
        <label for="leido">Leído</label>
        <input type="radio" name="estado" id="leyendo" value="leyendo">
        <label for="leyendo">Leyendo</label>
        <input type="radio" name="estado" id="pendiente" value="pendiente">
        <label for="pendiente">Pendiente</label>
      </div>

      <label for="categorias">Categorías:</label>
      <input type="text" id="categorias">

      <input type="submit" value="Guardar cambios" name="modificar-libro">
      <input type="hidden" name="idLibroEstante" value="${datosLibro.id}" autofocus>
    </form>
  `;
}


/* --- FUNCIONES COMPLEMENTARIAS --- */
function anhadirCierreModal (modal) {
  modal.addEventListener("click", event => {
    let modalBounds = modal.getBoundingClientRect();
    if ((event.clientX < modalBounds.left || event.clientX > modalBounds.right)
      || (event.clientY < modalBounds.top || event.clientY > modalBounds.bottom))
      modal.close();
  });
}

/* Con la finalidad de que no se vayan almacenando modales en el DOM, cada vez que se
abre uno, se elimina el anterior que estuviese activo (exceptuando el de búsqueda de libros). */
function eliminarModalActivo () {
  const modalActivo = document.querySelector("#modificar-libro-modal") || document.querySelector("#eliminar-libro-modal");

  if (modalActivo != null) document.body.removeChild(modalActivo);
}