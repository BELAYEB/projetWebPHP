/**
 * Client Dashboard JavaScript
 * Handles client dashboard functionality
 */

// Mock Auth object for demonstration purposes
const Auth = {
  requireRole: (role) => {
    console.log(`Requiring role: ${role}`)
  },
  getCurrentUser: () => {
    return { id: "user123" } // Mock user
  },
}

// Mock DB object for demonstration purposes
const DB = {
  KEYS: {
    REQUESTS: "requests",
    TASKS: "tasks",
    DASHBOARD_WIDGETS: "dashboardWidgets",
  },
  getAll: (key) => {
    if (key === "requests") {
      return [
        {
          id: "1",
          clientId: "user123",
          title: "Request 1",
          status: "in-progress",
          updatedAt: new Date(),
          feedback: { rating: 4 },
        },
        {
          id: "2",
          clientId: "user123",
          title: "Request 2",
          status: "completed",
          updatedAt: new Date(),
          feedback: { rating: 5 },
        },
        {
          id: "3",
          clientId: "user123",
          title: "Request 3",
          status: "completed",
          updatedAt: new Date(),
          feedback: { rating: 3 },
        },
        { id: "4", clientId: "user123", title: "Request 4", status: "in-progress", updatedAt: new Date() },
        { id: "5", clientId: "user123", title: "Request 5", status: "pending", updatedAt: new Date() },
      ]
    } else if (key === "tasks") {
      return [
        { id: "1", relatedRequestId: "1", title: "Task 1", priority: "high", status: "pending", dueDate: new Date() },
        { id: "2", relatedRequestId: "1", title: "Task 2", priority: "urgent", status: "pending", dueDate: new Date() },
        {
          id: "3",
          relatedRequestId: "2",
          title: "Task 3",
          priority: "medium",
          status: "completed",
          dueDate: new Date(),
        },
        { id: "4", relatedRequestId: "3", title: "Task 4", priority: "high", status: "pending", dueDate: new Date() },
        { id: "5", relatedRequestId: "4", title: "Task 5", priority: "low", status: "pending", dueDate: new Date() },
      ]
    } else if (key === "dashboardWidgets") {
      return {
        user123: ["recentRequests", "highPriorityTasks", "upcomingTasks"],
      }
    }
    return []
  },
  getById: (key, id) => {
    if (key === "requests") {
      return DB.getAll(DB.KEYS.REQUESTS).find((req) => req.id === id)
    }
    return null
  },
}

// Mock functions for UI manipulation
function clearElement(element) {
  element.innerHTML = ""
}

