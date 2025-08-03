<!-- Footer Section -->
 <footer class="bg-dark text-white py-3">
  <div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
      
      <!-- Left-aligned text -->
      <p class="mb-2 mb-md-0">&copy;  Track My Bus | All Rights Reserved</p>
      
      <!-- Right-aligned social icons -->
      <div>
        <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
        <a href="#" class="text-white me-3"><i class="bi bi-twitter"></i></a>
        <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
        <a href="#" class="text-white me-3"><i class="bi bi-linkedin"></i></a>
        <a href="#" class="text-white"><i class="bi bi-youtube"></i></a>
      </div>
      
    </div>
  </div>
</footer>


 <!-- Bootstrap 5 JS (CDN) and Popper.js (for dropdowns and tooltips) -->
 <!-- Async and Defer added -->
 <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" defer></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" defer></script>

 <script>
document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const toggle = document.getElementById('toggleDarkMode');

    // Apply saved mode
    if (localStorage.getItem('darkMode') === 'enabled') {
        body.classList.add('dark-mode');
    }

    toggle.addEventListener('click', function () {
        body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', body.classList.contains('dark-mode') ? 'enabled' : 'disabled');
    });
});
</script>
 </body>
</html>