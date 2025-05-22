/**
 * View Requests JavaScript
 * Handles service request viewing and management functionality
 */

// Mock Auth, DB, clearElement, formatDate for demonstration. In a real application, these would be properly imported or defined.
const Auth = {
  requireRole: (role) => {
    // Placeholder for role requirement logic
    console.log(`Required role: ${role}`)
  },
  getCurrentUser: () => {
    // Placeholder for getting current user
    return { id: "client123", role: "client", name: "John Doe", avatar: "../assets/images/avatar-placeholder.svg" }
  },
}

const DB = {
  KEYS: {
    REQUESTS: "requests",
    USERS: "users",
    COMMENTS: "comments",
    ACTIVITIES: "activities",
    NOTIFICATIONS: "notifications",
  },
  getAll: (key) => {
    // Placeholder for getting all data from a key
    if (key === "requests") {
      return [
        {
          id: "req1",
          clientId: "client123",
          title: "Broken Laptop Screen",
          type: "Repair",
          status: "pending",
          priority: "high",
          description: "My laptop screen is broken.",
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString(),
          attachments: [],
          feedback: null,
        },
        {
          id: "req2",
          clientId: "client123",
          title: "Software Installation",
          type: "Installation",
          status: "completed",
          priority: "medium",
          description: "Need to install Adobe Photoshop.",
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString(),
          attachments: [],
          feedback: { rating: 4, comment: "Good service." },
        },
        {
          id: "req3",
          clientId: "client123",
          title: "Network Troubleshooting",
          type: "Troubleshooting",
          status: "in-progress",
          priority: "low",
          description: "Cannot connect to the internet.",
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString(),
          attachments: [],
          feedback: null,
        },
      ]
    } else if (key === "users") {
      return [
        { id: "client123", role: "client", name: "John Doe", avatar: "../assets/images/avatar-placeholder.svg" },
        { id: "admin123", role: "admin", name: "Jane Smith", avatar: "../assets/images/avatar-placeholder.svg" },
      ]
    } else if (key === "comments") {
      return []
    } else if (key === "activities") {
      return []
    } else if (key === "notifications") {
      return []
    }
    return []
  },
  getById: function (key, id) {
    // Placeholder for getting data by ID
    if (key === "requests") {
      return this.getAll(key).find((req) => req.id === id)
    } else if (key === "users") {
      return this.getAll(key).find((user) => user.id === id)
    }
    return null
  },
  save: (key, data) => {
    // Placeholder for saving data
    console.log(`Saving to ${key}:`, data)
  },
}

function clearElement(element) {
  while (element.firstChild) {
    element.removeChild(element.firstChild)
  }
}

function formatDate(dateString, includeTime = false) {
  const date = new Date(dateString)
  const options = { year: "numeric", month: "long", day: "numeric" }
  if (includeTime) {
    options.hour = "numeric"
    options.minute = "numeric"
  }
  return date.toLocaleDateString(undefined, options)
}

function showNotification(message, type) {
  alert(`${type}: ${message}`)
}

