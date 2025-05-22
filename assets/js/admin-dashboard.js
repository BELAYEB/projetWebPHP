import { Chart } from "@/components/ui/chart"
/**
 * Admin Dashboard JavaScript
 * Handles admin dashboard functionality
 */

// Mock Auth object for demonstration purposes
const Auth = {
  requireRole: (role) => {
    console.log(`Requiring role: ${role}`)
    // In a real application, this would check the user's role and redirect if necessary
  },
  getCurrentUser: () => {
    // Mock user data
    return {
      id: "admin123",
      name: "Admin User",
      role: "admin",
    }
  },
}

// Mock DB object for demonstration purposes
const DB = {
  KEYS: {
    REQUESTS: "requests",
    USERS: "users",
    ACTIVITIES: "activities",
    NOTIFICATIONS: "notifications",
  },
  getAll: (key) => {
    const data = localStorage.getItem(key)
    return data ? JSON.parse(data) : []
  },
  getById: (key, id) => {
    const items = DB.getAll(key)
    return items.find((item) => item.id === id)
  },
}

// Helper functions
function clearElement(element) {
  while (element.firstChild) {
    element.removeChild(element.firstChild)
  }
}

function createElement(tag, attributes, children) {
  const element = document.createElement(tag)
  for (const key in attributes) {
    element[key] = attributes[key]
  }
  if (children) {
    children.forEach((child) => {
      if (typeof child === "string") {
        element.appendChild(document.createTextNode(child))
      } else {
        element.appendChild(child)
      }
    })
  }
  return element
}

function createStatusBadge(status) {
  let className = "status-badge"
  switch (status) {
    case "pending":
      className += " pending"
      break
    case "in-progress":
      className += " in-progress"
      break
    case "completed":
      className += " completed"
      break
    case "cancelled":
      className += " cancelled"
      break
    default:
      className += " unknown"
  }

  const badge = createElement("span", { className: className }, [status.toUpperCase()])
  return badge
}

function formatDate(dateString) {
  const date = new Date(dateString)
  const options = { year: "numeric", month: "long", day: "numeric", hour: "2-digit", minute: "2-digit" }
  return date.toLocaleDateString(undefined, options)
}

function timeAgo(dateString) {
  const date = new Date(dateString)
  const now = new Date()
  const diff = now.getTime() - date.getTime()
  const seconds = Math.floor(diff / 1000)
  const minutes = Math.floor(seconds / 60)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)

  if (days > 0) {
    return days + " days ago"
  } else if (hours > 0) {
    return hours + " hours ago"
  } else if (minutes > 0) {
    return minutes + " minutes ago"
  } else {
    return "Just now"
  }
}

function showNotification(message, type) {
  alert(`${type.toUpperCase()}: ${message}`) // Replace with a proper notification system
}

function showModal(modalId) {
  alert(`Showing modal: ${modalId}`) // Replace with a proper modal display
}

function hideModal(modalId) {
  alert(`Hiding modal: ${modalId}`) // Replace with a proper modal hiding
}

function generateId(prefix) {
  return prefix + "_" + Math.random().toString(36).substr(2, 9)
}

document.addEventListener("DOMContentLoaded", () => {
  // Require admin role
  Auth.requireRole("admin")

  // Initialize dashboard
  initDashboard()

  // Set up event listeners
  setupEventListeners()
})

/**
 * Initialize the dashboard
 */
function initDashboard() {
  // Load dashboard stats
  loadDashboardStats()

  // Load recent requests
  loadRecentRequests()

  // Load request status chart
  loadRequestStatusChart()

  // Load recent activity
  loadRecentActivity()

  // Load satisfaction chart
  loadSatisfactionChart()

  // Load notifications
  loadNotifications()
}

/**
 * Load dashboard statistics
 */