function createElement(tag, attributes, children) {
  const element = document.createElement(tag)
  for (const key in attributes) {
    element.setAttribute(key, attributes[key])
  }
  if (children) {
    if (!Array.isArray(children)) {
      children = [children]
    }
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
  const badge = createElement("span", { className: `status-badge ${status}` }, status)
  return badge
}

function createPriorityBadge(priority) {
  const badge = createElement("span", { className: `priority-badge ${priority}` }, priority)
  return badge
}

function formatDate(date) {
  const d = new Date(date)
  const year = d.getFullYear()
  const month = "" + (d.getMonth() + 1)
  const day = "" + d.getDate()
  \
  if (month.length &lt;
  2
  ) month = '0' + month
  if (day.length &lt;
  2
  ) day = '0' + day

  return [year, month, day].join("-")
}

function showModal(modalId) {
  console.log(`Showing modal: ${modalId}`)
}

function hideModal(modalId) {
  console.log(`Hiding modal: ${modalId}`)
}

function showNotification(message, type) {
  console.log(`Notification: ${message} (${type})`)
}

document.addEventListener("DOMContentLoaded", () => {
  // Require client role
  Auth.requireRole("client")

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

  // Load dashboard widgets
  loadDashboardWidgets()
}

/**
 * Load dashboard statistics
 */
function loadDashboardStats() {
  const currentUser = Auth.getCurrentUser()
  if (!currentUser) return

  // Get all requests for the current user
  const requests = DB.getAll(DB.KEYS.REQUESTS).filter((req) => req.clientId === currentUser.id)

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
 * Load dashboard widgets
 */
function loadDashboardWidgets() {
  const currentUser = Auth.getCurrentUser()
  if (!currentUser) return

  // Get user's widget preferences
  const dashboardWidgets = DB.getAll(DB.KEYS.DASHBOARD_WIDGETS)
  const userWidgets = dashboardWidgets[currentUser.id] || ["recentRequests", "highPriorityTasks", "upcomingTasks"]

  // Load each widget
  userWidgets.forEach((widgetId) => {
    switch (widgetId) {
      case "recentRequests":
        loadRecentRequestsWidget()
        break
      case "highPriorityTasks":
        loadHighPriorityTasksWidget()
        break
      case "upcomingTasks":
        loadUpcomingTasksWidget()
        break
      case "requestStatus":
        loadRequestStatusWidget()
        break
    }
  })
}

/**
 * Load recent requests widget
 */
function loadRecentRequestsWidget() {
  const currentUser = Auth.getCurrentUser()
  if (!currentUser) return

  const recentRequestsList = document.getElementById("recentRequestsList")
  if (!recentRequestsList) return

  // Clear the list
  clearElement(recentRequestsList)

  // Get recent requests for the current user
  const requests = DB.getAll(DB.KEYS.REQUESTS)
    .filter((req) => req.clientId === currentUser.id)
    .sort((a, b) => new Date(b.updatedAt) - new Date(a.updatedAt))
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
    const requestItem = createElement(
      "div",
      {
        className: "request-item",
        onclick: () => {
          window.location.href = `view-requests.html?id=${request.id}`
        },
      },
      [
        createElement("div", { className: "request-item-header" }, [
          createElement("div", { className: "request-item-title" }, request.title),
          createStatusBadge(request.status),
        ]),
        createElement("div", { className: "request-item-meta" }, [
          createElement("span", {}, `#${request.id}`),
          createElement("span", {}, formatDate(request.updatedAt)),
        ]),
      ],
    )

    recentRequestsList.appendChild(requestItem)
  })
}

/**
 * Load high priority tasks widget
 */
function loadHighPriorityTasksWidget() {
  const currentUser = Auth.getCurrentUser()
  if (!currentUser) return

  const highPriorityTasksList = document.getElementById("highPriorityTasksList")
  if (!highPriorityTasksList) return

  // Clear the list
  clearElement(highPriorityTasksList)

  // Get all requests for the current user
  const userRequests = DB.getAll(DB.KEYS.REQUESTS)
    .filter((req) => req.clientId === currentUser.id)
    .map((req) => req.id)

  // Get high priority tasks related to user's requests
  const tasks = DB.getAll(DB.KEYS.TASKS)
    .filter(
      (task) =>
        userRequests.includes(task.relatedRequestId) &&
        (task.priority === "high" || task.priority === "urgent") &&
        task.status !== "completed",
    )
    .sort((a, b) => {
      // Sort by priority (urgent first) then by due date
      if (a.priority === "urgent" && b.priority !== "urgent") return -1
      if (a.priority !== "urgent" && b.priority === "urgent") return 1

      if (a.dueDate && b.dueDate) {
        return new Date(a.dueDate) - new Date(b.dueDate)
      }
      if (a.dueDate) return -1
      if (b.dueDate) return 1

      return 0
    })
    .slice(0, 5)

  if (tasks.length === 0) {
    // Show empty state
    highPriorityTasksList.appendChild(
      createElement("div", { className: "empty-state" }, [
        createElement("i", { className: "fas fa-tasks" }),
        createElement("p", {}, "No high priority tasks"),
      ]),
    )
    return
  }

  // Create task items
  tasks.forEach((task) => {
    // Get related request
    const request = DB.getById(DB.KEYS.REQUESTS, task.relatedRequestId)

    const taskItem = createElement("div", { className: "task-item" }, [
      createElement("div", { className: "task-item-header" }, [
        createElement("div", { className: "task-item-title" }, task.title),
        createPriorityBadge(task.priority),
      ]),
      createElement("div", { className: "task-item-meta" }, [
        createElement("span", {}, request ? `Request: ${request.title}` : ""),
        createElement("span", {}, task.dueDate ? `Due: ${formatDate(task.dueDate)}` : "No due date"),
      ]),
    ])

    highPriorityTasksList.appendChild(taskItem)
  })
}

/**
 * Load upcoming tasks widget
 */
function loadUpcomingTasksWidget() {
  const currentUser = Auth.getCurrentUser()
  if (!currentUser) return

  const upcomingTasksList = document.getElementById("upcomingTasksList")
  if (!upcomingTasksList) return

  // Clear the list
  clearElement(upcomingTasksList)

  // Get all requests for the current user
  const userRequests = DB.getAll(DB.KEYS.REQUESTS)
    .filter((req) => req.clientId === currentUser.id)
    .map((req) => req.id)

  // Get upcoming tasks related to user's requests
  const now = new Date()
  const tasks = DB.getAll(DB.KEYS.TASKS)
    .filter((task) => userRequests.includes(task.relatedRequestId) && task.status !== "completed" && task.dueDate)
    .filter((task) => {
      const dueDate = new Date(task.dueDate)
      const diffDays = Math.ceil((dueDate - now) / (1000 * 60 * 60 * 24))
      return (diffDays >= 0 && diffDays & lt = 7) // Due within the next 7 days
    })
    .sort((a, b) => new Date(a.dueDate) - new Date(b.dueDate))
    .slice(0, 5)

  if (tasks.length === 0) {
    // Show empty state
    upcomingTasksList.appendChild(
      createElement("div", { className: "empty-state" }, [
        createElement("i", { className: "fas fa-calendar" }),
        createElement("p", {}, "No upcoming tasks"),
      ]),
    )
    return
  }

  // Create task items
  tasks.forEach((task) => {
    // Get related request
    const request = DB.getById(DB.KEYS.REQUESTS, task.relatedRequestId)

    const taskItem = createElement("div", { className: "task-item" }, [
      createElement("div", { className: "task-item-header" }, [
        createElement("div", { className: "task-item-title" }, task.title),
        createStatusBadge(task.status),
      ]),
      createElement("div", { className: "task-item-meta" }, [
        createElement("span", {}, request ? `Request: ${request.title}` : ""),
        createElement("span", {}, `Due: ${formatDate(task.dueDate)}`),
      ]),
    ])

    upcomingTasksList.appendChild(taskItem)
  })
}

/**
 * Load request status widget
 */
function loadRequestStatusWidget() {
  // Placeholder for request status widget loading logic
  console.log("Loading request status widget")
}

/**
 * Set up event listeners
 */
function setupEventListeners() {
  // New request button
  const newRequestBtn = document.getElementById("newRequestBtn")
  if (newRequestBtn) {
    newRequestBtn.addEventListener("click", () => {
      window.location.href = "request-service.html"
    })
  }

  // Customize dashboard button
  const customizeDashboardBtn = document.getElementById("customizeDashboardBtn")
  if (customizeDashboardBtn) {
    customizeDashboardBtn.addEventListener("click", () => {
      showModal("addWidgetModal")
    })
  }

  // Add widget button
  const addWidgetBtn = document.getElementById("addWidgetBtn")
  if (addWidgetBtn) {
    addWidgetBtn.addEventListener("click", () => {
      addSelectedWidget()
    })
  }

  // Widget option selection
  const widgetOptions = document.querySelectorAll(".widget-option")
  widgetOptions.forEach((option) => {
    option.addEventListener("click", () => {
      // Toggle selection
      option.classList.toggle("selected")
    })
  })

  // Widget refresh buttons
  const refreshButtons = document.querySelectorAll(".widget-refresh")
  refreshButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      e.stopPropagation()
      const widget = button.closest(".widget")
      if (widget) {
        const widgetId = widget.dataset.widgetId
        refreshWidget(widgetId)
      }
    })
  })

  // Widget remove buttons
  const removeButtons = document.querySelectorAll(".widget-remove")
  removeButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      e.stopPropagation()
      const widget = button.closest(".widget")
      if (widget) {
        const widgetId = widget.dataset.widgetId
        removeWidget(widgetId)
      }
    })
  })
}

