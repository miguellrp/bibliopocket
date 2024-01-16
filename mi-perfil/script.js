import { previsualizarNuevaImagen } from "/bibliopocket/client/handlers/previewHandler.js";

prepararListenerImageUploader();

function prepararListenerImageUploader () {
  const uploader = document.querySelector("#uploader-input");
  const previewer = document.querySelector(".preview");

  uploader.addEventListener("change", () => previsualizarNuevaImagen(uploader, previewer));
}