function loadDashboardStats() {
  // Get all requests
  const requests = DB.getAll(DB.KEYS.REQUESTS)

  // Count requests by status
  const totalRequests = requests.length
  const inProgressRequests = requests.filter((req) => req.status === "in-progress").length
  const completedRequests = requests.filter((req) => req.status === "completed").length

  // Calculate average rating
  const ratedRequests = requests.filter((req) => req.feedback && req.feedback.rating)
  const avgRating =
    ratedRequests.length > 0
      ? (ratedRequests.reduce((sum, req) => sum + req.feedback.rating, 0) / ratedRequests.length).toFixed(1)
      : "0.0"

  // Update the stats in the UI
  document.getElementById("totalRequests").textContent = totalRequests
  document.getElementById("inProgressRequests").textContent = inProgressRequests
  document.getElementById("completedRequests").textContent = completedRequests
  document.getElementById("avgRating").textContent = avgRating
}

/**
 * Load recent requests
 */
function loadRecentRequests() {
  const recentRequestsList = document.getElementById("recentRequestsList")
  if (!recentRequestsList) return

  // Clear the list
  clearElement(recentRequestsList)

  // Get recent requests
  const requests = DB.getAll(DB.KEYS.REQUESTS)
    .sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))
    .slice(0, 5)

  if (requests.length === 0) {
    // Show empty state
    recentRequestsList.appendChild(
      createElement("div", { className: "empty-state" }, [
        createElement("i", { className: "fas fa-ticket-alt" }),
        createElement("p", {}, "No recent requests"),
      ]),
    )
    return
  }

  // Create request items
  requests.forEach((request) => {
    // Get client info
    const client = DB.getById(DB.KEYS.USERS, request.clientId)

    const requestItem = createElement(
      "div",
      {
        className: "request-item",
        onclick: () => {
          window.location.href = `admin-requests.html?id=${request.id}`
        },
      },
      [
        createElement("div", { className: "request-item-header" }, [
          createElement("div", { className: "request-item-title" }, request.title),
          createStatusBadge(request.status),
        ]),
        createElement("div", { className: "request-item-meta" }, [
          createElement("span", {}, client ? `Client: ${client.name}` : "Unknown Client"),
          createElement("span", {}, formatDate(request.createdAt)),
        ]),
      ],
    )

    recentRequestsList.appendChild(requestItem)
  })
}

/**
 * Load request status chart
 */
function loadRequestStatusChart() {
  const chartCanvas = document.getElementById("requestStatusChart")
  if (!chartCanvas) return

  // Get all requests
  const requests = DB.getAll(DB.KEYS.REQUESTS)

  // Count requests by status
  const statusCounts = {
    pending: 0,
    "in-progress": 0,
    completed: 0,
    cancelled: 0,
  }

  requests.forEach((request) => {
    if (statusCounts.hasOwnProperty(request.status)) {
      statusCounts[request.status]++
    }
  })

  // Create chart
  new Chart(chartCanvas, {
    type: "doughnut",
    data: {
      labels: ["Pending", "In Progress", "Completed", "Cancelled"],
      datasets: [
        {
          data: [statusCounts.pending, statusCounts["in-progress"], statusCounts.completed, statusCounts.cancelled],
          backgroundColor: ["#f39c12", "#3498db", "#2ecc71", "#e74c3c"],
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
        },
      },
    },
  })
}

/**
 * Load recent activity
 */
