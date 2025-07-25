function showLinks(){
    const link = document.getElementById("nav-links");
    const burger = document.getElementById("hamburger");
    link.classList.toggle("show");
    burger.classList.toggle("active");
}

const dropArea = document.getElementById("drop-area");
  const fileInput = document.getElementById("fileInput");
  const fileBtn = document.getElementById("fileBtn");
  const fileList = document.getElementById("file-list");

  // Klik tombol -> buka file explorer
  fileBtn.addEventListener("click", () => fileInput.click());

  // Pilih file secara manual
  fileInput.addEventListener("change", (e) => {
    handleFiles(e.target.files);
  });

  // Highlight area saat drag
  ["dragenter", "dragover"].forEach(event => {
    dropArea.addEventListener(event, (e) => {
      e.preventDefault();
      dropArea.classList.add("hover");
    });
  });

  // Hilangkan highlight saat keluar/drop
  ["dragleave", "drop"].forEach(event => {
    dropArea.addEventListener(event, (e) => {
      e.preventDefault();
      dropArea.classList.remove("hover");
    });
  });

  // Drop file
  dropArea.addEventListener("drop", (e) => {
    handleFiles(e.dataTransfer.files);
  });

  function handleFiles(files) {
    fileList.innerHTML = ""; // Kosongkan dulu

    for (let file of files) {
      const fileName = file.name;
      const fileSize = (file.size / 1024).toFixed(2) + " KB";

      const item = document.createElement("div");
      item.className = "file-item";
      item.textContent = `📄 ${fileName} (${fileSize})`;

      fileList.appendChild(item);
    }
  }