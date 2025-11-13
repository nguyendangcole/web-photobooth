// KHÔNG khai báo lại const video
const filterOptions = document.querySelectorAll(".filter-option");
const cameraFrame = document.querySelector(".camera-frame");

filterOptions.forEach(option => {
  option.addEventListener("click", (e) => {
    e.preventDefault();

    // Xóa tất cả preset class cũ
    cameraFrame.classList.remove("preset1", "preset2", "preset3", "preset4", "preset5");

    const filter = option.getAttribute("data-filter");

    if (filter.startsWith("preset")) {
      // preset → add class
      cameraFrame.classList.add(filter);
      video.style.filter = "none"; // reset filter mặc định
    } else {
      // filter cơ bản
      video.style.filter = filter;
    }
  });
});
