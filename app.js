// Main application JavaScript file

// Check if dark mode is enabled in localStorage
document.addEventListener("DOMContentLoaded", () => {
  // Check for dark mode preference
  const darkMode = localStorage.getItem("darkMode") === "true"
  if (darkMode) {
    document.body.classList.add("dark-theme")
  }

  // Initialize demo data if on the landing page
  if (window.location.pathname === "/" || window.location.pathname.endsWith("index.html")) {
    initLandingPage()
  }
})

// Initialize landing page functionality
function initLandingPage() {
  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()
      const targetId = this.getAttribute("href")
      if (targetId === "#") return

      const targetElement = document.querySelector(targetId)
      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 80,
          behavior: "smooth",
        })
      }
    })
  })

  // Add animation to feature cards on scroll
  const featureCards = document.querySelectorAll(".feature-card")
  if (featureCards.length > 0) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = 1
            entry.target.style.transform = "translateY(0)"
          }
        })
      },
      { threshold: 0.1 },
    )

    featureCards.forEach((card) => {
      card.style.opacity = 0
      card.style.transform = "translateY(20px)"
      card.style.transition = "opacity 0.5s ease, transform 0.5s ease"
      observer.observe(card)
    })
  }
}
