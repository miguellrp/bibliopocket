import { previsualizarNuevaImagen } from "/bibliopocket/client/handlers/previewHandler.js";

const navButtons = document.querySelectorAll(".nav-perfil input");

navButtons.forEach((button) => button.addEventListener("click", seccionActivaListener));
prepararListenerImageUploader();



function prepararListenerImageUploader () {
  const uploader = document.querySelector("#uploader-input");
  const previewer = document.querySelector(".preview");

  uploader.addEventListener("change", () => previsualizarNuevaImagen(uploader, previewer));
}


/* --- FUNCIONES COMPLEMENTARIAS --- */
function seccionActivaListener () {
  const detallesContainer = document.querySelector(".detalles-cuenta");
  const configuracionContainer = document.querySelector(".configuracion");


  if (this == navButtons[0]) {
    detallesContainer.setAttribute("active", "");
    configuracionContainer.removeAttribute("active");
    navButtons[1].removeAttribute("active");
  }
  else {
    configuracionContainer.setAttribute("active", "");
    detallesContainer.removeAttribute("active");
    navButtons[0].removeAttribute("active");
  }
  this.setAttribute("active", "");

}