function loadRecentActivity() {
  const activityList = document.getElementById("recentActivityList")
  if (!activityList) return

  // Clear the list
  clearElement(activityList)

  // Get recent activities
  const activities = DB.getAll(DB.KEYS.ACTIVITIES)
    .sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))
    .slice(0, 5)

  if (activities.length === 0) {
    // Show empty state
    activityList.appendChild(
      createElement("div", { className: "empty-state" }, [
        createElement("i", { className: "fas fa-history" }),
        createElement("p", {}, "No recent activity"),
      ]),
    )
    return
  }

  // Create activity items
  activities.forEach((activity) => {
    // Get user info
    const user = DB.getById(DB.KEYS.USERS, activity.userId)

    const activityItem = createElement("div", { className: "activity-item" }, [
      createElement("div", { className: "activity-icon" }, [
        createElement("i", { className: getActivityIcon(activity.action) }),
      ]),
      createElement("div", { className: "activity-content" }, [
        createElement("div", { className: "activity-text" }, [
          createElement("strong", {}, user ? user.name : "Unknown User"),
          " ",
          activity.details,
        ]),
        createElement("div", { className: "activity-time" }, timeAgo(activity.createdAt)),
      ]),
    ])

    activityList.appendChild(activityItem)
  })
}

/**
 * Get icon for activity type
 * @param {string} action - The activity action
 * @returns {string} - Font Awesome icon class
 */
function getActivityIcon(action) {
  switch (action) {
    case "created":
      return "fas fa-plus"
    case "updated":
      return "fas fa-edit"
    case "deleted":
      return "fas fa-trash"
    case "commented":
      return "fas fa-comment"
    default:
      return "fas fa-history"
  }
}

/**
 * Load satisfaction chart
 */
function loadSatisfactionChart() {
  const chartCanvas = document.getElementById("satisfactionChart")
  if (!chartCanvas) return

  // Get all requests with feedback
  const requests = DB.getAll(DB.KEYS.REQUESTS).filter((req) => req.feedback && req.feedback.rating)

  // Count ratings
  const ratingCounts = [0, 0, 0, 0, 0] // 1-5 stars

  requests.forEach((request) => {
    const rating = request.feedback.rating
    if (rating >= 1 && rating <= 5) {
      ratingCounts[rating - 1]++
    }
  })

  // Create chart
  new Chart(chartCanvas, {
    type: "bar",
    data: {
      labels: ["1 Star", "2 Stars", "3 Stars", "4 Stars", "5 Stars"],
      datasets: [
        {
          label: "Number of Ratings",
          data: ratingCounts,
          backgroundColor: ["#e74c3c", "#e67e22", "#f39c12", "#3498db", "#2ecc71"],
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0,
          },
        },
      },
      plugins: {
        legend: {
          display: false,
        },
      },
    },
  })
}

/**
 * Load notifications
 */
function loadNotifications() {
  const currentUser = Auth.getCurrentUser()
  if (!currentUser) return

  // Get unread notifications for the current user
  const notifications = DB.getAll(DB.KEYS.NOTIFICATIONS).filter(
    (notif) => notif.userId === currentUser.id && !notif.read,
  )

  // Update notification count
  const notificationCount = document.getElementById("notificationCount")
  if (notificationCount) {
    notificationCount.textContent = notifications.length
    notificationCount.style.display = notifications.length > 0 ? "flex" : "none"
  }

  // Load notification list
  const notificationList = document.getElementById("notificationList")
  if (notificationList) {
    // Clear the list
    clearElement(notificationList)

    if (notifications.length === 0) {
      // Show empty state
      notificationList.appendChild(
        createElement("div", { className: "empty-state" }, [
          createElement("i", { className: "fas fa-bell-slash" }),
          createElement("p", {}, "No notifications"),
        ]),
      )
      return
    }

    // Sort notifications by date (newest first)
    notifications.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))

    // Create notification items
    notifications.forEach((notification) => {
      const notificationItem = createElement(
        "div",
        {
          className: "notification-item" + (notification.read ? "" : " unread"),
          onclick: () => {
            markNotificationAsRead(notification.id)

            // Navigate to related item if applicable
            if (notification.relatedId) {
              switch (notification.type) {
                case "request":
                  window.location.href = `admin-requests.html?id=${notification.relatedId}`
                  break
                case "task":
                  window.location.href = `admin-tasks.html?id=${notification.relatedId}`
                  break
              }
            }
          },
        },
        [
          createElement("div", { className: "notification-title" }, notification.title),
          createElement("div", { className: "notification-message" }, notification.message),
          createElement("div", { className: "notification-time" }, timeAgo(notification.createdAt)),
        ],
      )

      notificationList.appendChild(notificationItem)
    })
  }
}

