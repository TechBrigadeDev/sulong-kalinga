function toggleDropdown(element) {
    const dropdownParent = element.closest("li");
    dropdownParent.classList.toggle("showMenu");
  }

let sidebar = document.querySelector(".sidebar");
let logoToggle = document.getElementById("logoToggle");
logoToggle.addEventListener("click", () => {
  sidebar.classList.toggle("close");
});

// document.addEventListener("DOMContentLoaded", function () {
//   const sidebar = document.querySelector(".sidebar");
//   const toggleButton = document.querySelector("#toggleSidebarButton");

//   toggleButton.addEventListener("click", function () {
//       if (sidebar.classList.contains("hidden")) {
//           sidebar.classList.remove("hidden");
//           sidebar.classList.add("visible");
//       } else {
//           sidebar.classList.remove("visible");
//           sidebar.classList.add("hidden");
//       }
//   });
// });