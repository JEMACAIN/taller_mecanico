document.addEventListener("DOMContentLoaded", () => {
  // Toggle sidebar
  const sidebarToggle = document.getElementById("sidebarToggle")
  const sidebar = document.querySelector(".sidebar")
  const mainContent = document.querySelector(".main-content")

  if (sidebarToggle && sidebar && mainContent) {
    sidebarToggle.addEventListener("click", () => {
      sidebar.classList.toggle("active")
      mainContent.classList.toggle("expanded")
    })
  }

  // User dropdown
  const dropdownToggle = document.querySelector(".dropdown-toggle")
  const dropdownMenu = document.querySelector(".dropdown-menu")

  if (dropdownToggle && dropdownMenu) {
    dropdownToggle.addEventListener("click", () => {
      dropdownMenu.classList.toggle("show")
    })

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener("click", (event) => {
      if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.classList.remove("show")
      }
    })
  }

  // Confirmación para eliminar
  const deleteButtons = document.querySelectorAll(".delete-btn")

  if (deleteButtons) {
    deleteButtons.forEach((button) => {
      button.addEventListener("click", (e) => {
        if (!confirm("¿Está seguro de que desea eliminar este elemento? Esta acción no se puede deshacer.")) {
          e.preventDefault()
        }
      })
    })
  }

  // Vista previa de imagen
  const imageInput = document.getElementById("imagen")
  const imagePreview = document.getElementById("imagePreview")

  if (imageInput && imagePreview) {
    imageInput.addEventListener("change", function () {
      const file = this.files[0]

      if (file) {
        const reader = new FileReader()

        reader.addEventListener("load", () => {
          imagePreview.src = reader.result
          imagePreview.style.display = "block"
        })

        reader.readAsDataURL(file)
      }
    })
  }

  // Editor de texto enriquecido
  const textareas = document.querySelectorAll(".rich-editor")

  if (textareas.length > 0) {
    textareas.forEach((textarea) => {
      // Aquí se podría inicializar un editor WYSIWYG como TinyMCE o CKEditor
      console.log("Editor inicializado para", textarea.id)
    })
  }

  // Datepicker para campos de fecha
  const dateInputs = document.querySelectorAll(".date-picker")

  if (dateInputs.length > 0) {
    dateInputs.forEach((input) => {
      // Aquí se podría inicializar un datepicker
      input.type = "date"
    })
  }

  // Validación de formularios
  const forms = document.querySelectorAll(".needs-validation")

  if (forms.length > 0) {
    forms.forEach((form) => {
      form.addEventListener(
        "submit",
        (event) => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }

          form.classList.add("was-validated")
        },
        false,
      )
    })
  }
})
