// Toggle the user profile box
const userBtn = document.querySelector("#user-btn");
if (userBtn) {  // Ensure the button exists
    userBtn.addEventListener("click", function () {
        const userBox = document.querySelector(".profile-detail");
        if (userBox) {
            userBox.classList.toggle("active");
        }
    });
}

// Toggle the sidebar visibility
const toggle = document.querySelector(".toggle-btn");
if (toggle) {  // Ensure the toggle button exists
    toggle.addEventListener("click", function () {
        const sidebar = document.querySelector(".sidebar");
        if (sidebar) {
            sidebar.classList.toggle("active");
        }
    });
}