function createElement(tag, attributes, children) {
  const element = document.createElement(tag)
  for (const key in attributes) {
    element.setAttribute(key, attributes[key])
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

function getFileIcon(filename) {
  const extension = filename.split(".").pop().toLowerCase()
  switch (extension) {
    case "pdf":
      return "fas fa-file-pdf"
    case "doc":
    case "docx":
      return "fas fa-file-word"
    case "xls":
    case "xlsx":
      return "fas fa-file-excel"
    case "png":
    case "jpg":
    case "jpeg":
      return "fas fa-file-image"
    default:
      return "fas fa-file"
  }
}

function showModal(modalId) {
  // Placeholder for showing modal
  console.log(`Showing modal: ${modalId}`)
}

function generateId(prefix) {
  return `${prefix}-${Math.random().toString(36).substring(2, 15)}`
}

document.addEventListener("DOMContentLoaded", () => {
  // Require client role
  Auth.requireRole("client")

  // Initialize page
  initPage()

  // Set up event listeners
  setupEventListeners()
})

// Global variables
let currentPage = 1
const itemsPerPage = 10
let filteredRequests = []
let selectedRequestId = null

/**
 * Initialize the page
 */
function initPage() {
  // Load requests
  loadRequests()

  // Check if a specific request ID is in the URL
  const urlParams = new URLSearchParams(window.location.search)
  const requestId = urlParams.get("id")

  if (requestId) {
    // Open the request details modal
    openRequestDetails(requestId)
  }
}

/**
 * Load service requests
 */
function loadRequests() {
  const currentUser = Auth.getCurrentUser()
  if (!currentUser) return

  // Get all requests for the current user
  const requests = DB.getAll(DB.KEYS.REQUESTS).filter((req) => req.clientId === currentUser.id)

  // Apply filters
  filteredRequests = applyFilters(requests)

  // Update the table
  updateRequestsTable()
}

/**
 * Apply filters to requests
 * @param {Array} requests - Array of requests
 * @returns {Array} - Filtered requests
 */
function applyFilters(requests) {
  // Get filter values
  const statusFilter = document.getElementById("statusFilter").value
  const typeFilter = document.getElementById("typeFilter").value
  const priorityFilter = document.getElementById("priorityFilter").value
  const dateFilter = document.getElementById("dateFilter").value
  const searchQuery = document.getElementById("searchRequests").value.toLowerCase()

  // Apply filters
  return requests.filter((request) => {
    // Status filter
    if (statusFilter !== "all" && request.status !== statusFilter) {
      return false
    }

    // Type filter
    if (typeFilter !== "all" && request.type !== typeFilter) {
      return false
    }

    // Priority filter
    if (priorityFilter !== "all" && request.priority !== priorityFilter) {
      return false
    }

    // Date filter
    if (dateFilter !== "all") {
      const requestDate = new Date(request.createdAt)
      const now = new Date()

      switch (dateFilter) {
        case "today":
          if (requestDate.toDateString() !== now.toDateString()) {
            return false
          }
          break
        case "week":
          const weekAgo = new Date(now)
          weekAgo.setDate(now.getDate() - 7)
          \
          if (requestDate &lt;
          weekAgo
          )
          {
            return false
          }
          break
        case "month":
          const monthAgo = new Date(now)
          monthAgo.setMonth(now.getMonth() - 1)
          if (requestDate &lt;
          monthAgo
          )
          {
            return false
          }
          break
        case "year":
          const yearAgo = new Date(now)
          yearAgo.setFullYear(now.getFullYear() - 1)
          if (requestDate &lt;
          yearAgo
          )
          {
            return false
          }
          break
      }
    }

    // Search query
    if (searchQuery) {
      const searchFields = [
        request.id,
        request.title,
        request.type,
        request.status,
        request.priority,
        request.description,
      ].map((field) => (field ? field.toString().toLowerCase() : ""))

      if (!searchFields.some((field) => field.includes(searchQuery))) {
        return false
      }
    }

    return true
  })
}

/**
 * Update the requests table
 */
function updateRequestsTable() {
  const tableBody = document.getElementById("requestsTableBody")
  const emptyMessage = document.getElementById("emptyRequestsMessage")
  const paginationInfo = document.getElementById("paginationInfo")
  const prevPageBtn = document.getElementById("prevPageBtn")
  const nextPageBtn = document.getElementById("nextPageBtn")

  if (!tableBody || !emptyMessage || !paginationInfo || !prevPageBtn || !nextPageBtn) return

  // Clear the table
  clearElement(tableBody)

  // Check if there are any requests
  if (filteredRequests.length === 0) {
    tableBody.style.display = "none"
    emptyMessage.style.display = "flex"
    paginationInfo.textContent = "Page 0 of 0"
    prevPageBtn.disabled = true
    nextPageBtn.disabled = true
    return
  }

  // Show the table, hide empty message
  tableBody.style.display = ""
  emptyMessage.style.display = "none"

  // Calculate pagination
  const totalPages = Math.ceil(filteredRequests.length / itemsPerPage)
  if (currentPage > totalPages) {
    currentPage = totalPages
  }

  const startIndex = (currentPage - 1) * itemsPerPage
  const endIndex = Math.min(startIndex + itemsPerPage, filteredRequests.length)

  // Update pagination info
  paginationInfo.textContent = `Page ${currentPage} of ${totalPages}`
  prevPageBtn.disabled = currentPage === 1
  nextPageBtn.disabled = currentPage === totalPages

  // Sort requests by date (newest first)
  const sortedRequests = [...filteredRequests].sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))

  // Get current page requests
  const pageRequests = sortedRequests.slice(startIndex, endIndex)

  // Create table rows
  pageRequests.forEach((request) => {
    const row = document.createElement("tr")

    row.innerHTML = `
            <td>${request.id}</td>
            <td>${request.title}</td>
            <td>${request.type}</td>
            <td><span class="status-badge ${request.status}">${request.status.replace("-", " ")}</span></td>
            <td><span class="priority-badge ${request.priority}">${request.priority}</span></td>
            <td>${formatDate(request.createdAt)}</td>
            <td>${formatDate(request.updatedAt)}</td>
            <td>
                <div class="table-actions">
                    <button class="btn-icon view-request" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${
                      request.status === "completed"
                        ? `
                        <button class="btn-icon generate-invoice" title="Generate Invoice">
                            <i class="fas fa-file-invoice"></i>
                        </button>
                    `
                        : ""
                    }
                </div>
            </td>
        `

    // Add event listeners
    const viewBtn = row.querySelector(".view-request")
    if (viewBtn) {
      viewBtn.addEventListener("click", () => {
        openRequestDetails(request.id)
      })
    }

    const invoiceBtn = row.querySelector(".generate-invoice")
    if (invoiceBtn) {
      invoiceBtn.addEventListener("click", () => {
        generateInvoice(request.id)
      })
    }

    tableBody.appendChild(row)
  })
}

