document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll(".navbar-nav .nav-link");

    navLinks.forEach(link => {
        link.addEventListener("click", function () {
            // Hapus class active-link dari semua link
            navLinks.forEach(l => l.classList.remove("active-link"));

            // Tambahkan class active-link ke yang diklik
            this.classList.add("active-link");
        });
    });

    // Inisialisasi Dropzone
    Dropzone.options.propertyDropzone = {
        url: "/upload-images", // Sesuaikan dengan route Laravel kamu
        paramName: "gambar[]",
        maxFiles: 4,
        acceptedFiles: "image/*",
        addRemoveLinks: true,
        dictDefaultMessage: "Drag & drop gambar di sini atau klik untuk upload (Maksimal 4 gambar)",
        dictRemoveFile: "Hapus",
        init: function () {
            this.on("maxfilesexceeded", function (file) {
                this.removeFile(file);
                alert("Maksimal upload hanya 4 gambar.");
            });

            this.on("success", function (file, response) {
                console.log("Gambar berhasil diupload:", response.images);
            });

            this.on("error", function (file, errorMessage) {
                console.error("Gagal upload:", errorMessage);
            });
        }
    };
});
