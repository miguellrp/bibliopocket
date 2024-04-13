# BiblioPocket 📚
_Biblioteca virtual en la que la persona usuaria podrá buscar libros, guardarlos, modificar sus datos y categorizarlos. Creado con PHP, CSS y JS nativo (y haciendo uso de WebComponents)._

<img src="demo/demo_pantalla_inicial.png" alt="Captura de pantalla de la vista inicial de BiblioPocket 📚" >

## ✍️ TO-DO:
- [x] Sección "Mi perfil":
  - [x] Modificar datos de la persona usuaria y aplicar los cambios en la DB (sección "Mi perfil").
  - [x] * Previsualización en cambio de foto de perfil de la persona usuaria.
  - [x] Estilado de la sección.

- [ ] Sección "Mi estantería":
  - [x] Añadir modal de creación de libro desde 0.
  - [ ] * Previsualización en cambio de imagen de portada (modal [modificación | creación] de libro).
  - [ ] Estado de lecturas de los libros (estantería personal):
    - [ ] Cargar de la DB y mostrar correctamente en el _front_ la propiedad `categorías` de cada libro.
    - [x] Diferenciar visualmente los 3 estados principales en los que se puede encontrar cada libro (`Leído` || `Leyendo` || `Pendiente`).

- [ ] Refactorizar código:
  - [ ] Adecuar nombre de variables (kebab-case ➡️ camelCase) y funciones.
  - [ ] Optimizar fragmentos de código correspondientes a la toma de datos de la DB y su carga en el _front_.