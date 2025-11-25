/* =========================
   Photobooth.js (fixed)
   ========================= */

// ---------- DOM ----------
const video          = document.getElementById("video");
const startBtn       = document.getElementById("startBtn");
const exportBtn      = document.getElementById("exportBtn");
const countdownEl    = document.getElementById("countdown");
const capturedImages = document.getElementById("captured-images");
const timerOptions   = document.querySelectorAll(".timer-option");
const filterOptions  = document.querySelectorAll(".filter-option");

// ---------- BASE ----------
(function ensureBase() {
  if (!window.APP_BASE) {
    const b = document.querySelector("base")?.getAttribute("href") || "/";
    window.APP_BASE = b.endsWith("/") ? b : b + "/";
  }
})();
const FRAME_URL = window.APP_BASE + "index.php?p=frame";

// ---------- CONFIG ----------
let captureDelay = 3;
// Use global currentFilter if available, otherwise default to "none"
if (typeof window.currentFilter === 'undefined') {
  window.currentFilter = "none";
}
const MAX_SHOTS = 4;

// Ảnh đã chụp (dataURL JPEG nén)
let photos = [];

// ---------- UI helpers ----------
function showAlert(message) {
  alert(message);
}
function toggleExportButton() {
  if (!exportBtn) return;
  exportBtn.disabled = photos.length < MAX_SHOTS;
}
function addThumb(dataUrl) {
  const img = document.createElement("img");
  img.src = dataUrl;
  img.className = "border shadow m-2";
  img.style.width = "120px";
  capturedImages?.appendChild(img);
}
function clearThumbs() {
  if (capturedImages) capturedImages.innerHTML = "";
}

// ---------- Timer dropdown ----------
timerOptions.forEach(opt => {
  opt.addEventListener("click", (e) => {
    e.preventDefault();
    captureDelay = parseInt(opt.getAttribute("data-time"), 10) || 3;
    const dd = document.getElementById("timerDropdown");
    if (dd) dd.textContent = `${captureDelay} seconds`;
    timerOptions.forEach(o => o.classList.remove("active"));
    opt.classList.add("active");
  });
});

// ---------- Filter preview ----------
// Filter handling is done in photobooth.php inline script to avoid conflicts
// This code is disabled to prevent duplicate event listeners
/*
filterOptions.forEach(opt => {
  opt.addEventListener("click", (e) => {
    e.preventDefault();
    currentFilter = opt.getAttribute("data-filter") || "none";

    filterOptions.forEach(o => o.classList.remove("active"));
    opt.classList.add("active");

    const frame = video.closest(".camera-frame");
    frame?.classList.remove("preset1","preset2","preset3","preset4","preset5");

    if (currentFilter.startsWith("preset")) {
      frame?.classList.add(currentFilter);
      video.style.filter = "none";
    } else {
      video.style.filter = currentFilter;
    }

    const btn = document.getElementById("filterDropdown");
    if (btn) btn.textContent = opt.textContent;
  });
});
*/

// ---------- Webcam ----------
if (navigator.mediaDevices?.getUserMedia) {
  navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => video.srcObject = stream)
    .catch(err => showAlert("Không thể truy cập webcam: " + err));
} else {
  showAlert("Trình duyệt không hỗ trợ camera.");
}

// ---------- Overlay Ready ----------
let overlay;
(function mountOverlay() {
  const frame = video.closest(".camera-frame");
  overlay = document.createElement("div");
  Object.assign(overlay.style, {
    position: "absolute", inset: 0,
    display: "flex", alignItems: "center", justifyContent: "center",
    color: "#fff", fontWeight: "800", fontSize: "28px",
    textShadow: "0 2px 10px rgba(0,0,0,.6)",
    opacity: 0, pointerEvents: "none",
    transition: "opacity .2s", zIndex: 5
  });
  frame?.appendChild(overlay);
})();
const showPrompt = (t="") => { overlay.textContent = t; overlay.style.opacity = t ? 1 : 0; };
const hidePrompt = () => { overlay.style.opacity = 0; };

// ---------- Effects ----------
function flashEffect() {
  const el = document.createElement("div");
  Object.assign(el.style, { position:"fixed", inset:0, background:"#fff", opacity:1, zIndex:9999 });
  document.body.appendChild(el);
  setTimeout(() => { el.style.transition="opacity .5s"; el.style.opacity=0; setTimeout(()=>el.remove(), 500); }, 100);
}

