class CustomHeader extends HTMLElement {
  constructor () {
    super();
    this.attachShadow({ mode: "open" });
  }

  connectedCallback () {
    const paginaActiva = this.getAttribute('pagina-activa') || '';
    const siteType = this.getAttribute('site-type');

    this.shadowRoot.innerHTML = /* html */ `
    <style>
      header {
        position: sticky;
        display: flex;
        justify-content: space-between;
        width: 100vw;
        z-index: 10;

        background-color: var(--header-background);
        box-shadow: 1px 1px 22px #00000060;

        & ul {
          display: flex;
          align-items: center;
          flex-direction: row;
          column-gap: 50px;
          margin: 15px;
          
          list-style: none;

          & a, & input[name="log-out"] {
            padding: 3px;
            font-weight: bold;

            &:hover {
              color: color-mix(in srgb, var(--primary-color) 100%, #fff 35%);
            }
          }

          & .pagina-activa {
            & a {
              color: var(--header-active-color);

              &:hover {
                color: var(--header-active-color);
                filter: drop-shadow(0 0 1.5px var(--header-active-color));
              }
            }
          }
        }
        
        & a {
              text-decoration: none;
              color: var(--primary-color);
            }

        & input[type="checkbox"] {
          display: none;
        }
      }

      h1 {
        padding: 5px !important;
        margin: 15px;
        letter-spacing: -1px;
        text-shadow: 1.5px 1.5px 0 var(--secondary-color);
      }

      h1, a, input[name="log-out"], .hamburger-icon {
        padding: 0 2.5px;
        outline: 2px solid transparent;
        border-radius: 5px;
        transition: .3s ease;

        &:focus-visible {
          outline: 2px solid var(--header-active-color);
        }
      }

      .icon {
        display: flex;
        margin: auto;
        height: -moz-fit-content;
        height: fit-content;
      }

      input[type="submit"] {
        margin-bottom: 0;
        font-family: LTCushion;
        font-size: 16px;
        font-weight: bold;

        border: none;
        color: var(--primary-color);
        background-color: transparent;
        cursor: pointer;
      }

      /* DISEÑO HAMBURGER-ICON y apartado responsive a partir de: Álvaro Trigo (https://codepen.io/alvarotrigo/pen/XWejzjR) */
      .hamburger-icon {
        position: absolute;
        height: 23px;
        width: 35px;
        padding: 5px !important;
        top: 25px;
        right: 15px;
        z-index: 2;
        flex-direction: column;
        justify-content: space-between;
        display: none;

        & .line {
          display: block;
          height: 4.5px;
          width: 100%;
          border-radius: 10px;
          background: var(--primary-color);
        }

        & .line1 {
            transform-origin: 0% 0%;
            transition: transform 0.4s ease-in-out;
          }

          & .line2 {
            transition: transform 0.2s ease-in-out;
          }

          & .line3 {
            transform-origin: 0% 100%;
            transition: transform 0.4s ease-in-out;
          }
      }

      input[type="checkbox"]:checked ~ .hamburger-icon .line1 {
        transform: rotate(35deg);
        height: 3px;
      }

      input[type="checkbox"]:checked ~ .hamburger-icon .line2 {
        transform: scaleY(0);
      }

      input[type="checkbox"]:checked ~ .hamburger-icon .line3 {
        transform: rotate(-35deg);
        height: 3px;
      }

      input[type="checkbox"]:checked ~ .menu-items {
        transform: translateX(0);
      }

      input[type="checkbox"]:focus-visible ~ .hamburger-icon {
        outline: 2px solid var(--header-active-color);
      }

      ::selection {
        color: var(--background-color);
        background: var(--primary-color);
      }

      ${siteType != "admin" ? /* css */ `@media (max-width: 788px) {
        header {
          & .menu-items {
            flex-direction: column;
            position: fixed;
            margin: 0;
            padding: 0 30px;
            top: 0;
            right: 0;
            background: var(--header-background);
            max-width: 300px;
            height: 100vh;
            transform: translateX(150%);
            transition: transform 0.5s ease-in-out;
            box-shadow: -2px 0px 5px 1px #00000060;
            row-gap: 25px;

            & li, & input[type="submit"] {
              font-size: 1.3rem;
              font-weight: bold;
            }

            & li {
              margin-bottom: 1.5rem;
            }

            & li:first-child {
              margin-top: 120px;
            }
          }

          & input[type="checkbox"] {
            display: block;
            position: fixed;
            top: 25px;
            right: 21px;
            
            width: 33px;
            height: 27px;
            opacity: 0;
            z-index: 3;
            cursor: pointer;
          }

          & .hamburger-icon {
            display: flex;
            position: fixed;
          }
        }

        input[type="checkbox"]:not(:checked), input[type="checkbox"]:not(:checked) ~ .hamburger-icon {
          position: absolute;
          transition: .3s ease;
        }
      }`
        : ""
      }
  </style>
  `;

    if (siteType == "admin") this.shadowRoot.innerHTML += /* html */ `
      <header>
      <h1><a href="/admin">BiblioPocket 📚</a></h1>
        <ul class="menu-items">
          <li>
            <form action="/index.php" method="POST">
              <input type="submit" value="SALIR" name="log-out">
            </form>
        </li>
      </ul>
    </header>
      `;
    else this.shadowRoot.innerHTML += /* html */ `
    <header>
    <h1><a href="/inicio">BiblioPocket 📚</a></h1>
      <input type="checkbox">
      <div class="hamburger-icon">
        <span class="line line1"></span>
        <span class="line line2"></span>
        <span class="line line3"></span>
      </div>
      <ul class="menu-items">
        <li class="${paginaActiva === 'inicio' ? 'pagina-activa' : ''}">
          <a href="/inicio">INICIO</a>
        </li>
        <li class="${paginaActiva === 'mi-estanteria' ? 'pagina-activa' : ''}">
          <a href="/mi-estanteria">MI ESTANTERÍA</a>
        </li>
        <li class="${paginaActiva === 'mi-perfil' ? 'pagina-activa' : ''}">
          <a href="/mi-perfil">MI PERFIL</a>
        </li>
        <li>
          <form action="/index.php" method="POST">
            <input type="submit" value="SALIR" name="log-out">
          </form>
        </li>
      </ul>
    </header>
    `;

    this.setListenerKeyHamburgerIcon();
  }

  setListenerKeyHamburgerIcon () {
    const checkboxIcon = this.shadowRoot.querySelector("input[type=checkbox]");

    if (checkboxIcon != null) {
      checkboxIcon.addEventListener("keydown", (event) => {
        if (event.key == "Enter") checkboxIcon.checked = !checkboxIcon.checked;
      });
    }
  }
}


customElements.define("custom-header", CustomHeader);