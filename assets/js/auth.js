/**
 * Authentication Module
 * Handles user authentication, session management, and authorization
 */

// Declare DB variable (assuming it's globally available or imported elsewhere)
let DB

// Initialize the database if needed
if (typeof DB !== "undefined") {
  DB.init()
}

// Auth namespace
const Auth = {
  /**
   * Attempt to log in a user
   * @param {string} email - User email
   * @param {string} password - User password
   * @param {string} role - User role (client or admin)
   * @returns {Object} - Result with success flag and user data or error message
   */
  login(email, password, role) {
    // Get all users
    const users = DB.getAll(DB.KEYS.USERS)

    // Find user with matching email and role
    const user = users.find((u) => u.email.toLowerCase() === email.toLowerCase() && u.role === role)

    // Check if user exists and password matches
    if (user && user.password === password) {
      // Create a session object (exclude password)
      const session = {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        avatar: user.avatar,
        loggedInAt: new Date().toISOString(),
      }

      // Store session in localStorage
      localStorage.setItem(DB.KEYS.CURRENT_USER, JSON.stringify(session))

      return {
        success: true,
        user: session,
      }
    }

    return {
      success: false,
      message: "Invalid email or password",
    }
  },

  /**
   * Log out the current user
   */
  logout() {
    localStorage.removeItem(DB.KEYS.CURRENT_USER)
  },

  /**
   * Get the current logged-in user
   * @returns {Object|null} - Current user or null if not logged in
   */
  getCurrentUser() {
    const userData = localStorage.getItem(DB.KEYS.CURRENT_USER)
    return userData ? JSON.parse(userData) : null
  },

  /**
   * Check if a user is logged in
   * @returns {boolean} - True if user is logged in
   */
  isLoggedIn() {
    return !!this.getCurrentUser()
  },

  /**
   * Check if current user has a specific role
   * @param {string} role - Role to check
   * @returns {boolean} - True if user has the role
   */
  hasRole(role) {
    const user = this.getCurrentUser()
    return user && user.role === role
  },

  /**
   * Redirect to login page if not logged in
   */
  requireLogin() {
    if (!this.isLoggedIn()) {
      window.location.href = "../pages/login.html"
    }
  },

  /**
   * Redirect to appropriate dashboard based on role
   */
  redirectToDashboard() {
    const user = this.getCurrentUser()
    if (user) {
      if (user.role === "admin") {
        window.location.href = "../pages/admin-dashboard.html"
      } else {
        window.location.href = "../pages/client-dashboard.html"
      }
    }
  },

  /**
   * Require a specific role, redirect if not authorized
   * @param {string} role - Required role
   */
  requireRole(role) {
    if (!this.hasRole(role)) {
      if (this.isLoggedIn()) {
        // Redirect to appropriate dashboard
        this.redirectToDashboard()
      } else {
        // Redirect to login
        window.location.href = "../pages/login.html"
      }
    }
  },
}

