let groupModals = document.querySelectorAll(".modal");

/* Se proporciona la funcionalidad de cerrar el <dialog> al clickar fuera de
su recuadro a todos aquellos elementos que pertenezcan a la clase "modal" */
for (let modal of groupModals) {
  modal.addEventListener("click", event => {
    let modalBounds = modal.getBoundingClientRect();
    if ((event.clientX < modalBounds.left || event.clientX > modalBounds.right)
      || (event.clientY < modalBounds.top || event.clientY > modalBounds.bottom))
      modal.close();
  });
}

function openModal (modal) {
  modal.showModal();
}

/*

/* Se asocian los modales con los botones que ejecutarán su showModal correspondiente */
// Modal de Registro
let modalRegistro = document.getElementById("registro");
let registroButton = document.getElementById("registro-button");

registroButton.addEventListener("click", () => openModal(modalRegistro));

// Modal de Iniciar Sesión
let modalLogin = document.getElementById("login");
let loginButton = document.getElementById("login-button");

loginButton.addEventListener("click", () => openModal(modalLogin));