// ---------- Utils ----------
const sleep = (ms) => new Promise(r => setTimeout(r, ms));
function runCountdownAsync(seconds) {
  return new Promise(resolve => {
    let t = seconds;
    if (countdownEl) { countdownEl.style.opacity = 1; countdownEl.textContent = t; }
    const timer = setInterval(() => {
      t--;
      if (countdownEl) countdownEl.textContent = t > 0 ? t : "";
      if (t === 0) {
        clearInterval(timer);
        if (countdownEl) { countdownEl.textContent = ""; countdownEl.style.opacity = 0; }
        resolve();
      }
    }, 1000);
  });
}

// Nén/resize JPEG để tránh vượt quota
function compressDataURL(dataUrl, maxEdge = 1400, quality = 0.82) {
  return new Promise((resolve, reject) => {
    const img = new Image();
    img.onload = () => {
      let { width, height } = img;
      const longEdge = Math.max(width, height);
      const scale = longEdge > maxEdge ? (maxEdge / longEdge) : 1;
      const w = Math.round(width * scale);
      const h = Math.round(height * scale);

      const c = document.createElement('canvas');
      c.width = w; c.height = h;
      const ctx = c.getContext('2d');
      ctx.drawImage(img, 0, 0, w, h);

      const out = c.toDataURL('image/jpeg', quality);
      resolve(out);
    };
    img.onerror = () => reject(new Error('Không nén được ảnh.'));
    img.src = dataUrl;
  });
}

// ---------- Capture (canvas + filter/preset) ----------
function captureOncePNG() {
  if (!video.videoWidth || !video.videoHeight) {
    showAlert("Camera chưa sẵn sàng, vui lòng thử lại.");
    return null;
  }
  const canvas = document.createElement("canvas");
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  const ctx = canvas.getContext("2d");

  // mirror
  ctx.translate(canvas.width, 0);
  ctx.scale(-1, 1);

  // Get current filter - read directly from DOM (most reliable)
  let currentFilter = "none";
  
  // First, check camera-wrapper for preset classes
  const cameraWrapper = document.querySelector('.camera-wrapper');
  if (cameraWrapper) {
    for (let i = 1; i <= 10; i++) {
      if (cameraWrapper.classList.contains(`preset${i}`)) {
        currentFilter = `preset${i}`;
        break;
      }
    }
  }
  
  // If no preset class found, check active filter option
  if (currentFilter === "none") {
    const activeFilterOption = document.querySelector('.filter-option.active');
    if (activeFilterOption && activeFilterOption.dataset.filter) {
      currentFilter = activeFilterOption.dataset.filter;
    }
  }
  
  // Fallback to window.currentFilter
  if (currentFilter === "none" && window.currentFilter) {
    currentFilter = window.currentFilter;
  }
  
  
  // Reset composite operation
  ctx.globalCompositeOperation = "source-over";
  ctx.globalAlpha = 1.0;
  
  // Apply filter/preset
  if (!currentFilter || currentFilter === "none" || !currentFilter.startsWith("preset")) {
    // For CSS filters (non-preset)
    // Read filter from multiple sources
    const videoInlineFilter = video.style.filter;
    const computedStyle = window.getComputedStyle(video);
    const computedFilter = computedStyle.filter;
    
    // Priority: computed > inline > currentFilter
    let filterToApply = "none";
    if (computedFilter && computedFilter !== "none") {
      filterToApply = computedFilter;
    } else if (videoInlineFilter && videoInlineFilter !== "none" && videoInlineFilter !== "") {
      filterToApply = videoInlineFilter;
    } else if (currentFilter && currentFilter !== "none") {
      filterToApply = currentFilter;
    }
    
    ctx.filter = filterToApply;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
  } else {
    // For preset filters: 
    // Apply filter with stronger color overlay to match preview
    
    // Step 1: Draw video with CSS filter
    let cssFilter = "none";
    switch (currentFilter) {
      case "preset1": cssFilter = "brightness(104%) contrast(104%) saturate(122%)"; break;
      case "preset2": cssFilter = "contrast(128%) saturate(120%)"; break;
      case "preset3": cssFilter = "contrast(128%) grayscale(100%) saturate(120%)"; break;
      case "preset4": cssFilter = "contrast(107%) saturate(165%) sepia(50%)"; break;
      case "preset5": cssFilter = "brightness(105%) contrast(106%) saturate(90%)"; break;
      case "preset6": cssFilter = "brightness(105%) contrast(115%) saturate(130%)"; break;
      case "preset7": cssFilter = "brightness(110%) contrast(105%) saturate(140%) sepia(30%)"; break;
      case "preset8": cssFilter = "brightness(108%) contrast(102%) saturate(115%)"; break;
      case "preset9": cssFilter = "brightness(115%) contrast(130%) saturate(150%)"; break;
      case "preset10": cssFilter = "brightness(95%) contrast(110%) saturate(80%) sepia(40%)"; break;
    }
    
    ctx.filter = cssFilter;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    ctx.filter = "none"; // Reset filter
    
    // Step 2: Apply color overlay to match preview
    // Use subtle opacity to match CSS preview exactly
    switch (currentFilter) {
      case "preset1": 
        // Cyan Dream
        ctx.fillStyle = "rgba(0, 225, 250, 0.18)";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        break;
      case "preset2": 
        // Pink Magic
        ctx.fillStyle = "rgba(250, 0, 204, 0.2)";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        break;
      case "preset3": 
        // Mono Blue
        ctx.fillStyle = "rgba(0, 142, 250, 0.2)";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        break;
      case "preset4": 
        // Soft Glow
        ctx.fillStyle = "rgba(58, 0, 250, 0.25)";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        break;
      case "preset5": 
        // Red Heat
        ctx.fillStyle = "rgba(250, 0, 0, 0.2)";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        break;
      case "preset6": 
        // Purple Haze
        ctx.fillStyle = "rgba(140, 82, 255, 0.2)";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        break;
      case "preset7": 
        // Golden Hour
        ctx.fillStyle = "rgba(255, 189, 89, 0.22)";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        break;
      case "preset8": 
        // Mint Fresh
        ctx.fillStyle = "rgba(193, 255, 114, 0.2)";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        break;
      case "preset9": 
        // Neon Night - gradient overlay
        const gradient = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
        gradient.addColorStop(0, "rgba(255, 0, 110, 0.18)");
        gradient.addColorStop(1, "rgba(0, 245, 255, 0.18)");
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        break;
      case "preset10": 
        // Vintage
        ctx.fillStyle = "rgba(139, 115, 85, 0.22)";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        break;
    }
  }

  // Draw stickers on canvas (after video and filters)
  // Reset transform before drawing stickers (canvas was mirrored for video)
  ctx.setTransform(1, 0, 0, 1, 0, 0); // Reset to identity matrix
  drawStickersOnCanvas(ctx, canvas);

  return canvas.toDataURL("image/png");
}

