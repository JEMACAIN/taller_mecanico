document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainToggleBtn = document.getElementById('mainToggleBtn');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    // Check if there's a saved state in localStorage
    const sidebarState = localStorage.getItem('sidebarCollapsed');
    if (sidebarState === 'true') {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
    }

    // Function to toggle sidebar
    function toggleSidebar() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }

    // Handle sidebar toggle button click
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    // Handle main toggle button click
    if (mainToggleBtn) {
        mainToggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    // Handle clicks outside sidebar
    document.addEventListener('click', function(e) {
        if (!sidebar.contains(e.target) && !mainToggleBtn.contains(e.target)) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            localStorage.setItem('sidebarCollapsed', 'true');
        }
    });

    // Prevent sidebar from collapsing when clicking inside it
    sidebar.addEventListener('click', function(e) {
        e.stopPropagation();
    });
}); 