import * as modalesEstanteria from "/client/handlers/estanteriaModalsHandler.js";

export function prepararListenersLibro () {
  listenersModalModificarLibro();
  listenersModalEliminarLibro();
}

function listenersModalEliminarLibro () {
  const eliminarGroupBtn = document.querySelectorAll(".icon.eliminar");
  eliminarGroupBtn.forEach(eliminarButton => {
    const libroVinculado = eliminarButton.parentNode.parentNode;
    const datosLibro = libroVinculado.querySelector("form").elements;
    const idLibroSeleccionado = datosLibro.id.value;

    eliminarButton.addEventListener("click", () => {
      const modalEliminacion = modalesEstanteria.getModalEliminacion(idLibroSeleccionado);
      generarCierreModal(modalEliminacion);
      modalEliminacion.showModal();
    });
  });
}

function listenersModalModificarLibro () {
  const modificarGroupBtn = document.querySelectorAll(".icon.modificar");
  modificarGroupBtn.forEach(modificarButton => {
    const libroVinculado = modificarButton.parentNode.parentNode;

    modificarButton.addEventListener("click", () => {
      const modalModificacion = modalesEstanteria.getModalDatosLibro(libroVinculado);
      generarCierreModal(modalModificacion);
      modalModificacion.querySelector("#titulo").blur();

      const estadosLibroTags = modalModificacion.querySelector(".grupo-estados-libro").querySelectorAll(":scope > input");
      estadosLibroTags.forEach((estadoLibroTag) => estadoLibroTag.addEventListener("click", listenersEstadosLibro));

      modalModificacion.showModal();
    });
  });
}

export function listenersEstadosLibro () {
  const estadosTags = this.parentNode.querySelectorAll("input[type='radio']");

  this.setAttribute("checked", "");
  estadosTags.forEach((estadoTag) => {
    if (estadoTag != this) estadoTag.removeAttribute("checked");
  });

}

/* --- FUNCIONES COMPLEMENTARIAS --- */
export function generarCierreModal (modal) {
  modal.addEventListener("mousedown", event => {
    let modalBounds = modal.getBoundingClientRect();
    if ((event.clientX < modalBounds.left || event.clientX > modalBounds.right)
      || (event.clientY < modalBounds.top || event.clientY > modalBounds.bottom))
      modal.close();
  });
}