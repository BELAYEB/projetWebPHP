/**
 * DOM Utility Functions
 * Helper functions for DOM manipulation and UI updates
 */

/**
 * Create an element with attributes and children
 * @param {string} tag - The HTML tag name
 * @param {Object} attributes - Attributes to set on the element
 * @param {Array|string|Node} children - Child elements or text content
 * @returns {HTMLElement} - The created element
 */
function createElement(tag, attributes = {}, children = []) {
  const element = document.createElement(tag)

  // Set attributes
  for (const [key, value] of Object.entries(attributes)) {
    if (key === "className") {
      element.className = value
    } else if (key === "style" && typeof value === "object") {
      Object.assign(element.style, value)
    } else if (key.startsWith("on") && typeof value === "function") {
      const eventName = key.substring(2).toLowerCase()
      element.addEventListener(eventName, value)
    } else {
      element.setAttribute(key, value)
    }
  }

  // Add children
  if (Array.isArray(children)) {
    children.forEach((child) => {
      if (child) {
        appendChild(element, child)
      }
    })
  } else if (children) {
    appendChild(element, children)
  }

  return element
}

/**
 * Append a child to an element
 * @param {HTMLElement} parent - The parent element
 * @param {HTMLElement|string} child - The child element or text content
 */
function appendChild(parent, child) {
  if (typeof child === "string") {
    parent.appendChild(document.createTextNode(child))
  } else if (child instanceof Node) {
    parent.appendChild(child)
  }
}

/**
 * Format a date string to a readable format
 * @param {string} dateString - ISO date string
 * @param {boolean} includeTime - Whether to include time
 * @returns {string} - Formatted date string
 */
function formatDate(dateString, includeTime = false) {
  if (!dateString) return "N/A"

  const date = new Date(dateString)
  const options = {
    year: "numeric",
    month: "short",
    day: "numeric",
  }

  if (includeTime) {
    options.hour = "2-digit"
    options.minute = "2-digit"
  }

  return date.toLocaleDateString("en-US", options)
}

/**
 * Calculate time elapsed since a date
 * @param {string} dateString - ISO date string
 * @returns {string} - Elapsed time in a readable format
 */
function timeAgo(dateString) {
  if (!dateString) return ""

  const date = new Date(dateString)
  const now = new Date()
  const seconds = Math.floor((now - date) / 1000)

  let interval = Math.floor(seconds / 31536000)
  if (interval >= 1) {
    return interval === 1 ? "1 year ago" : `${interval} years ago`
  }

  interval = Math.floor(seconds / 2592000)
  if (interval >= 1) {
    return interval === 1 ? "1 month ago" : `${interval} months ago`
  }

  interval = Math.floor(seconds / 86400)
  if (interval >= 1) {
    return interval === 1 ? "1 day ago" : `${interval} days ago`
  }

  interval = Math.floor(seconds / 3600)
  if (interval >= 1) {
    return interval === 1 ? "1 hour ago" : `${interval} hours ago`
  }

  interval = Math.floor(seconds / 60)
  if (interval >= 1) {
    return interval === 1 ? "1 minute ago" : `${interval} minutes ago`
  }

  return "Just now"
}

/**
 * Generate a unique ID
 * @param {string} prefix - Prefix for the ID
 * @returns {string} - Unique ID
 */
function generateId(prefix = "") {
  return `${prefix}${Date.now().toString(36)}${Math.random().toString(36).substr(2, 5)}`
}

/**
 * Clear all children of an element
 * @param {HTMLElement} element - The element to clear
 */
function clearElement(element) {
  while (element.firstChild) {
    element.removeChild(element.firstChild)
  }
}

/**
 * Show a modal
 * @param {string} modalId - The ID of the modal to show
 */
function showModal(modalId) {
  const modal = document.getElementById(modalId)
  if (modal) {
    modal.classList.add("show")

    // Add event listener to close button
    const closeButtons = modal.querySelectorAll(".close-modal")
    closeButtons.forEach((button) => {
      button.addEventListener("click", () => {
        hideModal(modalId)
      })
    })

    // Close modal when clicking outside
    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        hideModal(modalId)
      }
    })
  }
}

/**
 * Hide a modal
 * @param {string} modalId - The ID of the modal to hide
 */
function hideModal(modalId) {
  const modal = document.getElementById(modalId)
  if (modal) {
    modal.classList.remove("show")
  }
}

/**
 * Create a status badge element
 * @param {string} status - The status value
 * @returns {HTMLElement} - The status badge element
 */
function createStatusBadge(status) {
  return createElement(
    "span",
    {
      className: `status-badge ${status}`,
    },
    status.replace("-", " "),
  )
}

