/**
 * Loading States & Progress Indicators
 * Usage: 
 *   loading.show('Loading...')
 *   loading.hide()
 *   loading.progress(50) // 0-100
 */

(function() {
  'use strict';

  let loadingOverlay = null;
  let progressBar = null;

  // Create loading overlay
  function createLoadingOverlay() {
    if (loadingOverlay) return loadingOverlay;

    loadingOverlay = document.createElement('div');
    loadingOverlay.id = 'loading-overlay';
    loadingOverlay.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      backdrop-filter: blur(4px);
    `;

    const spinner = document.createElement('div');
    spinner.style.cssText = `
      background: #fff;
      border: 3px solid #000;
      border-radius: 16px;
      padding: 30px 40px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
      font-family: 'Space Grotesk', sans-serif;
      min-width: 200px;
    `;

    const spinnerIcon = document.createElement('div');
    spinnerIcon.className = 'loading-spinner';
    spinnerIcon.style.cssText = `
      width: 40px;
      height: 40px;
      border: 4px solid rgba(0, 0, 0, 0.1);
      border-top-color: #000;
    `;

    const text = document.createElement('div');
    text.id = 'loading-text';
    text.style.cssText = `
      font-size: 16px;
      font-weight: 600;
      color: #333;
      text-align: center;
    `;
    text.textContent = 'Loading...';

    progressBar = document.createElement('div');
    progressBar.id = 'loading-progress';
    progressBar.style.cssText = `
      width: 100%;
      height: 4px;
      background: rgba(0, 0, 0, 0.1);
      border-radius: 2px;
      overflow: hidden;
      display: none;
    `;

    const progressFill = document.createElement('div');
    progressFill.id = 'loading-progress-fill';
    progressFill.style.cssText = `
      height: 100%;
      background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
      width: 0%;
      transition: width 0.3s ease;
    `;

    progressBar.appendChild(progressFill);
    spinner.appendChild(spinnerIcon);
    spinner.appendChild(text);
    spinner.appendChild(progressBar);
    loadingOverlay.appendChild(spinner);
    document.body.appendChild(loadingOverlay);

    return loadingOverlay;
  }

  // Show loading overlay
  function showLoading(message = 'Loading...', showProgress = false) {
    const overlay = createLoadingOverlay();
    const textEl = document.getElementById('loading-text');
    if (textEl) textEl.textContent = message;
    
    if (showProgress) {
      progressBar.style.display = 'block';
    } else {
      progressBar.style.display = 'none';
    }
    
    overlay.style.display = 'flex';
  }

  // Hide loading overlay
  function hideLoading() {
    if (loadingOverlay) {
      loadingOverlay.style.display = 'none';
      if (progressBar) {
        const fill = document.getElementById('loading-progress-fill');
        if (fill) fill.style.width = '0%';
      }
    }
  }

  // Set progress (0-100)
  function setProgress(percent) {
    if (!progressBar) return;
    progressBar.style.display = 'block';
    const fill = document.getElementById('loading-progress-fill');
    if (fill) {
      fill.style.width = Math.min(100, Math.max(0, percent)) + '%';
    }
  }

  // Button loading state helper
  function setButtonLoading(button, loading) {
    if (!button) return;
    
    if (loading) {
      button.disabled = true;
      button.classList.add('btn-loading');
      button.dataset.originalText = button.textContent;
      button.textContent = '';
    } else {
      button.disabled = false;
      button.classList.remove('btn-loading');
      if (button.dataset.originalText) {
        button.textContent = button.dataset.originalText;
        delete button.dataset.originalText;
      }
    }
  }

  // Public API
  window.loading = {
    show: showLoading,
    hide: hideLoading,
    progress: setProgress,
    button: setButtonLoading
  };
})();