/**
 * Mark a notification as read
 * @param {string} notificationId - The ID of the notification
 */
function markNotificationAsRead(notificationId) {
  // Get all notifications
  const notifications = DB.getAll(DB.KEYS.NOTIFICATIONS)

  // Find and update the notification
  const notificationIndex = notifications.findIndex((n) => n.id === notificationId)
  if (notificationIndex !== -1) {
    notifications[notificationIndex].read = true

    // Save updated notifications
    localStorage.setItem(DB.KEYS.NOTIFICATIONS, JSON.stringify(notifications))

    // Reload notifications
    loadNotifications()
  }
}

/**
 * Mark all notifications as read
 */
function markAllNotificationsAsRead() {
  const currentUser = Auth.getCurrentUser()
  if (!currentUser) return

  // Get all notifications
  const notifications = DB.getAll(DB.KEYS.NOTIFICATIONS)

  // Update all unread notifications for the current user
  let updated = false
  notifications.forEach((notification) => {
    if (notification.userId === currentUser.id && !notification.read) {
      notification.read = true
      updated = true
    }
  })

  if (updated) {
    // Save updated notifications
    localStorage.setItem(DB.KEYS.NOTIFICATIONS, JSON.stringify(notifications))

    // Reload notifications
    loadNotifications()

    showNotification("All notifications marked as read", "success")
  }
}

/**
 * Clear all notifications
 */
function clearAllNotifications() {
  const currentUser = Auth.getCurrentUser()
  if (!currentUser) return

  // Get all notifications
  let notifications = DB.getAll(DB.KEYS.NOTIFICATIONS)

  // Remove all notifications for the current user
  const initialCount = notifications.length
  notifications = notifications.filter((notification) => notification.userId !== currentUser.id)

  if (notifications.length < initialCount) {
    // Save updated notifications
    localStorage.setItem(DB.KEYS.NOTIFICATIONS, JSON.stringify(notifications))

    // Reload notifications
    loadNotifications()

    showNotification("All notifications cleared", "success")
  }
}

/**
 * Set up event listeners
 */
function setupEventListeners() {
  // Export data button
  const exportDataBtn = document.getElementById("exportDataBtn")
  if (exportDataBtn) {
    exportDataBtn.addEventListener("click", () => {
      exportData()
    })
  }

  // Import data button
  const importDataBtn = document.getElementById("importDataBtn")
  if (importDataBtn) {
    importDataBtn.addEventListener("click", () => {
      showModal("importDataModal")
    })
  }

  // Confirm import button
  const confirmImportBtn = document.getElementById("confirmImportBtn")
  if (confirmImportBtn) {
    confirmImportBtn.addEventListener("click", () => {
      importData()
    })
  }

  // Notification icon
  const notificationIcon = document.querySelector(".notification-icon")
  if (notificationIcon) {
    notificationIcon.addEventListener("click", () => {
      const notificationPanel = document.getElementById("notificationPanel")
      if (notificationPanel) {
        notificationPanel.classList.toggle("show")
      }
    })
  }

  // Close notifications button
  const closeNotificationsBtn = document.getElementById("closeNotifications")
  if (closeNotificationsBtn) {
    closeNotificationsBtn.addEventListener("click", () => {
      const notificationPanel = document.getElementById("notificationPanel")
      if (notificationPanel) {
        notificationPanel.classList.remove("show")
      }
    })
  }

  // Mark all notifications as read button
  const markAllReadBtn = document.getElementById("markAllReadBtn")
  if (markAllReadBtn) {
    markAllReadBtn.addEventListener("click", () => {
      markAllNotificationsAsRead()
    })
  }

  // Clear all notifications button
  const clearAllBtn = document.getElementById("clearAllBtn")
  if (clearAllBtn) {
    clearAllBtn.addEventListener("click", () => {
      clearAllNotifications()
    })
  }

  // Widget refresh buttons
  const refreshButtons = document.querySelectorAll(".widget-refresh")
  refreshButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const widget = button.closest(".widget")
      if (widget) {
        const widgetHeader = widget.querySelector(".widget-header h3")
        if (widgetHeader) {
          const widgetTitle = widgetHeader.textContent

          switch (widgetTitle) {
            case "Recent Requests":
              loadRecentRequests()
              break
            case "Request Status":
              loadRequestStatusChart()
              break
            case "Recent Activity":
              loadRecentActivity()
              break
            case "Client Satisfaction":
              loadSatisfactionChart()
              break
          }

          showNotification("Widget refreshed", "info")
        }
      }
    })
  })

  // Import file input
  const importFile = document.getElementById("importFile")
  if (importFile) {
    importFile.addEventListener("change", () => {
      previewImportFile()
    })
  }
}