/**
 * Open request details modal
 * @param {string} requestId - The ID of the request to view
 */
function openRequestDetails(requestId) {
  const request = DB.getById(DB.KEYS.REQUESTS, requestId)
  if (!request) {
    showNotification("Request not found", "error")
    return
  }

  selectedRequestId = requestId

  // Update modal content
  document.getElementById("modalRequestTitle").textContent = request.title
  document.getElementById("modalRequestId").textContent = request.id
  document.getElementById("modalRequestStatus").textContent = request.status.replace("-", " ")
  document.getElementById("modalRequestStatus").className = `request-status ${request.status}`
  document.getElementById("modalRequestPriority").textContent = request.priority
  document.getElementById("modalRequestPriority").className = `request-priority ${request.priority}`
  document.getElementById("modalRequestType").textContent = request.type
  document.getElementById("modalRequestCreated").textContent = formatDate(request.createdAt, true)
  document.getElementById("modalRequestUpdated").textContent = formatDate(request.updatedAt, true)
  document.getElementById("modalRequestDescription").textContent = request.description

  // Load attachments
  loadAttachments(request)

  // Load comments
  loadComments(requestId)

  // Show/hide feedback section based on status
  const feedbackSection = document.getElementById("requestFeedbackSection")
  const feedbackForm = document.getElementById("feedbackForm")
  const feedbackSubmitted = document.getElementById("feedbackSubmitted")

  if (feedbackSection && feedbackForm && feedbackSubmitted) {
    if (request.status === "completed") {
      feedbackSection.style.display = "block"

      // Check if feedback already exists
      if (request.feedback) {
        feedbackForm.style.display = "none"
        feedbackSubmitted.style.display = "block"

        // Display submitted rating
        const submittedRating = document.getElementById("submittedRating")
        if (submittedRating) {
          clearElement(submittedRating)

          for (let i = 1; i &lt;
          = 5
          i++
          )
          {
            const star = document.createElement("i")
            star.className = i & lt = request.feedback.rating ? "fas fa-star" : "far fa-star"
            submittedRating.appendChild(star)
          }
        }

        // Display submitted feedback text
        const submittedFeedbackText = document.getElementById("submittedFeedbackText")
        if (submittedFeedbackText) {
          submittedFeedbackText.textContent = request.feedback.comment || "No additional feedback provided."
        }
      } else {
        feedbackForm.style.display = "block"
        feedbackSubmitted.style.display = "none"

        // Reset star rating
        const stars = document.querySelectorAll("#starRating i")
        stars.forEach((star) => {
          star.className = "far fa-star"
        })

        // Reset feedback text
        document.getElementById("feedbackText").value = ""
      }
    } else {
      feedbackSection.style.display = "none"
    }
  }

  // Show the modal
  showModal("requestDetailsModal")
}

