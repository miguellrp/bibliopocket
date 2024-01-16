export function previsualizarNuevaImagen (uploader, previewer) {
  const [nuevaImagen] = uploader.files
  if (nuevaImagen) {
    const nuevaImagenURL = URL.createObjectURL(nuevaImagen);
    previewer.src = nuevaImagenURL;
  }
}