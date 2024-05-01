/* ELEMENTOS DOM */
const contrasenhaInputTag = document.getElementById("contrasenha-reg");
const contrasenhaConfirmTag = document.getElementById("confirm-contrasenha");
const registroSubmit = document.querySelector(".submit-btn");
const formRegistro = document.querySelector("#form-registro");
const notificacionRegistro = document.querySelector(".notificacion-registro");

/* VARIABLES GLOBALES */
const EXTENSION_MINIMA_USERNAME = 5;
const EXTENSION_MINIMA_CONTRASENHA = 6;

/* COLORES PARA FEEDBACK USUARIO EN VALIDACIÓN: */
const colorValido = "#46d01c";
const colorInvalido = "#ba1313";


/* --- FUNCIONES VALIDADORAS PARA REGISTRO: --- */
function camposCubiertos () {
  const camposRegistro = document.querySelectorAll(".input-registro");
  let camposCubiertos = true;

  camposRegistro.forEach(campo => {
    if (campo.value == "") {
      camposCubiertos = false;
    };
  });

  return camposCubiertos;
}

function camposValidos () {
  const emailInput = document.querySelector("input[type='email']").value;
  const usernameInput = document.querySelector("#nombre-usuario-reg").value;

  return (emailInput.includes("@") && emailInput.includes(".") && usernameInput.length >= EXTENSION_MINIMA_USERNAME)
}

function confirmacionValida () {
  const contrasenhaInput = contrasenhaInputTag.value;
  const contrasenhaConfirm = contrasenhaConfirmTag.value;

  // Esta función devuelve un booleano si se cumple o no la condición y,
  // a su vez, marca el campo como válido o inválido
  if (contrasenhaInput === contrasenhaConfirm) {
    contrasenhaConfirmTag.setCustomValidity("");                              // :user-valid ✅
    return true;
  }
  else {
    contrasenhaConfirmTag.setCustomValidity("Las contraseñas no coinciden");  // :user-invalid ❌
    return false;
  }
}

function extensionContrasenhaValida () {
  const contrasenhaInput = contrasenhaInputTag.value;

  return contrasenhaInput.length >= EXTENSION_MINIMA_CONTRASENHA;
}

contrasenhaConfirmTag.addEventListener("keyup", extensionContrasenhaValida);

// Se va validando que se cumplan todas las condiciones requeridas para el registro con cada pulsación de tecla ("keyup"):
formRegistro.addEventListener("keyup", () => {
  if (camposValidos() && camposCubiertos() && (extensionContrasenhaValida() && confirmacionValida())) {
    notificacionRegistro.style.opacity = 0;
    notificacionRegistro.style.transition = ".3s ease";
    registroSubmit.disabled = false;
  }
  else {
    registroSubmit.disabled = true;
    if (!extensionContrasenhaValida() && contrasenhaInputTag.value != "")
      notificacionRegistro.textContent = `⚠️ La contraseña debe tener una extensión mínima de ${EXTENSION_MINIMA_CONTRASENHA} caracteres`;
    else if (!confirmacionValida() && contrasenhaConfirmTag.value != "")
      notificacionRegistro.textContent = "⚠️ Ambas contraseñas deben coincidir.";
    else {
      notificacionRegistro.textContent = "Faltan campos requeridos por cubrir (*)";
    }
    notificacionRegistro.style.opacity = 1;
  }
});