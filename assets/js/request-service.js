/**
 * Request Service JavaScript
 * Handles service request creation functionality
 */

// Mock implementations (replace with actual imports or declarations)
const Auth = {
  requireRole: (role) => {
    console.log(`Auth.requireRole called for role: ${role}`)
  },
  getCurrentUser: () => {
    return { id: "user123", name: "Test User" }
  },
}

const initFileUpload = (fileInput, fileList) => {
  console.log("initFileUpload called")
}
const showNotification = (message, type) => {
  console.log(`Notification: ${message} (type: ${type})`)
}
const generateId = (prefix) => {
  return `${prefix}-${Math.random().toString(36).substring(2, 15)}`
}
const DB = {
  KEYS: { REQUESTS: "requests", ACTIVITIES: "activities", NOTIFICATIONS: "notifications", USERS: "users" },
  save: (key, data) => {
    console.log(`DB.save called with key: ${key}, data:`, data)
  },
  getAll: (key) => {
    if (key === DB.KEYS.USERS) {
      return [{ id: "admin1", role: "admin", name: "Admin User" }]
    }
    return []
  },
}

document.addEventListener("DOMContentLoaded", () => {
  // Require client role
  Auth.requireRole("client")

  // Initialize form
  initForm()

  // Set up event listeners
  setupEventListeners()
})

/**
 * Initialize the form
 */
function initForm() {
  // Initialize file upload
  const fileInput = document.getElementById("attachments")
  const fileList = document.getElementById("fileList")

  if (fileInput && fileList) {
    initFileUpload(fileInput, fileList)
  }
}

/**
 * Set up event listeners
 */
function setupEventListeners() {
  // Form submission
  const serviceRequestForm = document.getElementById("serviceRequestForm")
  if (serviceRequestForm) {
    serviceRequestForm.addEventListener("submit", (e) => {
      e.preventDefault()
      submitServiceRequest()
    })
  }

  // Cancel button
  const cancelRequestBtn = document.getElementById("cancelRequestBtn")
  if (cancelRequestBtn) {
    cancelRequestBtn.addEventListener("click", () => {
      // Confirm before canceling
      if (confirm("Are you sure you want to cancel this request? Any unsaved changes will be lost.")) {
        window.location.href = "client-dashboard.html"
      }
    })
  }
}

/**
 * Submit the service request
 */
function submitServiceRequest() {
  const currentUser = Auth.getCurrentUser()
  if (!currentUser) return

  // Get form values
  const title = document.getElementById("requestTitle").value
  const type = document.getElementById("requestType").value
  const priority = document.getElementById("priority").value
  const description = document.getElementById("requestDescription").value

  // Validate form
  if (!title || !type || !priority || !description) {
    showNotification("Please fill in all required fields", "error")
    return
  }

  // Create new request object
  const newRequest = {
    id: generateId("req"),
    clientId: currentUser.id,
    title: title,
    type: type,
    priority: priority,
    status: "pending",
    description: description,
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString(),
    assignedTo: null,
    attachments: getAttachments(),
  }

  // Save the request
  DB.save(DB.KEYS.REQUESTS, newRequest)

  // Create activity record
  const activity = {
    id: generateId("activity"),
    userId: currentUser.id,
    action: "created",
    entityType: "request",
    entityId: newRequest.id,
    details: `Created request "${newRequest.title}"`,
    createdAt: new Date().toISOString(),
  }

  DB.save(DB.KEYS.ACTIVITIES, activity)

  // Create notification for admins
  const admins = DB.getAll(DB.KEYS.USERS).filter((user) => user.role === "admin")

  admins.forEach((admin) => {
    const notification = {
      id: generateId("notif"),
      userId: admin.id,
      title: "New Service Request",
      message: `${currentUser.name} submitted a new service request: ${newRequest.title}`,
      type: "request",
      relatedId: newRequest.id,
      read: false,
      createdAt: new Date().toISOString(),
    }

    DB.save(DB.KEYS.NOTIFICATIONS, notification)
  })

  // Show success notification
  showNotification("Service request submitted successfully", "success")

  // Redirect to view requests page
  setTimeout(() => {
    window.location.href = "view-requests.html"
  }, 1500)
}

/**
 * Get attachments from the file input
 * @returns {Array} - Array of attachment objects
 */
function getAttachments() {
  const fileInput = document.getElementById("attachments")
  if (!fileInput || fileInput.files.length === 0) {
    return []
  }

  const attachments = []

  // In a real app, we would upload the files to a server
  // For this demo, we'll just store the file names
  Array.from(fileInput.files).forEach((file) => {
    attachments.push({
      id: generateId("attach"),
      name: file.name,
      size: file.size,
      type: file.type,
      uploadedAt: new Date().toISOString(),
    })
  })

  return attachments
}
