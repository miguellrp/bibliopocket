const uploader = document.querySelector("#uploader-input");
const previewer = document.querySelector(".preview");

uploader.addEventListener("change", () => previsualizarNuevaImagen(uploader, previewer));


function previsualizarNuevaImagen (uploader, previewer) {
  const [nuevaImagen] = uploader.files
  if (nuevaImagen) {
    const nuevaImagenURL = URL.createObjectURL(nuevaImagen);
    previewer.src = nuevaImagenURL;
  }
}

