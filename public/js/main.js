function showLinks(){
    const link = document.getElementById("nav-links");
    const burger = document.getElementById("hamburger");
    link.classList.toggle("show");
    burger.classList.toggle("active");
}

document.addEventListener("DOMContentLoaded", () => {
    const areas = [
        {
            drop: document.getElementById("drop-area-dokumen"),
            input: document.getElementById("fileInputDokumen"),
            button: document.getElementById("fileBtnDokumen"),
            list: document.getElementById("fileListDokumen"),
        },
        {
            drop: document.getElementById("drop-area-bukti"),
            input: document.getElementById("fileInputBukti"),
            button: document.getElementById("fileBtnBukti"),
            list: document.getElementById("fileListBukti"),
        },
    ];

    // Hindari browser buka file saat di-drop ke luar
    ["dragover", "drop"].forEach(evt =>
        window.addEventListener(evt, e => {
            if (!e.target.closest(".drop-area")) e.preventDefault();
        })
    );

    areas.forEach(({ drop, input, button, list }) => {
        if (!drop || !input || !button || !list) {
            console.error("❌ Elemen tidak lengkap.");
            return;
        }

        // Buka file picker saat tombol diklik
        button.addEventListener("click", () => input.click());

        // Saat file dipilih manual
        input.addEventListener("change", () => {
            tampilkanFile(input.files, list);
        });

        // Highlight saat file di-drag
        ["dragenter", "dragover"].forEach(event =>
            drop.addEventListener(event, e => {
                e.preventDefault();
                drop.classList.add("highlight");
            })
        );

        // Hilangkan highlight saat keluar/drop
        ["dragleave", "drop"].forEach(event =>
            drop.addEventListener(event, e => {
                e.preventDefault();
                drop.classList.remove("highlight");
            })
        );

        // Saat file di-drop
        drop.addEventListener("drop", e => {
            const files = e.dataTransfer.files;
            if (!files || files.length === 0) {
                alert("⚠️ Tidak ada file yang dideteksi.");
                return;
            }

            const dt = new DataTransfer();
            for (let i = 0; i < files.length; i++) {
                dt.items.add(files[i]);
            }

            input.files = dt.files;
            tampilkanFile(dt.files, list);
        });
    });

    function tampilkanFile(files, container) {
        container.innerHTML = "";
        for (const file of files) {
            const p = document.createElement("p");
            p.textContent = `📄 ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
            container.appendChild(p);
        }
    }
});