/**
 * Export data to CSV
 */
function exportData() {
  // Get all data
  const requests = DB.getAll(DB.KEYS.REQUESTS)
  const clients = DB.getAll(DB.KEYS.USERS).filter((user) => user.role === "client")

  // Create CSV content for requests
  let requestsCsv = "ID,Client ID,Title,Type,Priority,Status,Created At,Updated At\n"
  requests.forEach((request) => {
    requestsCsv += `"${request.id}","${request.clientId}","${request.title}","${request.type}","${request.priority}","${request.status}","${request.createdAt}","${request.updatedAt}"\n`
  })

  // Create CSV content for clients
  let clientsCsv = "ID,Name,Email,Company,Phone,Created At\n"
  clients.forEach((client) => {
    clientsCsv += `"${client.id}","${client.name}","${client.email}","${client.company || ""}","${client.phone || ""}","${client.createdAt}"\n`
  })

  // Create download links
  const requestsBlob = new Blob([requestsCsv], { type: "text/csv" })
  const requestsUrl = URL.createObjectURL(requestsBlob)
  const requestsLink = document.createElement("a")
  requestsLink.href = requestsUrl
  requestsLink.download = "service_requests.csv"

  const clientsBlob = new Blob([clientsCsv], { type: "text/csv" })
  const clientsUrl = URL.createObjectURL(clientsBlob)
  const clientsLink = document.createElement("a")
  clientsLink.href = clientsUrl
  clientsLink.download = "clients.csv"

  // Trigger downloads
  requestsLink.click()
  setTimeout(() => {
    clientsLink.click()

    // Clean up
    URL.revokeObjectURL(requestsUrl)
    URL.revokeObjectURL(clientsUrl)
  }, 100)

  showNotification("Data exported successfully", "success")
}

/**
 * Preview import file
 */
function previewImportFile() {
  const importFile = document.getElementById("importFile")
  const importPreview = document.getElementById("importPreview")

  if (!importFile || !importPreview) return

  const file = importFile.files[0]
  if (!file) {
    importPreview.innerHTML = "<p>No file selected</p>"
    return
  }

  const reader = new FileReader()
  reader.onload = (e) => {
    const contents = e.target.result
    const lines = contents.split("\n")

    // Show first 5 lines
    const previewLines = lines.slice(0, 5).join("\n")
    importPreview.innerHTML = `<pre>${previewLines}</pre>`

    if (lines.length > 5) {
      importPreview.innerHTML += `<p>...and ${lines.length - 5} more lines</p>`
    }
  }

  reader.readAsText(file)
}

/**
 * Import data from CSV
 */
