// ===== SayHi! user-page logic (Bootstrap + SweetAlert2 + loading topbar) =====
(function () {
    const form = document.getElementById('data-form');
    const submitBtn = document.getElementById('submitBtn');
    const thanks = document.getElementById('thanks');

    const cDosen = document.getElementById('count-dosen');
    const cMhs = document.getElementById('count-mahasiswa');
    const cUmum = document.getElementById('count-umum');
    const tgl = document.getElementById('tanggal');

    const TOPBAR = document.getElementById('topbar');

    if (!form) return;

    // Topbar helpers
    function topbarStart() { TOPBAR.style.width = '35%'; requestAnimationFrame(() => TOPBAR.style.width = '85%'); }
    function topbarDone() { TOPBAR.style.width = '100%'; setTimeout(() => TOPBAR.style.width = '0%', 250); }

    // Toast helper
    function toast(icon, title) {
        Swal.fire({ toast: true, position: 'top-end', icon, title, timer: 1600, showConfirmButton: false });
    }

    // Default tanggal: hari ini
    const today = new Date().toISOString().split('T')[0];
    tgl.value = today;

    // Ambil counter
    async function loadCounts() {
        try {
            const res = await fetch("/sayhi/api/dashboard/counts.php");
            const data = await res.json();
            if (data.status === "success") {
                cDosen.textContent = data.data.Dosen ?? 0;
                cMhs.textContent = data.data.Mahasiswa ?? 0;
                cUmum.textContent = data.data.Umum ?? 0;
            }
        } catch (err) {
            // diamkan saja agar halaman tetap ringan
            console.error("Gagal load count:", err);
        }
    }

    // Realtime tiap 2 detik
    loadCounts();
    setInterval(loadCounts, 2000);

    // Submit
    let lock = false;
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        if (lock) return;
        lock = true;

        // validasi ringan
        const fullname = document.getElementById("fullname").value.trim();
        const instansi = document.getElementById("instansi").value;
        const phone = document.getElementById("phone").value.trim();
        const email = document.getElementById("email").value.trim();
        const tanggal = tgl.value;

        if (!fullname || !instansi || !phone) {
            toast('error', 'Mohon lengkapi semua form!');
            lock = false; return;
        }
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            toast('error', 'Email tidak valid');
            lock = false; return;
        }
        if (!/^[0-9+\-\s]{6,20}$/.test(phone)) {
            toast('error', 'Nomor HP tidak valid');
            lock = false; return;
        }

        const id_status = instansi === "Mahasiswa" ? 1 : (instansi === "Dosen" ? 2 : 3);

        const fd = new FormData();
        fd.append("nama", fullname);
        fd.append("instansi", instansi);
        fd.append("no_hp", phone);
        fd.append("email", email);
        fd.append("id_status", id_status);
        fd.append("tanggal", tanggal);

        // UX: loading topbar + disable tombol
        topbarStart();
        submitBtn.disabled = true;

        try {
            const res = await fetch("/sayhi/api/tamu/add.php", { method: "POST", body: fd });
            const out = await res.json();
            if (out.status === "success") {
                toast('success', 'Data tersimpan');
                form.reset();
                tgl.value = today;
                thanks.classList.remove('d-none');
                loadCounts();
            } else {
                toast('error', out.message || 'Gagal menyimpan');
            }
        } catch (e) {
            toast('error', 'Server error');
            console.error(e);
        } finally {
            submitBtn.disabled = false;
            topbarDone();
            setTimeout(() => { thanks.classList.add('d-none'); }, 2500);
            setTimeout(() => { lock = false; }, 2000); // anti double submit
        }
    });
})();
