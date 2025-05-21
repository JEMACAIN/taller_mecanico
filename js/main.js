document.addEventListener("DOMContentLoaded", () => {
  // Menú móvil
  const menuToggle = document.getElementById("menuToggle")
  const navMenu = document.getElementById("navMenu")

  if (menuToggle && navMenu) {
    menuToggle.addEventListener("click", () => {
      navMenu.classList.toggle("active")
      menuToggle.classList.toggle("active")
    })
  }

  // Slider de promociones
  const promocionesSlider = document.getElementById("promocionesSlider")
  const prevPromo = document.getElementById("prevPromo")
  const nextPromo = document.getElementById("nextPromo")

  if (promocionesSlider && prevPromo && nextPromo) {
    const slides = promocionesSlider.querySelectorAll(".promo-slide")
    let currentSlide = 0

    // Ocultar todos los slides excepto el primero
    for (let i = 1; i < slides.length; i++) {
      slides[i].style.display = "none"
    }

    // Función para mostrar un slide específico
    function showSlide(n) {
      // Ocultar todos los slides
      for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none"
      }

      // Mostrar el slide actual
      slides[n].style.display = "block"
    }

    // Evento para el botón anterior
    prevPromo.addEventListener("click", () => {
      currentSlide--
      if (currentSlide < 0) {
        currentSlide = slides.length - 1
      }
      showSlide(currentSlide)
    })

    // Evento para el botón siguiente
    nextPromo.addEventListener("click", () => {
      currentSlide++
      if (currentSlide >= slides.length) {
        currentSlide = 0
      }
      showSlide(currentSlide)
    })

    // Cambiar automáticamente cada 5 segundos
    setInterval(() => {
      currentSlide++
      if (currentSlide >= slides.length) {
        currentSlide = 0
      }
      showSlide(currentSlide)
    }, 5000)
  }

  // Animación de scroll suave para los enlaces internos
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()

      const targetId = this.getAttribute("href")
      const targetElement = document.querySelector(targetId)

      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 100,
          behavior: "smooth",
        })

        // Cerrar el menú móvil si está abierto
        if (navMenu && navMenu.classList.contains("active")) {
          navMenu.classList.remove("active")
          menuToggle.classList.remove("active")
        }
      }
    })
  })

  // Animación al hacer scroll
  const animateOnScroll = () => {
    const elements = document.querySelectorAll(".servicio-card, .producto-card, .razon")

    elements.forEach((element) => {
      const elementPosition = element.getBoundingClientRect().top
      const windowHeight = window.innerHeight

      if (elementPosition < windowHeight - 100) {
        element.classList.add("animate")
      }
    })
  }

  // Ejecutar la animación al cargar la página
  animateOnScroll()

  // Ejecutar la animación al hacer scroll
  window.addEventListener("scroll", animateOnScroll)
})
