  </div>
  <!-- End Main Content -->
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Auto-hide alerts -->
  <script>
    // Auto dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', () => {
      const alerts = document.querySelectorAll('.alert.alert-dismissible');
      alerts.forEach(alert => {
        setTimeout(() => {
          const bsAlert = new bootstrap.Alert(alert);
          bsAlert.close();
        }, 5000);
      });
    });
  </script>
</body>
</html>

