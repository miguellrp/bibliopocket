export class Libro {
  constructor (
    id, titulo, subtitulo, autoria, descripcion, portada, numPaginas,
    editorial, anhoPublicacion, enlaceAPI, estado, fechaAdicion) {

    this._id = id;
    this._titulo = capitalizarPrimerCaracter(titulo);
    this._subtitulo = capitalizarPrimerCaracter(subtitulo);
    this._autoria = autoria.join(" | ");
    this._descripcion = capitalizarPrimerCaracter(descripcion);
    this._portada = portada;
    this._numPaginas = numPaginas;
    this._editorial = capitalizarPrimerCaracter(editorial);
    this._anhoPublicacion = getAnhoPublicacion(anhoPublicacion);
    this._enlaceAPI = enlaceAPI;
    this._estado = estado;
    this._fechaAdicion = fechaAdicion;
  }

  get id () { return this._id }
  get titulo () { return this._titulo }
  get subtitulo () { return this._subtitulo }
  get autoria () { return this._autoria }
  get descripcion () { return this._descripcion }
  get portada () { return this._portada }
  get numPaginas () { return this._numPaginas }
  get editorial () { return this._editorial }
  get anhoPublicacion () { return this._anhoPublicacion }
  get enlaceAPI () { return this._enlaceAPI }
  get estado () { return this._estado }
  get fechaAdicion () { return this._fechaAdicion }
}

/* Funcións complementarias para formato: */
function capitalizarPrimerCaracter (cadena) {
  return cadena.charAt(0).toUpperCase() + cadena.slice(1);
}

function getAnhoPublicacion (fechaPublicacion) {
  let anhoPublicacion = new Date(fechaPublicacion).getFullYear();

  return Number.isInteger(anhoPublicacion) ? anhoPublicacion : "s.f.";
}