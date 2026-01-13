function showToast(message, type = 'info') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    let iconClass = 'fa-solid fa-circle-info';
    if (type === 'success') iconClass = 'fa-solid fa-circle-check';
    if (type === 'error') iconClass = 'fa-solid fa-circle-exclamation';

    toast.innerHTML = `
        <i class="toast-icon ${iconClass}"></i>
        <span class="toast-message">${message}</span>
        <div class="toast-progress">
            <div class="toast-progress-bar"></div>
        </div>
    `;

    container.appendChild(toast);

    // Auto remove logic matches css animation duration (4s)
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.5s ease forwards';
        toast.addEventListener('animationend', () => {
            toast.remove();
        });
    }, 4000);
}
