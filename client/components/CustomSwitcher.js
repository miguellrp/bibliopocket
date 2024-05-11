/* Switcher a partir de: Josetxu [https://josetxu.com/creando-toggle-switches-con-css/] */
class CustomSwitcher extends HTMLElement {
  constructor () {
    super();
    this.attachShadow({ mode: "open" });
  }

  connectedCallback () {
    const width = this.getAttribute("custom-width") || 15;
    const height = this.getAttribute("custom-height") || 15;
    const switcherChecked = this.getAttribute("data-checked") === "true";
    const inputElement = switcherChecked ? "<input type=checkbox id=btn checked>" : "<input type=checkbox id=btn>";

    this.shadowRoot.innerHTML = /* html */`
    <style>
      .switcher {
        display: inline-block;
        width: ${width + 30}px;
        height: ${height}px;

        background: #a18078;
        padding: 5px;
        border-radius: 50px;

        cursor: pointer;
      }

      .circle {
        width: ${width}px;
        height: ${height}px;
        background: var(--primary-color);
        display: flex;
        border-radius: 50px;
        transition: all .3s ease 0s;
      }

      input {
        display: none;
      }

      .switcher:has(input:checked) {
        background: #adff7a;

        & .circle {
          margin-left: 30px;
          background: #5cc31d;
          animation: stretch .5s ease;
        }
      }

      @keyframes stretch {
        50% {
          transform: scale(0.4, 0.7);
        } 80% {
          transform: scale(1.1, 1.1);
        } 100% {
          transform: scale(1);
        }
      }
    </style>
    <label for="btn" class="switcher">
      <span class="circle"></span>
      ${inputElement}
   </label>
    `;

    this.setInputHidden();
    this.setListenerOnChange();
  }

  setInputHidden () {
    const inputHidden = document.createElement("input");
    const switcherChecked = this.getAttribute("data-checked") === "true";
    const nameInputHidden = this.getAttribute("data-name");

    inputHidden.type = "hidden";
    if (nameInputHidden != null) inputHidden.name = nameInputHidden;
    inputHidden.value = switcherChecked;

    this.parentNode.appendChild(inputHidden);
  }

  setListenerOnChange () {
    const switcherInput = this.shadowRoot.querySelector("input[type=checkbox]");
    const inputHidden = this.parentNode.querySelector("input[type=hidden]");

    switcherInput.addEventListener("change", () => inputHidden.value = !(inputHidden.value === "true"));

  }
}

customElements.define("custom-switcher", CustomSwitcher);