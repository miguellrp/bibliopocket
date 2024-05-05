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

    eliminarButton.addEventListener("click", () => {
      const modalEliminacion = modalesEstanteria.getModalEliminacion(datosLibro);
      const cancelarBoton = modalEliminacion.querySelector("input[type=button]");
      generarCierreModal(modalEliminacion, cancelarBoton);

      modalEliminacion.showModal();
      modalEliminacion.querySelector("input[type=submit]").blur();
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

      const estadosLibroTags = modalModificacion.querySelector(".grupo-estados-libro").querySelectorAll(":scope > input");
      estadosLibroTags.forEach((estadoLibroTag) => estadoLibroTag.addEventListener("click", listenersEstadosLibro));

      modalModificacion.showModal();
      modalModificacion.querySelector("#titulo").blur();
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
export function generarCierreModal (modal, cancelarBoton = null) {
  modal.addEventListener("mousedown", event => {
    let modalBounds = modal.getBoundingClientRect();
    if ((event.clientX < modalBounds.left || event.clientX > modalBounds.right)
      || (event.clientY < modalBounds.top || event.clientY > modalBounds.bottom))
      modal.close();
  });

  if (cancelarBoton != null) cancelarBoton.addEventListener("click", () => modal.close());
}