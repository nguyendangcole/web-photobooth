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
    toggle.className = 'password-toggle-btn';
    toggle.setAttribute('aria-label', 'Toggle password visibility');
    
    // Icon eye (show password)
    const eyeIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
      <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
      <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
    </svg>`;
    
    // Icon eye-slash (hide password)
    const eyeSlashIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
      <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-.769 0-1.539-.2-2.309-.5l-.747.746A7.028 7.028 0 0 0 8 13.5c5 0 8-5.5 8-5.5a15.98 15.98 0 0 0-2.641-2.762z"/>
      <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l4.474 4.474z"/>
      <path d="M13.646 15.354l-12-12 .708-.708 12 12-.708.708z"/>
    </svg>`;
    
    toggle.innerHTML = eyeIcon;
    toggle.onclick = () => {
      if (pw.type === 'password') {
        pw.type = 'text';
        toggle.innerHTML = eyeSlashIcon;
      } else {
        pw.type = 'password';
        toggle.innerHTML = eyeIcon;
      }
    };
    
    // Đảm bảo parent có position relative
    const parent = pw.parentElement;
    if (parent) {
      parent.style.position = 'relative';
      parent.appendChild(toggle);
    }
  });
  
  