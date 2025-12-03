/**
 * Toast Notification System
 * Usage: toast.show('Message', 'success|error|warning|info', duration)
 */

(function() {
  'use strict';

  // Create toast container if it doesn't exist
  function getToastContainer() {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      container.className = 'toast-container';
      document.body.appendChild(container);
    }
    return container;
  }

  // Get icon for toast type
  function getIcon(type) {
    const icons = {
      success: '✓',
      error: '✕',
      warning: '⚠',
      info: 'ℹ'
    };
    return icons[type] || icons.info;
  }

  // Show toast notification
  function showToast(message, type = 'info', duration = 4000) {
    const container = getToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    const icon = getIcon(type);
    const iconEl = document.createElement('div');
    iconEl.className = 'toast-icon';
    iconEl.textContent = icon;
    
    const content = document.createElement('div');
    content.className = 'toast-content';
    content.textContent = message;
    
    const closeBtn = document.createElement('button');
    closeBtn.className = 'toast-close';
    closeBtn.innerHTML = '×';
    closeBtn.setAttribute('aria-label', 'Close');
    closeBtn.onclick = () => removeToast(toast);
    
    toast.appendChild(iconEl);
    toast.appendChild(content);
    toast.appendChild(closeBtn);
    
    // Add progress bar if duration is set
    if (duration > 0) {
      const progress = document.createElement('div');
      progress.className = 'toast-progress';
      progress.style.animationDuration = duration + 'ms';
      toast.appendChild(progress);
    }
    
    container.appendChild(toast);
    
    // Auto remove after duration
    if (duration > 0) {
      setTimeout(() => {
        removeToast(toast);
      }, duration);
    }
    
    return toast;
  }

  // Remove toast with animation
  function removeToast(toast) {
    if (!toast || !toast.parentNode) return;
    toast.classList.add('slide-out');
    setTimeout(() => {
      if (toast.parentNode) {
        toast.parentNode.removeChild(toast);
      }
    }, 300);
  }

  // Public API
  window.toast = {
    show: showToast,
    success: (message, duration) => showToast(message, 'success', duration),
    error: (message, duration) => showToast(message, 'error', duration),
    warning: (message, duration) => showToast(message, 'warning', duration),
    info: (message, duration) => showToast(message, 'info', duration),
    remove: removeToast
  };

  // Auto-load CSS if not already loaded
  if (!document.getElementById('toast-css')) {
    const link = document.createElement('link');
    link.id = 'toast-css';
    link.rel = 'stylesheet';
    // Try to get base URL from various sources
    const baseUrl = window.APP_BASE || (document.querySelector('base')?.getAttribute('href') || '/');
    const cssPath = baseUrl.endsWith('/') ? baseUrl + 'css/toast.css' : baseUrl + '/css/toast.css';
    link.href = cssPath;
    document.head.appendChild(link);
  }
})();