function importData() {
  const importFile = document.getElementById("importFile")
  const importType = document.getElementById("importType")

  if (!importFile || !importType) return

  const file = importFile.files[0]
  if (!file) {
    showNotification("Please select a file to import", "error")
    return
  }

  const reader = new FileReader()
  reader.onload = (e) => {
    const contents = e.target.result
    const lines = contents.split("\n")

    // Parse header
    const header = lines[0].split(",")

    // Parse data
    const data = []
    for (let i = 1; i < lines.length; i++) {
      if (!lines[i].trim()) continue

      const values = parseCSVLine(lines[i])
      const item = {}

      header.forEach((key, index) => {
        item[key.trim()] = values[index] ? values[index].trim() : ""
      })

      data.push(item)
    }

    // Import data based on type
    if (importType.value === "clients") {
      importClients(data)
    } else if (importType.value === "requests") {
      importRequests(data)
    }

    hideModal("importDataModal")
  }

  reader.readAsText(file)
}

/**
 * Parse a CSV line, handling quoted values
 * @param {string} line - CSV line to parse
 * @returns {Array} - Array of values
 */
function parseCSVLine(line) {
  const values = []
  let inQuotes = false
  let currentValue = ""

  for (let i = 0; i < line.length; i++) {
    const char = line[i]

    if (char === '"') {
      inQuotes = !inQuotes
    } else if (char === "," && !inQuotes) {
      values.push(currentValue)
      currentValue = ""
    } else {
      currentValue += char
    }
  }

  values.push(currentValue)
  return values
}

/**
 * Import clients from CSV data
 * @param {Array} data - Client data from CSV
 */
function importClients(data) {
  // Get existing users
  const users = DB.getAll(DB.KEYS.USERS)

  // Process each client
  let importCount = 0
  data.forEach((item) => {
    // Check if client already exists
    const existingClient = users.find((user) => user.email === item.Email || (item.ID && user.id === item.ID))

    if (existingClient) {
      // Update existing client
      existingClient.name = item.Name
      existingClient.company = item.Company
      existingClient.phone = item.Phone
      existingClient.updatedAt = new Date().toISOString()
    } else {
      // Create new client
      const newClient = {
        id: item.ID || generateId("client"),
        email: item.Email,
        password: "client123", // Default password
        name: item.Name,
        role: "client",
        company: item.Company,
        phone: item.Phone,
        avatar: "../assets/images/avatar-placeholder.svg",
        createdAt: item["Created At"] || new Date().toISOString(),
      }

      users.push(newClient)
    }

    importCount++
  })

  // Save updated users
  localStorage.setItem(DB.KEYS.USERS, JSON.stringify(users))

  showNotification(`Imported ${importCount} clients successfully`, "success")
}

/**
 * Import requests from CSV data
 * @param {Array} data - Request data from CSV
 */
function importRequests(data) {
  // Get existing requests
  const requests = DB.getAll(DB.KEYS.REQUESTS)

  // Process each request
  let importCount = 0
  data.forEach((item) => {
    // Check if request already exists
    const existingRequest = requests.find((req) => item.ID && req.id === item.ID)

    if (existingRequest) {
      // Update existing request
      existingRequest.title = item.Title
      existingRequest.type = item.Type
      existingRequest.priority = item.Priority
      existingRequest.status = item.Status
      existingRequest.updatedAt = new Date().toISOString()
    } else {
      // Create new request
      const newRequest = {
        id: item.ID || generateId("req"),
        clientId: item["Client ID"],
        title: item.Title,
        type: item.Type,
        priority: item.Priority,
        status: item.Status,
        description: item.Description || "Imported request",
        createdAt: item["Created At"] || new Date().toISOString(),
        updatedAt: item["Updated At"] || new Date().toISOString(),
      }

      requests.push(newRequest)
    }

    importCount++
  })

  // Save updated requests
  localStorage.setItem(DB.KEYS.REQUESTS, JSON.stringify(requests))

  showNotification(`Imported ${importCount} requests successfully`, "success")
}
