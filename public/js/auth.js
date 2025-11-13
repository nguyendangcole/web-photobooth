document.addEventListener("DOMContentLoaded", () => {
    const slides = document.querySelectorAll(".slide");
    let slideIndex = 0;
  
    function showSlides() {
      slides.forEach((s, i) => s.classList.toggle("active", i === slideIndex));
      slideIndex = (slideIndex + 1) % slides.length;
    }
  
    if (slides.length > 0) {
      showSlides(); // show first
      setInterval(showSlides, 5000); // change every 5s
    }
  });

  document.querySelectorAll('input[type="password"]').forEach(pw => {
    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.textContent = '👁️';
    toggle.className = 'btn btn-sm btn-outline-secondary position-absolute end-0 top-50 translate-middle-y me-2';
    toggle.onclick = () => pw.type = pw.type === 'password' ? 'text' : 'password';
    pw.parentElement.style.position = 'relative';
    pw.parentElement.appendChild(toggle);
  });
  
  