/**
 * Add selected widget to dashboard
 */
function addSelectedWidget() {
  const selectedOptions = document.querySelectorAll(".widget-option.selected")
  if (selectedOptions.length === 0) {
    showNotification("Please select at least one widget to add", "warning")
    return
  }

  const currentUser = Auth.getCurrentUser()
  if (!currentUser) return

  // Get current widgets
  const dashboardWidgets = DB.getAll(DB.KEYS.DASHBOARD_WIDGETS)
  const userWidgets = dashboardWidgets[currentUser.id] || []

  // Add selected widgets
  let widgetsAdded = false
  selectedOptions.forEach((option) => {
    const widgetId = option.dataset.widgetId
    if (widgetId && !userWidgets.includes(widgetId)) {
      userWidgets.push(widgetId)
      widgetsAdded = true

      // Load the widget
      switch (widgetId) {
        case "recentRequests":
          loadRecentRequestsWidget()
          break
        case "highPriorityTasks":
          loadHighPriorityTasksWidget()
          break
        case "upcomingTasks":
          loadUpcomingTasksWidget()
          break
        case "requestStatus":
          loadRequestStatusWidget()
          break
      }
    }
  })

  if (widgetsAdded) {
    // Save updated widgets
    dashboardWidgets[currentUser.id] = userWidgets
    localStorage.setItem(DB.KEYS.DASHBOARD_WIDGETS, JSON.stringify(dashboardWidgets))

    showNotification("Widgets added successfully", "success")
    hideModal("addWidgetModal")
  } else {
    showNotification("Selected widgets are already on your dashboard", "info")
  }
}

/**
 * Refresh a specific widget
 * @param {string} widgetId - The ID of the widget to refresh
 */
function refreshWidget(widgetId) {
  switch (widgetId) {
    case "recentRequests":
      loadRecentRequestsWidget()
      break
    case "highPriorityTasks":
      loadHighPriorityTasksWidget()
      break
    case "upcomingTasks":
      loadUpcomingTasksWidget()
      break
    case "requestStatus":
      loadRequestStatusWidget()
      break
  }

  showNotification("Widget refreshed", "info")
}

/**
 * Remove a widget from the dashboard
 * @param {string} widgetId - The ID of the widget to remove
 */
function removeWidget(widgetId) {
  const currentUser = Auth.getCurrentUser()
  if (!currentUser) return

  // Get current widgets
  const dashboardWidgets = DB.getAll(DB.KEYS.DASHBOARD_WIDGETS)
  let userWidgets = dashboardWidgets[currentUser.id] || []

  // Remove the widget
  userWidgets = userWidgets.filter((id) => id !== widgetId)

  // Save updated widgets
  dashboardWidgets[currentUser.id] = userWidgets
  localStorage.setItem(DB.KEYS.DASHBOARD_WIDGETS, JSON.stringify(dashboardWidgets))

  // Remove the widget from the DOM
  const widget = document.querySelector(`.widget[data-widget-id="${widgetId}"]`)
  if (widget) {
    widget.remove()
  }

  showNotification("Widget removed", "info")
}