// Helper function to draw stickers on canvas
function drawStickersOnCanvas(ctx, canvas) {
  const photoboothStudio = document.querySelector('.photobooth-studio');
  const cameraWrapper = document.querySelector('.camera-wrapper');
  const videoElement = document.getElementById('video');
  
  if (!photoboothStudio || !cameraWrapper || !videoElement) return;
  
  const stickers = photoboothStudio.querySelectorAll('.placed-sticker');
  if (stickers.length === 0) return;
  
  // Get video position and size
  const videoRect = videoElement.getBoundingClientRect();
  const cameraWrapperRect = cameraWrapper.getBoundingClientRect();
  
  // Get actual video dimensions (same as canvas dimensions)
  const videoActualWidth = videoElement.videoWidth || canvas.width;
  const videoActualHeight = videoElement.videoHeight || canvas.height;
  
  // Calculate video position within camera-wrapper
  const videoOffsetX = videoRect.left - cameraWrapperRect.left;
  const videoOffsetY = videoRect.top - cameraWrapperRect.top;
  
  // Calculate how video is actually displayed vs its actual size
  // Video uses object-fit: cover, so it fills container but may be cropped
  const videoDisplayAspect = videoRect.width / videoRect.height;
  const videoActualAspect = videoActualWidth / videoActualHeight;
  
  // Determine which dimension is the limiting factor (width or height)
  let scale, offsetX = 0, offsetY = 0;
  
  if (videoDisplayAspect > videoActualAspect) {
    // Display is wider than video -> height is limiting, video is cropped on sides
    scale = videoActualHeight / videoRect.height;
    const scaledWidth = videoRect.width * scale;
    offsetX = (scaledWidth - videoActualWidth) / 2;
  } else {
    // Display is taller than video -> width is limiting, video is cropped on top/bottom
    scale = videoActualWidth / videoRect.width;
    const scaledHeight = videoRect.height * scale;
    offsetY = (scaledHeight - videoActualHeight) / 2;
  }
  
  // Draw each sticker
  stickers.forEach(sticker => {
    const stickerRect = sticker.getBoundingClientRect();
    const stickerImg = sticker.querySelector('img');
    
    if (!stickerImg || !stickerImg.complete) return; // Skip if image not loaded
    
    // Calculate sticker position relative to camera-wrapper
    const stickerX = stickerRect.left - cameraWrapperRect.left;
    const stickerY = stickerRect.top - cameraWrapperRect.top;
    
    // Calculate position relative to video
    const relativeX = stickerX - videoOffsetX;
    const relativeY = stickerY - videoOffsetY;
    
    // Only draw if sticker is within video bounds
    if (relativeX >= -stickerRect.width && relativeY >= -stickerRect.height && 
        relativeX < videoRect.width && relativeY < videoRect.height) {
      
      // Calculate scaled position and size for canvas
      // Note: canvas was mirrored for video, but we reset transform, so now we need to mirror X again
      // Video is mirrored, so sticker X needs to be flipped to match
      
      // Get the displayed sticker size (what user sees on screen)
      const displayedWidth = stickerRect.width;
      const displayedHeight = stickerRect.height;
      
      // Use uniform scale to maintain aspect ratio
      // Adjust position to account for video cropping
      const canvasX = canvas.width - ((relativeX * scale) + offsetX) - (displayedWidth * scale);
      const canvasY = (relativeY * scale) + offsetY;
      const canvasWidth = displayedWidth * scale;
      const canvasHeight = displayedHeight * scale;
      
      // Draw sticker on canvas
      ctx.save();
      ctx.globalCompositeOperation = 'source-over';
      ctx.globalAlpha = 1.0;
      
      try {
        // Draw directly from the img element if it's loaded
        if (stickerImg.complete && stickerImg.naturalWidth > 0) {
          // Draw with maintained aspect ratio
          ctx.drawImage(stickerImg, canvasX, canvasY, canvasWidth, canvasHeight);
        }
      } catch (e) {
        console.warn('Error drawing sticker:', e);
      }
      
      ctx.restore();
    }
  });
}

