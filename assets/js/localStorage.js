/**
 * LocalStorage Utility Functions
 * Handles CRUD operations for storing data in localStorage
 */

// Namespace for our localStorage operations
const DB = {
  // Keys for different data types
  KEYS: {
    USERS: "contactflow_users",
    CLIENTS: "contactflow_clients",
    REQUESTS: "contactflow_requests",
    TASKS: "contactflow_tasks",
    COMMENTS: "contactflow_comments",
    NOTIFICATIONS: "contactflow_notifications",
    ACTIVITIES: "contactflow_activities",
    DASHBOARD_WIDGETS: "contactflow_dashboard_widgets",
    SETTINGS: "contactflow_settings",
    CURRENT_USER: "contactflow_current_user",
    DARK_MODE: "darkMode",
  },

  /**
   * Initialize the database with sample data if it doesn't exist
   */
  init() {
    // Check if data already exists
    if (!localStorage.getItem(this.KEYS.USERS)) {
      this.initSampleData()
    }
  },

  /**
   * Create sample data for demonstration purposes
   */
  initSampleData() {
    // Sample users (admin and clients)
    const users = [
      {
        id: "admin1",
        email: "admin@contactflow.com",
        password: "admin123", // In a real app, this would be hashed
        name: "Admin User",
        role: "admin",
        avatar: "../assets/images/admin-avatar.svg",
        createdAt: new Date().toISOString(),
      },
      {
        id: "client1",
        email: "john@example.com",
        password: "client123", // In a real app, this would be hashed
        name: "John Doe",
        role: "client",
        company: "Acme Inc",
        phone: "555-123-4567",
        avatar: "../assets/images/avatar-placeholder.svg",
        createdAt: new Date().toISOString(),
      },
      {
        id: "client2",
        email: "sarah@example.com",
        password: "client123", // In a real app, this would be hashed
        name: "Sarah Johnson",
        role: "client",
        company: "XYZ Corp",
        phone: "555-987-6543",
        avatar: "../assets/images/avatar-placeholder.svg",
        createdAt: new Date().toISOString(),
      },
    ]

    // Sample service requests
    const now = new Date()
    const requests = [
      {
        id: "req1",
        clientId: "client1",
        title: "Website Performance Issue",
        type: "support",
        priority: "high",
        status: "in-progress",
        description: "Our website is loading slowly during peak hours. Need assistance optimizing performance.",
        createdAt: new Date(now - 7 * 24 * 60 * 60 * 1000).toISOString(), // 7 days ago
        updatedAt: new Date(now - 2 * 24 * 60 * 60 * 1000).toISOString(), // 2 days ago
        assignedTo: "admin1",
        attachments: [],
      },
      {
        id: "req2",
        clientId: "client1",
        title: "New Feature Request",
        type: "feature",
        priority: "medium",
        status: "pending",
        description: "We would like to request a new reporting dashboard that shows monthly trends.",
        createdAt: new Date(now - 3 * 24 * 60 * 60 * 1000).toISOString(), // 3 days ago
        updatedAt: new Date(now - 3 * 24 * 60 * 60 * 1000).toISOString(), // 3 days ago
        assignedTo: null,
        attachments: [],
      },
      {
        id: "req3",
        clientId: "client2",
        title: "Billing Inquiry",
        type: "billing",
        priority: "low",
        status: "completed",
        description: "I have a question about my last invoice. There seems to be a discrepancy in the charges.",
        createdAt: new Date(now - 14 * 24 * 60 * 60 * 1000).toISOString(), // 14 days ago
        updatedAt: new Date(now - 10 * 24 * 60 * 60 * 1000).toISOString(), // 10 days ago
        assignedTo: "admin1",
        attachments: [],
        feedback: {
          rating: 4,
          comment: "Issue was resolved quickly, but would have appreciated more detailed explanation.",
        },
      },
      {
        id: "req4",
        clientId: "client2",
        title: "Account Access Problem",
        type: "support",
        priority: "urgent",
        status: "completed",
        description: "Unable to access my account dashboard. Getting a 403 error when trying to log in.",
        createdAt: new Date(now - 5 * 24 * 60 * 60 * 1000).toISOString(), // 5 days ago
        updatedAt: new Date(now - 4 * 24 * 60 * 60 * 1000).toISOString(), // 4 days ago
        assignedTo: "admin1",
        attachments: [],
        feedback: {
          rating: 5,
          comment: "Excellent support! Problem was fixed within an hour.",
        },
      },
    ]

    // Sample tasks
    const tasks = [
      {
        id: "task1",
        title: "Investigate Website Performance",
        description: "Check server logs and run performance tests to identify bottlenecks.",
        status: "in-progress",
        priority: "high",
        assignedTo: "admin1",
        relatedRequestId: "req1",
        dueDate: new Date(now.getTime() + 2 * 24 * 60 * 60 * 1000).toISOString(), // 2 days from now
        createdAt: new Date(now - 7 * 24 * 60 * 60 * 1000).toISOString(), // 7 days ago
        updatedAt: new Date(now - 2 * 24 * 60 * 60 * 1000).toISOString(), // 2 days ago
      },
      {
        id: "task2",
        title: "Optimize Database Queries",
        description: "Review and optimize slow database queries affecting website performance.",
        status: "todo",
        priority: "high",
        assignedTo: "admin1",
        relatedRequestId: "req1",
        dueDate: new Date(now.getTime() + 4 * 24 * 60 * 60 * 1000).toISOString(), // 4 days from now
        createdAt: new Date(now - 6 * 24 * 60 * 60 * 1000).toISOString(), // 6 days ago
        updatedAt: new Date(now - 6 * 24 * 60 * 60 * 1000).toISOString(), // 6 days ago
      },
      {
        id: "task3",
        title: "Design New Reporting Dashboard",
        description: "Create wireframes for the new reporting dashboard requested by client.",
        status: "todo",
        priority: "medium",
        assignedTo: null,
        relatedRequestId: "req2",
        dueDate: null,
        createdAt: new Date(now - 3 * 24 * 60 * 60 * 1000).toISOString(), // 3 days ago
        updatedAt: new Date(now - 3 * 24 * 60 * 60 * 1000).toISOString(), // 3 days ago
      },
      {
        id: "task4",
        title: "Fix Account Access Issue",
        description: "Investigate and resolve the 403 error when client tries to log in.",
        status: "completed",
        priority: "urgent",
        assignedTo: "admin1",
        relatedRequestId: "req4",
        dueDate: new Date(now - 4 * 24 * 60 * 60 * 1000).toISOString(), // 4 days ago
        createdAt: new Date(now - 5 * 24 * 60 * 60 * 1000).toISOString(), // 5 days ago
        updatedAt: new Date(now - 4 * 24 * 60 * 60 * 1000).toISOString(), // 4 days ago
      },
    ]

    // Sample comments
    const comments = [
      {
        id: "comment1",
        requestId: "req1",
        userId: "admin1",
        text: "I've started investigating the performance issues. Will update you soon.",
        createdAt: new Date(now - 6 * 24 * 60 * 60 * 1000).toISOString(), // 6 days ago
      },
      {
        id: "comment2",
        requestId: "req1",
        userId: "client1",
        text: "Thank you. Looking forward to your findings.",
        createdAt: new Date(now - 5 * 24 * 60 * 60 * 1000).toISOString(), // 5 days ago
      },
      {
        id: "comment3",
        requestId: "req1",
        userId: "admin1",
        text: "Found some slow database queries. Working on optimizing them now.",
        createdAt: new Date(now - 3 * 24 * 60 * 60 * 1000).toISOString(), // 3 days ago
      },
      {
        id: "comment4",
        requestId: "req4",
        userId: "admin1",
        text: "I've reset your account permissions. Please try logging in again.",
        createdAt: new Date(now - 4.5 * 24 * 60 * 60 * 1000).toISOString(), // 4.5 days ago
      },
      {
        id: "comment5",
        requestId: "req4",
        userId: "client2",
        text: "It works now! Thank you for the quick response.",
        createdAt: new Date(now - 4 * 24 * 60 * 60 * 1000).toISOString(), // 4 days ago
      },
    ]

    // Sample notifications
    const notifications = [
      {
        id: "notif1",
        userId: "admin1",
        title: "New Service Request",
        message: "John Doe submitted a new service request: Website Performance Issue",
        type: "request",
        relatedId: "req1",
        read: true,
        createdAt: new Date(now - 7 * 24 * 60 * 60 * 1000).toISOString(), // 7 days ago
      },
      {
        id: "notif2",
        userId: "admin1",
        title: "New Service Request",
        message: "John Doe submitted a new service request: New Feature Request",
        type: "request",
        relatedId: "req2",
        read: false,
        createdAt: new Date(now - 3 * 24 * 60 * 60 * 1000).toISOString(), // 3 days ago
      },
      {
        id: "notif3",
        userId: "client1",
        title: "Request Status Updated",
        message: 'Your request "Website Performance Issue" has been updated to In Progress',
        type: "status",
        relatedId: "req1",
        read: false,
        createdAt: new Date(now - 6 * 24 * 60 * 60 * 1000).toISOString(), // 6 days ago
      },
      {
        id: "notif4",
        userId: "client1",
        title: "New Comment",
        message: 'Admin commented on your request "Website Performance Issue"',
        type: "comment",
        relatedId: "comment1",
        read: true,
        createdAt: new Date(now - 6 * 24 * 60 * 60 * 1000).toISOString(), // 6 days ago
      },
    ]

    // Sample activities
    const activities = [
      {
        id: "activity1",
        userId: "admin1",
        action: "created",
        entityType: "task",
        entityId: "task1",
        details: 'Created task "Investigate Website Performance"',
        createdAt: new Date(now - 7 * 24 * 60 * 60 * 1000).toISOString(), // 7 days ago
      },
      {
        id: "activity2",
        userId: "admin1",
        action: "updated",
        entityType: "request",
        entityId: "req1",
        details: 'Updated request "Website Performance Issue" status to In Progress',
        createdAt: new Date(now - 6 * 24 * 60 * 60 * 1000).toISOString(), // 6 days ago
      },
      {
        id: "activity3",
        userId: "admin1",
        action: "commented",
        entityType: "request",
        entityId: "req1",
        details: 'Commented on request "Website Performance Issue"',
        createdAt: new Date(now - 6 * 24 * 60 * 60 * 1000).toISOString(), // 6 days ago
      },
      {
        id: "activity4",
        userId: "client1",
        action: "created",
        entityType: "request",
        entityId: "req2",
        details: 'Created request "New Feature Request"',
        createdAt: new Date(now - 3 * 24 * 60 * 60 * 1000).toISOString(), // 3 days ago
      },
    ]

    // Sample dashboard widgets configuration
    const dashboardWidgets = {
      client1: ["recentRequests", "highPriorityTasks", "upcomingTasks"],
      client2: ["recentRequests", "requestStatus", "upcomingTasks"],
    }

    // Save all sample data to localStorage
    localStorage.setItem(this.KEYS.USERS, JSON.stringify(users))
    localStorage.setItem(this.KEYS.REQUESTS, JSON.stringify(requests))
    localStorage.setItem(this.KEYS.TASKS, JSON.stringify(tasks))
    localStorage.setItem(this.KEYS.COMMENTS, JSON.stringify(comments))
    localStorage.setItem(this.KEYS.NOTIFICATIONS, JSON.stringify(notifications))
    localStorage.setItem(this.KEYS.ACTIVITIES, JSON.stringify(activities))
    localStorage.setItem(this.KEYS.DASHBOARD_WIDGETS, JSON.stringify(dashboardWidgets))
  },

  /**
   * Generic method to get all items of a specific type
   * @param {string} key - The localStorage key
   * @returns {Array} - Array of items
   */
  getAll(key) {
    const data = localStorage.getItem(key)
    return data ? JSON.parse(data) : []
  },

  /**
   * Generic method to get a single item by ID
   * @param {string} key - The localStorage key
   * @param {string} id - The item ID
   * @returns {Object|null} - The item or null if not found
   */
  getById(key, id) {
    const items = this.getAll(key)
    return items.find((item) => item.id === id) || null
  },

  /**
   * Generic method to save an item
   * @param {string} key - The localStorage key
   * @param {Object} item - The item to save
   * @returns {Object} - The saved item
   */
  save(key, item) {
    const items = this.getAll(key)
    const now = new Date().toISOString()

    // If item has no ID, generate one
    if (!item.id) {
      item.id = this.generateId(key)
      item.createdAt = now
    }

    // Update the updatedAt timestamp
    item.updatedAt = now

    // Check if item already exists
    const existingIndex = items.findIndex((i) => i.id === item.id)

    if (existingIndex > -1) {
      items[existingIndex] = item // Replace existing item
    } else {
      items.push(item) // Add new item
    }

    localStorage.setItem(key, JSON.stringify(items))
    return item
  },

  /**
   * Generic method to delete an item
   * @param {string} key - The localStorage key
   * @param {string} id - The item ID
   */
  delete(key, id) {
    let items = this.getAll(key)
    items = items.filter((item) => item.id !== id)
    localStorage.setItem(key, JSON.stringify(items))
  },

  /**
   * Generate a unique ID for a new item
   * @param {string} key - The localStorage key (used as a prefix)
   * @returns {string} - A unique ID
   */
  generateId(key) {
    return key + "_" + Math.random().toString(36).substring(2, 15)
  },
}