/**
 * Load attachments for a request
 * @param {Object} request - The request object
 */
function loadAttachments(request) {
  const attachmentList = document.getElementById("modalAttachmentList")
  const attachmentsSection = document.getElementById("modalRequestAttachments")

  if (!attachmentList || !attachmentsSection) return

  // Clear the list
  clearElement(attachmentList)

  // Check if there are any attachments
  if (!request.attachments || request.attachments.length === 0) {
    attachmentsSection.style.display = "none"
    return
  }

  // Show the attachments section
  attachmentsSection.style.display = "block"

  // Create attachment items
  request.attachments.forEach((attachment) => {
    const attachmentItem = createElement("div", { className: "attachment-item" }, [
      createElement("i", { className: getFileIcon(attachment.name) }),
      createElement("span", {}, attachment.name),
      createElement(
        "a",
        {
          href: "#",
          className: "download-attachment",
          title: "Download",
          onclick: (e) => {
            e.preventDefault()
            // In a real app, this would download the file
            showNotification("Download functionality is not implemented in this demo", "info")
          },
        },
        [createElement("i", { className: "fas fa-download" })],
      ),
    ])

    attachmentList.appendChild(attachmentItem)
  })
}

/**
 * Load comments for a request
 * @param {string} requestId - The ID of the request
 */
function loadComments(requestId) {
  const commentList = document.getElementById("modalCommentList")
  if (!commentList) return

  // Clear the list
  clearElement(commentList)

  // Get comments for the request
  const comments = DB.getAll(DB.KEYS.COMMENTS)
    .filter((comment) => comment.requestId === requestId)
    .sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt))

  if (comments.length === 0) {
    // Show empty state
    commentList.appendChild(
      createElement("div", { className: "empty-state" }, [
        createElement("i", { className: "fas fa-comments" }),
        createElement("p", {}, "No comments yet"),
      ]),
    )
    return
  }

  // Create comment items
  comments.forEach((comment) => {
    // Get user info
    const user = DB.getById(DB.KEYS.USERS, comment.userId)

    const commentItem = createElement("div", { className: "comment-item" }, [
      createElement("div", { className: "comment-avatar" }, [
        createElement("img", {
          src: user ? user.avatar : "../assets/images/avatar-placeholder.svg",
          alt: user ? `${user.name}'s Avatar` : "Avatar",
        }),
      ]),
      createElement("div", { className: "comment-content" }, [
        createElement("div", { className: "comment-header" }, [
          createElement("div", { className: "comment-author" }, user ? user.name : "Unknown User"),
          createElement("div", { className: "comment-time" }, formatDate(comment.createdAt, true)),
        ]),
        createElement("div", { className: "comment-text" }, comment.text),
      ]),
    ])

    commentList.appendChild(commentItem)
  })
}

/**
 * Add a comment to a request
 */
