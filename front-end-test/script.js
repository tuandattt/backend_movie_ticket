document.addEventListener('DOMContentLoaded', function () {
    function showNotification(message) {
        const notificationBar = document.querySelector('.notification-bar');
        const notificationText = notificationBar.querySelector('.notification-text');

        notificationText.textContent = message;
        notificationBar.classList.add('show');

        setTimeout(() => {
            notificationBar.classList.remove('show');
        }, 3000);
    }

    function addEventListeners() {
        
    }

    function loadHTML(elementId, url) {
        fetch(url)
            .then(response => response.text())
            .then(data => {
                document.getElementById(elementId).innerHTML = data;
                addEventListeners();
            })
            .catch(error => console.error('Error loading file:', error));
    }

    loadHTML("header", "header.html");
    loadHTML("content", "homepage.html");
    loadHTML("footer", "footer.html");
});

function loadModal(fileName, containerId) {
    fetch(fileName)
        .then(response => response.text())
        .then(data => {
            document.getElementById(containerId).insertAdjacentHTML("beforeend", data);
        })
        .catch(error => console.error('Error loading modal:', error));
}

loadModal('login.html', 'modalContainer');
loadModal('register.html', 'modalContainer');
loadModal('forgot.html', 'modalContainer');

function openModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "block";
    }
}

function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "none";
    }
}

window.onclick = function(event) {
    var loginModal = document.getElementById('loginModal');
    var registerModal = document.getElementById('registerModal');
    var forgotModal = document.getElementById('forgotModal');
    if (event.target === loginModal || event.target === registerModal || event.target === forgotModal) {
        loginModal.style.display = "none";
        registerModal.style.display = "none";
        forgotModal.style.display = "none";
    }
};
