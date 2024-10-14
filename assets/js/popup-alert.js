const ALERT_DURATION_MS = 2000; // Duration in milliseconds

function showPopupAlert(message, type = 'info') {
    // Remove any existing alerts
    const existingAlert = document.querySelector('.popup-alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    // Create the alert element
    const alert = document.createElement('div');
    alert.className = `popup-alert fixed left-1/2 transform -translate-x-1/2 px-4 py-2 rounded-lg text-white text-sm font-semibold shadow-lg transition-all duration-300`;
    alert.style.zIndex = '9999';
    alert.style.bottom = '-100px'; // Start below the viewport

    // Set background color based on type
    switch (type) {
        case 'success':
            alert.classList.add('bg-green-500');
            break;
        case 'error':
            alert.classList.add('bg-red-500');
            break;
        default:
            alert.classList.add('bg-purple-500');
    }

    alert.textContent = message;

    // Add the alert to the DOM
    document.body.appendChild(alert);

    // Pop up animation
    setTimeout(() => {
        alert.style.bottom = '20px'; // Move to final position
    }, 10);

    // Pop down and remove after ALERT_DURATION_MS
    setTimeout(() => {
        alert.style.bottom = '-100px'; // Move back below the viewport
        setTimeout(() => {
            alert.remove();
        }, 300);
    }, ALERT_DURATION_MS);
}