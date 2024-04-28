/* Funcionalidad toggle modo Dark <-> Light */
const toggleIcon = document.querySelector(".darklight");

const propiedadesIcono = {
  light: {
    src: "/client/assets/images/moon-icon.png",
    alt: "Símbolo de una media luna (menguante) para representar el modo oscuro de la web",
    classList: "icon darklight moon"
  },
  dark: {
    src: "/client/assets/images/sun-icon.png",
    alt: "Símbolo de un sol para representar el modo claro de la web",
    classList: "icon darklight sun"
  }
};

function establecerTema (tema) {
  document.documentElement.setAttribute('data-theme', tema);

  if (toggleIcon) {
    toggleIcon.setAttribute("src", propiedadesIcono[tema].src);
    toggleIcon.setAttribute("alt", propiedadesIcono[tema].alt);
    toggleIcon.classList = propiedadesIcono[tema].classList;
  }

  localStorage.setItem('theme', tema);
}

function alternarTema () {
  const temaActual = document.documentElement.getAttribute("data-theme");
  const nuevoTema = (temaActual === "light") ? "dark" : "light";

  establecerTema(nuevoTema);
}

let temaGuardado = localStorage.getItem("theme") || (window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light");
establecerTema(temaGuardado);

if (toggleIcon) {
  toggleIcon.onclick = alternarTema;
  toggleIcon.addEventListener("keyup", function (event) {
    if (event.keyCode === 13) {
      toggleIcon.click();
    }
  });
}