/**
 * Create a priority badge element
 * @param {string} priority - The priority value
 * @returns {HTMLElement} - The priority badge element
 */
function createPriorityBadge(priority) {
  return createElement(
    "span",
    {
      className: `priority-badge ${priority}`,
    },
    priority,
  )
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

/**
 * Make an element draggable
 * @param {HTMLElement} element - The element to make draggable
 * @param {Function} onDragEnd - Callback function when drag ends
 */
function makeDraggable(element, onDragEnd) {
  element.setAttribute("draggable", "true")

  element.addEventListener("dragstart", (e) => {
    e.dataTransfer.setData("text/plain", element.id)
    element.classList.add("dragging")
  })

  element.addEventListener("dragend", () => {
    element.classList.remove("dragging")
  })

  // Return a function to remove event listeners
  return () => {
    element.removeAttribute("draggable")
    element.removeEventListener("dragstart", () => {})
    element.removeEventListener("dragend", () => {})
  }
}

/**
 * Make an element a drop target
 * @param {HTMLElement} element - The element to make a drop target
 * @param {Function} onDrop - Callback function when an item is dropped
 */
function makeDropTarget(element, onDrop) {
  element.addEventListener("dragover", (e) => {
    e.preventDefault()
    element.classList.add("drag-over")
  })

  element.addEventListener("dragleave", () => {
    element.classList.remove("drag-over")
  })

  element.addEventListener("drop", (e) => {
    e.preventDefault()
    element.classList.remove("drag-over")

    const id = e.dataTransfer.getData("text/plain")
    if (id && onDrop) {
      onDrop(id, element)
    }
  })

  // Return a function to remove event listeners
  return () => {
    element.removeEventListener("dragover", () => {})
    element.removeEventListener("dragleave", () => {})
    element.removeEventListener("drop", () => {})
  }
}

/**
 * Initialize a file upload input
 * @param {HTMLElement} fileInput - The file input element
 * @param {HTMLElement} fileList - The element to display selected files
 * @param {Function} onFileSelect - Callback function when files are selected
 */
function initFileUpload(fileInput, fileList, onFileSelect) {
  if (!fileInput || !fileList) return

  // Handle file selection
  fileInput.addEventListener("change", () => {
    updateFileList(fileInput, fileList)
    if (onFileSelect) {
      onFileSelect(fileInput.files)
    }
  })

  // Handle drag and drop
  const fileUpload = fileInput.parentElement
  if (fileUpload && fileUpload.classList.contains("file-upload")) {
    fileUpload.addEventListener("dragover", (e) => {
      e.preventDefault()
      fileUpload.classList.add("drag-over")
    })

    fileUpload.addEventListener("dragleave", () => {
      fileUpload.classList.remove("drag-over")
    })

    fileUpload.addEventListener("drop", (e) => {
      e.preventDefault()
      fileUpload.classList.remove("drag-over")

      if (e.dataTransfer.files.length > 0) {
        fileInput.files = e.dataTransfer.files
        updateFileList(fileInput, fileList)
        if (onFileSelect) {
          onFileSelect(fileInput.files)
        }
      }
    })
  }
}

/**
 * Update the file list display
 * @param {HTMLElement} fileInput - The file input element
 * @param {HTMLElement} fileList - The element to display selected files
 */
function updateFileList(fileInput, fileList) {
  clearElement(fileList)

  if (fileInput.files.length === 0) {
    return
  }

  Array.from(fileInput.files).forEach((file) => {
    const fileItem = createElement("div", { className: "file-item" }, [
      createElement("i", { className: getFileIcon(file.name) }),
      createElement("span", {}, file.name),
      createElement("i", {
        className: "fas fa-times remove-file",
        onclick: () => {
          // Remove file from list (this is tricky with the File API)
          // In a real app, we would use a custom file list
          fileItem.remove()
        },
      }),
    ])

    fileList.appendChild(fileItem)
  })
}

/**
 * Get an appropriate icon class for a file type
 * @param {string} fileName - The file name
 * @returns {string} - Font Awesome icon class
 */
function getFileIcon(fileName) {
  const extension = fileName.split(".").pop().toLowerCase()

  switch (extension) {
    case "pdf":
      return "fas fa-file-pdf"
    case "doc":
    case "docx":
      return "fas fa-file-word"
    case "xls":
    case "xlsx":
      return "fas fa-file-excel"
    case "ppt":
    case "pptx":
      return "fas fa-file-powerpoint"
    case "jpg":
    case "jpeg":
    case "png":
    case "gif":
    case "svg":
      return "fas fa-file-image"
    case "zip":
    case "rar":
    case "7z":
      return "fas fa-file-archive"
    case "txt":
      return "fas fa-file-alt"
    default:
      return "fas fa-file"
  }
}