// ---------- Chụp liên tục (4 tấm) ----------
async function startGuidedSession(totalShots = MAX_SHOTS) {
  try {
    startBtn.disabled = true;

    // reset để chụp lại
    photos = [];
    clearThumbs();
    toggleExportButton();

    // đảm bảo video ready
    if (!video.videoWidth || !video.videoHeight) {
      await new Promise(res => video.addEventListener("loadedmetadata", res, { once: true }));
    }

    for (let i = 0; i < totalShots; i++) {
      showPrompt(i === 0 ? "Ready..." : "Tấm kế tiếp!");
      await sleep(800);
      hidePrompt();

      await runCountdownAsync(captureDelay);

      const raw = captureOncePNG();
      if (raw) {
        // Compress before saving → smaller JPEG
        const compressed = await compressDataURL(raw, 1400, 0.82);
        photos.push(compressed);
        addThumb(compressed);
        toggleExportButton();
        flashEffect();
      }

      await sleep(350);
    }

    showPrompt("Done!");
    await sleep(700);
    hidePrompt();

  } catch (e) {
    console.error(e);
    showAlert("Có lỗi xảy ra khi chụp.");
  } finally {
    startBtn.disabled = false;
  }
}

// ---------- Buttons ----------
startBtn?.addEventListener("click", () => {
  startGuidedSession(MAX_SHOTS);
});

exportBtn?.addEventListener("click", async () => {
  if (photos.length < MAX_SHOTS) {
    showAlert(`Chưa đủ ảnh để xuất frame! (${photos.length}/${MAX_SHOTS})`);
    return;
  }

  // Lần 1: thử lưu
  try {
    localStorage.setItem("selectedPhotos", JSON.stringify(photos.slice(0, MAX_SHOTS)));
  } catch (e) {
    // Nếu quota → nén mạnh hơn và thử lại
    try {
      const tighter = await Promise.all(
        photos.slice(0, MAX_SHOTS).map(p => compressDataURL(p, 1200, 0.7))
      );
      localStorage.setItem("selectedPhotos", JSON.stringify(tighter));
    } catch (e2) {
      console.error(e2);
      showAlert("Bộ nhớ trình duyệt đầy. Hãy dùng nút “Add to Photobook” ở trang Frame (lưu lên server) rồi mở Photobook để xem.");
      return;
    }
  }

  window.location.href = FRAME_URL;
});

// ---------- Init ----------
document.addEventListener("DOMContentLoaded", toggleExportButton);

