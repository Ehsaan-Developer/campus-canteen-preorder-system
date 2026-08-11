<!-- footer.php -->
<footer>
    <p style="margin: 0; color: var(--text-main); font-weight: 500;">
        Designed & Developed by <strong>Ehsaan Ul Haq Tawakly</strong>
        <img src="https://flagcdn.com/w40/pk.png" width="24" style="vertical-align: middle; margin-left: 5px;" alt="Pakistan Flag">
    </p>
    <p style="margin: 5px 0 0; font-size: 13px; color: var(--text-muted);">
        &copy; 2026 Campus Canteen System | All Rights Reserved
    </p>
</footer>

<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    // Resolve relative path dynamically depending on folder depth
    const path = window.location.pathname.includes('/php/') || window.location.pathname.includes('/python/') ? '../sw.js' : 'sw.js';
    navigator.serviceWorker.register(path)
      .then(reg => console.log('PWA Service Worker registered:', reg.scope))
      .catch(err => console.log('Service Worker registration failed:', err));
  });
}
</script>