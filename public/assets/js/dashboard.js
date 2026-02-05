document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.querySelector('.toggle-sidebar');
    const sidebar = document.querySelector('.sidebar');
    const mainWrapper = document.querySelector('.main-wrapper');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function (event) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickOnToggle = toggleBtn.contains(event.target);

        if (window.innerWidth <= 992 && !isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });

    // Handle Submenus
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            const parentLi = this.parentElement;

            // Toggle open class
            parentLi.classList.toggle('open');

            // Optional: Close other submenus
            submenuToggles.forEach(otherToggle => {
                const otherLi = otherToggle.parentElement;
                if (otherLi !== parentLi) {
                    otherLi.classList.remove('open');
                }
            });
        });
    });

    // Handle Window Resize
    window.addEventListener('resize', function () {
        if (window.innerWidth > 992) {
            sidebar.classList.remove('active');
        }
    });
});
