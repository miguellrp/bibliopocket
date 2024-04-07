export function generarModalNuevoLibro () {
  const modalNuevoLibro = document.createElement("dialog");
  modalNuevoLibro.setAttribute("class", "modal");
  modalNuevoLibro.setAttribute("id", "nuevo-libro-modal");

  modalNuevoLibro.innerHTML = generarFormNuevoLibro();
  document.body.appendChild(modalNuevoLibro);

  return modalNuevoLibro;
}

function generarFormNuevoLibro () {
  const portadaPlaceholder = "/bibliopocket/client/assets/images/portadas/placeholder-portada-libro.webp"

  return /* html */` 
    <h2>Añadir nuevo libro desde 0 ✍️</h2>
    <form action="" method="POST">
      <div class="wrap-portada">
        <img src="${portadaPlaceholder}" class="portada" alt="Portada del nuevo libro">
        <div class="portada-upload">
          <label for="portada-input">
            <img src="/bibliopocket/client/assets/images/pencil-icon.png" class="lapiz-icon">
          </label>
          <input id="portada-input" type="file" accept="image/*" name="portada" />
        </div>   
      </div>
      <div class="datos-cabecera">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" class="input-txt" name="titulo">

        <label for="subtitulo">Subtítulo:</label>
        <input type="text" id="subtitulo" class="input-txt" name="subtitulo">
      </div>

      <label for="descripcion">Descripción:</label>
      <textarea id="descripcion" class="input-txt" name="descripcion"></textarea>

      <label for="autoria">Autoría:</label>
      <input type="text" id="autoria" class="input-txt" name="autoria">

      <label for="num-paginas">Nº de páginas:</label>
      <input type="text" id="num-paginas" class="input-txt" name="numPaginas">

      <label for="editorial">Editorial:</label>
      <input type="text" id="editorial" class="input-txt" name="editorial">

      <label for="anho-publicacion">Año de publicación:</label>
      <input type="text" id="anho-publicacion" class="input-txt" name="anhoPublicacion">

      <label>Estado:</label>
      <div class="grupo-estados-libro">
        <input type="radio" name="estado" id="leido" value="leido" checked="false">
        <label for="leido">Leído</label>
        <input type="radio" name="estado" id="leyendo" value="leyendo" checked="false">
        <label for="leyendo">Leyendo</label>
        <input type="radio" name="estado" id="pendiente" value="pendiente" checked="true">
        <label for="pendiente">Pendiente</label>
      </div>

      <label for="categorias">Categorías:</label>
      <input type="text" id="categorias">

      <input type="submit" value="Añadir libro" name="anhadir-nuevo-libro">
      <input type="hidden" name="portada" value="${portadaPlaceholder}">
    </form>
  `;
}