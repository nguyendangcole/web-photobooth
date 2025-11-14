<footer class="site-footer">
  <div class="footer-container">
    <div class="footer-links">
      <a href="?p=studio">Studio</a>
      <a href="?p=info">Info</a>
      <a href="?p=service">Service</a>
      <a href="?p=qa">Q&A</a>
      <a href="?p=contact">Contact</a>
    </div>
    <p class="footer-copyright">© <?= date('Y') ?> <strong>Space Photobooth</strong> | Show your style</p>
  </div>
</footer>

<style>
.site-footer {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  border-top: 2px solid #c1ff72;
  margin-top: 2rem;
  padding: 0.75rem 0;
  position: relative;
  overflow: hidden;
  width: 100%;
  clear: both;
}

.site-footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    radial-gradient(circle at 20% 50%, rgba(193, 255, 114, 0.08) 0%, transparent 50%),
    radial-gradient(circle at 80% 50%, rgba(138, 43, 226, 0.08) 0%, transparent 50%);
  pointer-events: none;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 1;
}

.footer-links {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1.5rem;
  flex-wrap: wrap;
  margin-bottom: 0.5rem;
}

.footer-links a {
  color: #e0e0e0;
  text-decoration: none;
  font-size: 0.85rem;
  transition: all 0.3s ease;
  position: relative;
}

.footer-links a::after {
  content: '';
  position: absolute;
  bottom: -3px;
  left: 0;
  width: 0;
  height: 2px;
  background: #c1ff72;
  transition: width 0.3s ease;
}

.footer-links a:hover {
  color: #c1ff72;
  text-shadow: 0 0 8px rgba(193, 255, 114, 0.4);
}

.footer-links a:hover::after {
  width: 100%;
}

.footer-copyright {
  text-align: center;
  color: #b0b0b0;
  font-size: 0.8rem;
  margin: 0;
  padding-top: 0.5rem;
  border-top: 1px solid rgba(193, 255, 114, 0.15);
}

.footer-copyright strong {
  color: #c1ff72;
  font-weight: 700;
}

@media (max-width: 576px) {
  .footer-links {
    gap: 1rem;
    font-size: 0.8rem;
  }
  
  .site-footer {
    padding: 0.6rem 0;
  }
  
  .footer-copyright {
    font-size: 0.75rem;
    padding-top: 0.4rem;
  }
}
</style>

<!-- Bootstrap Bundle from CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
