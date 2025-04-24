document.addEventListener("DOMContentLoaded", function () {
    const datePicker = document.querySelector("#dob");
    if (datePicker) {
        const today = new Date().toISOString().split('T')[0];
        datePicker.setAttribute('max', today);
    }
});
const toggleBtn = document.getElementById("toggle-btn");
const sidebar = document.querySelector(".sidebar");

toggleBtn.addEventListener("click", () => {
  if (window.innerWidth <= 768) {
    sidebar.classList.toggle("active");
  }
});

sidebar.addEventListener("click", () => {
  if (window.innerWidth <= 768) {
    sidebar.classList.remove("active");
  }
});