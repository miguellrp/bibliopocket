const modalBusquedaAPI = document.getElementById("busqueda-libro-modal");
const buscarLibroBtn = document.getElementById("busqueda-button");

buscarLibroBtn.addEventListener("click", () => modalBusquedaAPI.showModal());
anhadirCierreModal(modalBusquedaAPI);

/* Por cada botón de eliminar (cada libro), se le añade su funcionalidad
correspondiente de eliminar o modificar datos del libro en cuestión: */
const eliminarGroupBtn = document.querySelectorAll(".icon.eliminar");
eliminarGroupBtn.forEach(eliminarButton => {
  const libroSeleccionado = eliminarButton.parentNode.parentNode;
  const idLibroEstante = libroSeleccionado.querySelector("#id-libro-estante").value;

  const modalEliminacion = generarModalEliminacion(idLibroEstante);
  eliminarButton.addEventListener("click", () => {
    document.body.appendChild(modalEliminacion);
    modalEliminacion.showModal();
  });
});


const modificarGroupBtn = document.querySelectorAll(".icon.modificar");
modificarGroupBtn.forEach(modificarButton => {
  const libroSeleccionado = modificarButton.parentNode.parentNode;
  const idLibroEstante = libroSeleccionado.querySelector("#id-libro-estante").value;

  const modalModificacion = generarModalModificacion(idLibroEstante);
  modificarButton.addEventListener("click", () => {
    document.body.appendChild(modalModificacion);
    modalModificacion.showModal();
  });
});



/* --- FUNCIONES GENERADORAS --- */
function generarModalEliminacion (idLibroEstante) {
  const modalEliminacionLibro = document.createElement("dialog");
  modalEliminacionLibro.setAttribute("class", "modal");
  modalEliminacionLibro.setAttribute("id", "eliminar-libro-modal");

  modalEliminacionLibro.innerHTML = generarFormEliminacion(idLibroEstante);

  anhadirCierreModal(modalEliminacionLibro);
  return modalEliminacionLibro;

}

function generarModalModificacion (idLibroEstante) {
  const modalModificacionLibro = document.createElement("dialog");
  modalModificacionLibro.setAttribute("class", "modal");
  modalModificacionLibro.setAttribute("id", "modificar-libro-modal");

  modalModificacionLibro.innerHTML = generarFormModificacion(idLibroEstante);

  anhadirCierreModal(modalModificacionLibro);
  return modalModificacionLibro;
}



/* Funcion complementaria para gestionar cierre del modal al clickar en las partes externas del modal: */
function anhadirCierreModal (modal) {
  modal.addEventListener("click", event => {
    let modalBounds = modalBusquedaAPI.getBoundingClientRect();
    if ((event.clientX < modalBounds.left || event.clientX > modalBounds.right)
      || (event.clientY < modalBounds.top || event.clientY > modalBounds.bottom)) {
      modal.close();
    }
  });
}

/* Para no acumular modales en el DOM, cuando se clicka en "Cancelar" => se eliminan de él todos aquellos que se encuentren abiertos en ese momento. */
function cerrarModalesEliminacion () {
  const modalesEliminacion = document.querySelectorAll("#eliminar-libro-modal");

  modalesEliminacion.forEach(modalEliminacion => document.body.removeChild(modalEliminacion));
}

function cerrarModalesModificacion () {
  const modalesModificacion = document.querySelectorAll("#modificar-libro-modal");

  modalesModificacion.forEach(modalModificacion => document.body.removeChild(modalModificacion));
}

/* Funciones complementarias generadoras de los forms correspondientes: */
function generarFormEliminacion (idLibroEstante) {
  return `
    <form action="" method="POST">
      <p>¿Estás seguro de que quieres eliminar este libro de tu estantería?</p>
      <small>Todos sus datos modificados se perderán</small>
      <div class="grupo-buttons">
        <input type="submit" name="eliminar" value="Confirmar">
        <input type="button" value="Cancelar" onclick="cerrarModalesEliminacion()">
      </div>
      <input type="hidden" name="id-libro-estante" value="${idLibroEstante}">
    </form>
  `;
}

function generarFormModificacion (idLibroEstante) {
  // const libroActivo = 

  return `
    <h3>Actualizar datos del libro ✍️</h3>
    <form action="" method="POST">
      <label for="titulo">
      

      <input type="hidden" name="id-libro-estante" value="${idLibroEstante}">
    </form>
  `;
}