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
let currentFilter = "none";
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

  // lớp gốc
  ctx.filter = "none";
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

  // áp filter/preset
  if (!currentFilter.startsWith("preset")) {
    ctx.globalCompositeOperation = "source-over";
    ctx.filter = currentFilter;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
  } else {
    switch (currentFilter) {
      case "preset1":
        ctx.globalCompositeOperation = "multiply";
        ctx.fillStyle = "rgba(0,225,250,0.13)";
        ctx.fillRect(0,0,canvas.width,canvas.height);
        ctx.globalCompositeOperation = "source-over";
        ctx.filter = "brightness(104%) contrast(104%) saturate(122%)";
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        break;
      case "preset2":
        ctx.globalCompositeOperation = "multiply";
        ctx.fillStyle = "rgba(250,0,204,0.15)";
        ctx.fillRect(0,0,canvas.width,canvas.height);
        ctx.globalCompositeOperation = "source-over";
        ctx.filter = "contrast(128%) saturate(120%)";
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        break;
      case "preset3":
        ctx.globalCompositeOperation = "multiply";
        ctx.fillStyle = "rgba(0,142,250,0.15)";
        ctx.fillRect(0,0,canvas.width,canvas.height);
        ctx.globalCompositeOperation = "source-over";
        ctx.filter = "contrast(128%) grayscale(100%) saturate(120%)";
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        break;
      case "preset4":
        ctx.globalCompositeOperation = "soft-light";
        ctx.fillStyle = "rgba(58,0,250,0.5)";
        ctx.fillRect(0,0,canvas.width,canvas.height);
        ctx.globalCompositeOperation = "source-over";
        ctx.filter = "contrast(107%) saturate(165%) sepia(50%)";
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        break;
      case "preset5":
        ctx.globalCompositeOperation = "overlay";
        ctx.fillStyle = "rgba(250,0,0,0.3)";
        ctx.fillRect(0,0,canvas.width,canvas.height);
        ctx.globalCompositeOperation = "source-over";
        ctx.filter = "brightness(105%) contrast(106%) saturate(90%)";
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        break;
    }
  }

  return canvas.toDataURL("image/png");
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
        // NÉN TRƯỚC KHI LƯU → JPEG nhỏ
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