// Event listeners for login page
document.addEventListener("DOMContentLoaded", () => {
  // Check if we're on the login page
  const loginForm = document.getElementById("loginForm")
  if (loginForm) {
    // If user is already logged in, redirect to dashboard
    if (Auth.isLoggedIn()) {
      Auth.redirectToDashboard()
      return
    }

    // Handle login form submission
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault()

      const email = document.getElementById("email").value
      const password = document.getElementById("password").value
      const userType = document.getElementById("userType").value

      const result = Auth.login(email, password, userType)

      if (result.success) {
        // Show success notification
        showNotification("Login successful! Redirecting...", "success")

        // Redirect to appropriate dashboard
        setTimeout(() => {
          Auth.redirectToDashboard()
        }, 1000)
      } else {
        // Show error notification
        showNotification(result.message, "error")
      }
    })

    // Handle demo login
    const demoLoginBtn = document.getElementById("demoLogin")
    if (demoLoginBtn) {
      demoLoginBtn.addEventListener("click", (e) => {
        e.preventDefault()

        const userType = document.getElementById("userType").value
        let email, password

        if (userType === "admin") {
          email = "admin@contactflow.com"
          password = "admin123"
        } else {
          email = "john@example.com"
          password = "client123"
        }

        // Fill in the form
        document.getElementById("email").value = email
        document.getElementById("password").value = password

        // Submit the form
        const result = Auth.login(email, password, userType)

        if (result.success) {
          showNotification("Demo login successful! Redirecting...", "success")
          setTimeout(() => {
            Auth.redirectToDashboard()
          }, 1000)
        } else {
          showNotification(result.message, "error")
        }
      })
    }
  }

  // Handle logout button
  const logoutBtn = document.getElementById("logoutBtn")
  if (logoutBtn) {
    logoutBtn.addEventListener("click", () => {
      Auth.logout()
      window.location.href = "../pages/login.html"
    })
  }

  // Check if we're on a protected page
  const dashboardContainer = document.querySelector(".dashboard-container")
  if (dashboardContainer) {
    // Require login for all dashboard pages
    Auth.requireLogin()

    // Check for specific pages and require appropriate role
    const pathname = window.location.pathname

    if (pathname.includes("admin-")) {
      Auth.requireRole("admin")
    } else if (
      pathname.includes("client-") ||
      pathname.includes("request-service") ||
      pathname.includes("view-requests") ||
      pathname.includes("profile")
    ) {
      Auth.requireRole("client")
    }

    // Update user info in the header
    updateUserInfo()

    // Initialize theme toggle
    initThemeToggle()

    // Initialize sidebar toggle
    initSidebarToggle()
  }
})

/**
 * Update user information in the header
 */
function updateUserInfo() {
  const user = Auth.getCurrentUser()
  if (user) {
    // Update user name
    const userNameElements = document.querySelectorAll("#userName, #welcomeUserName")
    userNameElements.forEach((el) => {
      if (el) {
        if (el.id === "welcomeUserName") {
          el.textContent = user.name.split(" ")[0] // First name only
        } else {
          el.textContent = user.name
        }
      }
    })

    // Update user avatar
    const userAvatarElement = document.getElementById("userAvatar")
    if (userAvatarElement) {
      userAvatarElement.src = user.avatar
      userAvatarElement.alt = `${user.name}'s Avatar`
    }
  }
}

/**
 * Initialize theme toggle functionality
 */
function initThemeToggle() {
  const themeToggle = document.getElementById("themeToggle")
  if (themeToggle) {
    // Set initial state based on localStorage
    const darkMode = localStorage.getItem(DB.KEYS.DARK_MODE) === "true"
    themeToggle.checked = darkMode
    if (darkMode) {
      document.body.classList.add("dark-theme")
    }

    // Handle toggle change
    themeToggle.addEventListener("change", function () {
      if (this.checked) {
        document.body.classList.add("dark-theme")
        localStorage.setItem(DB.KEYS.DARK_MODE, "true")
      } else {
        document.body.classList.remove("dark-theme")
        localStorage.setItem(DB.KEYS.DARK_MODE, "false")
      }
    })
  }
}

/**
 * Initialize sidebar toggle functionality
 */
function initSidebarToggle() {
  const toggleSidebarBtn = document.getElementById("toggleSidebar")
  const sidebar = document.querySelector(".sidebar")

  if (toggleSidebarBtn && sidebar) {
    toggleSidebarBtn.addEventListener("click", () => {
      sidebar.classList.toggle("expanded")
    })
  }
}

/**
 * Show a notification message
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, error, info, warning)
 */
function showNotification(message, type = "info") {
  const notification = document.getElementById("notification")
  if (notification) {
    // Clear existing classes and add new ones
    notification.className = "notification"
    notification.classList.add(type)
    notification.classList.add("show")

    // Set message
    notification.textContent = message

    // Hide after 3 seconds
    setTimeout(() => {
      notification.classList.remove("show")
    }, 3000)
  }
}