function addComment() {
  if (!selectedRequestId) return

  const currentUser = Auth.getCurrentUser()
  if (!currentUser) return

  const commentText = document.getElementById("newComment").value.trim()
  if (!commentText) {
    showNotification("Please enter a comment", "warning")
    return
  }

  // Create new comment
  const newComment = {
    id: generateId("comment"),
    requestId: selectedRequestId,
    userId: currentUser.id,
    text: commentText,
    createdAt: new Date().toISOString(),
  }

  // Save the comment
  DB.save(DB.KEYS.COMMENTS, newComment)

  // Update the request's updatedAt timestamp
  const request = DB.getById(DB.KEYS.REQUESTS, selectedRequestId)
  if (request) {
    request.updatedAt = new Date().toISOString()
    DB.save(DB.KEYS.REQUESTS, request)
  }

  // Create activity record
  const activity = {
    id: generateId("activity"),
    userId: currentUser.id,
    action: "commented",
    entityType: "request",
    entityId: selectedRequestId,
    details: `Commented on request "${request ? request.title : selectedRequestId}"`,
    createdAt: new Date().toISOString(),
  }

  DB.save(DB.KEYS.ACTIVITIES, activity)

  // Create notification for admins if the comment is from a client
  if (currentUser.role === "client") {
    const admins = DB.getAll(DB.KEYS.USERS).filter((user) => user.role === "admin")

    admins.forEach((admin) => {
      const notification = {
        id: generateId("notif"),
        userId: admin.id,
        title: "New Comment",
        message: `${currentUser.name} commented on request "${request ? request.title : selectedRequestId}"`,
        type: "comment",
        relatedId: selectedRequestId,
        read: false,
        createdAt: new Date().toISOString(),
      }

      DB.save(DB.KEYS.NOTIFICATIONS, notification)
    })
  }

  // Clear the comment input
  document.getElementById("newComment").value = ""

  // Reload comments
  loadComments(selectedRequestId)

  showNotification("Comment added successfully", "success")
}

/**
 * Submit feedback for a request
 */
function submitFeedback() {
  if (!selectedRequestId) return

  // Get selected rating
  const stars = document.querySelectorAll("#starRating i.fas")
  const rating = stars.length

  if (rating === 0) {
    showNotification("Please select a rating", "warning")
    return
  }

  // Get feedback text
  const feedbackText = document.getElementById("feedbackText").value.trim()

  // Get the request
  const request = DB.getById(DB.KEYS.REQUESTS, selectedRequestId)
  if (!request) return

  // Add feedback to the request
  request.feedback = {
    rating: rating,
    comment: feedbackText,
    submittedAt: new Date().toISOString(),
  }

  // Save the updated request
  DB.save(DB.KEYS.REQUESTS, request)

  // Create activity record
  const currentUser = Auth.getCurrentUser()
  if (currentUser) {
    const activity = {
      id: generateId("activity"),
      userId: currentUser.id,
      action: "submitted",
      entityType: "feedback",
      entityId: selectedRequestId,
      details: `Submitted feedback for request "${request.title}"`,
      createdAt: new Date().toISOString(),
    }

    DB.save(DB.KEYS.ACTIVITIES, activity)

    // Create notification for admins
    const admins = DB.getAll(DB.KEYS.USERS).filter((user) => user.role === "admin")

    admins.forEach((admin) => {
      const notification = {
        id: generateId("notif"),
        userId: admin.id,
        title: "New Feedback",
        message: `${currentUser.name} submitted feedback for request "${request.title}"`,
        type: "feedback",
        relatedId: selectedRequestId,
        read: false,
        createdAt: new Date().toISOString(),
      }

      DB.save(DB.KEYS.NOTIFICATIONS, notification)
    })
  }

  // Update the modal
  const feedbackForm = document.getElementById("feedbackForm")
  const feedbackSubmitted = document.getElementById("feedbackSubmitted")

  if (feedbackForm && feedbackSubmitted) {
    feedbackForm.style.display = "none"
    feedbackSubmitted.style.display = "block"

    // Display submitted rating
    const submittedRating = document.getElementById("submittedRating")
    if (submittedRating) {
      clearElement(submittedRating)

      for (let i = 1; i &lt;
      = 5
      i++
      )
      {
        const star = document.createElement("i")
        star.className = i & lt = rating ? "fas fa-star" : "far fa-star"
        submittedRating.appendChild(star)
      }
    }

    // Display submitted feedback text
    const submittedFeedbackText = document.getElementById("submittedFeedbackText")
    if (submittedFeedbackText) {
      submittedFeedbackText.textContent = feedbackText || "No additional feedback provided."
    }
  }

  showNotification("Feedback submitted successfully", "success")
}

/**
 * Generate an invoice for a request
 * @param {string} requestId - The ID of the request
 */
function generateInvoice(requestId) {
  const request = DB.getById(DB.KEYS.REQUESTS, requestId)
  if (!request) {
    showNotification("Request not found", "error")
    return
  }

  // In a real app, this would generate a PDF invoice
  // For this demo, we'll just show a notification
  showNotification("Invoice generation is not implemented in this demo", "info")
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

  // Empty state new request button
  const emptyStateNewRequestBtn = document.getElementById("emptyStateNewRequestBtn")
  if (emptyStateNewRequestBtn) {
    emptyStateNewRequestBtn.addEventListener("click", () => {
      window.location.href = "request-service.html"
    })
  }

  // Search input
  const searchInput = document.getElementById("searchRequests")
  if (searchInput) {
    searchInput.addEventListener("input", () => {
      currentPage = 1
      loadRequests()
    })
  }

  // Filter selects
  const filterSelects = document.querySelectorAll("#statusFilter, #typeFilter, #priorityFilter, #dateFilter")
  filterSelects.forEach((select) => {
    select.addEventListener("change", () => {
      currentPage = 1
      loadRequests()
    })
  })

  // Reset filters button
  const resetFiltersBtn = document.getElementById("resetFiltersBtn")
  if (resetFiltersBtn) {
    resetFiltersBtn.addEventListener("click", () => {
      document.getElementById("statusFilter").value = "all"
      document.getElementById("typeFilter").value = "all"
      document.getElementById("priorityFilter").value = "all"
      document.getElementById("dateFilter").value = "all"
      document.getElementById("searchRequests").value = ""

      currentPage = 1
      loadRequests()
    })
  }

  // Pagination buttons
  const prevPageBtn = document.getElementById("prevPageBtn")
  const nextPageBtn = document.getElementById("nextPageBtn")

  if (prevPageBtn) {
    prevPageBtn.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--
        updateRequestsTable()
      }
    })
  }

  if (nextPageBtn) {
    const totalPages = Math.ceil(filteredRequests.length / itemsPerPage)
    nextPageBtn.addEventListener("click", () => {
      if (currentPage &lt;
      totalPages
      )
      currentPage++
      updateRequestsTable()
    })
  }

  // Add comment button
  const addCommentBtn = document.getElementById("addCommentBtn")
  if (addCommentBtn) {
    addCommentBtn.addEventListener("click", () => {
      addComment()
    })
  }

  // Star rating
  const stars = document.querySelectorAll("#starRating i")
  stars.forEach((star) => {
    star.addEventListener("click", () => {
      const rating = Number.parseInt(star.dataset.rating)

      // Update star display
      stars.forEach((s) => {
        const starRating = Number.parseInt(s.dataset.rating)
        s.className = starRating & lt = rating ? "fas fa-star" : "far fa-star"
      })
    })

    star.addEventListener("mouseover", () => {
      const rating = Number.parseInt(star.dataset.rating)

      // Update star display on hover
      stars.forEach((s) => {
        const starRating = Number.parseInt(s.dataset.rating)
        s.className = starRating & lt = rating ? "fas fa-star" : "far fa-star"
      })
    })

    star.addEventListener("mouseout", () => {
      // Reset to selected rating
      const selectedStars = document.querySelectorAll("#starRating i.fas")
      const selectedRating = selectedStars.length

      stars.forEach((s) => {
        const starRating = Number.parseInt(s.dataset.rating)
        s.className = starRating & lt = selectedRating ? "fas fa-star" : "far fa-star"
      })
    })
  })

  // Submit feedback button
  const submitFeedbackBtn = document.getElementById("submitFeedbackBtn")
  if (submitFeedbackBtn) {
    submitFeedbackBtn.addEventListener("click", () => {
      submitFeedback()
    })
  }

  // Generate invoice button
  const generateInvoiceBtn = document.getElementById("generateInvoiceBtn")
  if (generateInvoiceBtn) {
    generateInvoiceBtn.addEventListener("click", () => {
      if (selectedRequestId) {
        generateInvoice(selectedRequestId)
      }
    })
  }
}
