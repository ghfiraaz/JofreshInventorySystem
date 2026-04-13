document.addEventListener('DOMContentLoaded', () => {
    // CSRF Token for Laravel
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '';

    // --- Shared Modal Logic ---
    const modals = document.querySelectorAll('.modal-overlay');
    const closeBtns = document.querySelectorAll('[data-close-modal]');

    if (closeBtns.length > 0) {
        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetModal = btn.closest('.modal-overlay');
                if (targetModal) {
                    targetModal.classList.remove('active');
                    resetUserForm();
                }
            });
        });
    }

    modals.forEach(m => {
        m.addEventListener('click', (e) => {
            if (e.target === m) {
                m.classList.remove('active');
                resetUserForm();
            }
        });
    });


    // --- Users Page Logic (`users.blade.php`) ---
    const btnTambahPengguna = document.getElementById('btn-tambah-pengguna');
    const modalPengguna = document.getElementById('modal-tambah-pengguna');
    const formPengguna = document.getElementById('form-pengguna');
    const usersTableBody = document.querySelector('#usersTable tbody');

    // form fields
    const fId = document.getElementById('edit-row-id');
    const fName = document.getElementById('user-name');
    const fEmail = document.getElementById('user-email');
    const fPass = document.getElementById('user-password');
    const fRole = document.getElementById('user-role');
    const pwdHint = document.getElementById('password-hint');
    const modalTitle = document.getElementById('modal-title');
    const modalDesc = document.getElementById('modal-desc');
    const btnSubmit = document.getElementById('btn-submit-modal');

    function resetUserForm() {
        if (!formPengguna) return;
        formPengguna.reset();
        if (fId) fId.value = '';
        if (modalTitle) modalTitle.textContent = 'Tambah Pengguna Baru';
        if (modalDesc) modalDesc.textContent = 'Buat akun pengguna baru untuk sistem (Register).';
        if (btnSubmit) btnSubmit.textContent = 'Simpan Pengguna';
        if (fPass) fPass.required = true;
        if (pwdHint) pwdHint.textContent = '';
    }

    if (btnTambahPengguna && modalPengguna) {
        btnTambahPengguna.addEventListener('click', () => {
            resetUserForm();
            modalPengguna.classList.add('active');
        });
    }

    if (formPengguna) {
        formPengguna.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const payload = {
                name: fName.value,
                email: fEmail.value,
                password: fPass.value,
                role: fRole.value
            };

            const isEdit = fId.value !== '';
            const url = isEdit ? `/users/${fId.value}` : `/users`;
            const method = isEdit ? 'PUT' : 'POST';

            btnSubmit.disabled = true;
            btnSubmit.textContent = 'Menyimpan...';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => { throw err; });
                }
                return res.json();
            })
            .then(data => {
                // Success, reload page to sync with DB
                window.location.reload();
            })
            .catch(error => {
                console.error("Error saving user:", error);
                alert("Terjadi kesalahan saat menyimpan pengguna. Pastikan email unik & password minimal 8 huruf.");
                btnSubmit.disabled = false;
                btnSubmit.textContent = 'Simpan Pengguna';
            });
        });
    }

    // Event Delegation for Edit & Delete Buttons
    if (usersTableBody) {
        usersTableBody.addEventListener('click', (e) => {
            const btnDelete = e.target.closest('.btn-delete');
            const btnEdit = e.target.closest('.btn-edit');

            if (btnDelete) {
                if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
                    const tr = btnDelete.closest('tr');
                    const id = tr.getAttribute('data-id');
                    
                    fetch(`/users/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => {
                        if (res.ok) {
                            tr.remove(); // Only remove on success
                        } else {
                            alert("Gagal menghapus pengguna.");
                        }
                    });
                }
            }

            if (btnEdit) {
                const tr = btnEdit.closest('tr');
                const rowId = tr.getAttribute('data-id');
                const name = tr.querySelector('.row-name').textContent;
                const email = tr.querySelector('.row-email').textContent;
                const role = tr.querySelector('.row-role').textContent.trim();

                fId.value = rowId;
                fName.value = name;
                fEmail.value = email;
                fRole.value = role;
                fPass.value = ''; // Don't show existing password
                fPass.required = false; 
                pwdHint.textContent = '*Kosongkan jika tidak ingin mengubah password';

                modalTitle.textContent = 'Edit Pengguna';
                modalDesc.textContent = 'Perbarui informasi akun pengguna.';
                btnSubmit.textContent = 'Simpan Perubahan';

                modalPengguna.classList.add('active');
            }
        });
    }

    // --- Admin: Produk Page ---
    const btnTambahProduk = document.getElementById('btn-tambah-produk');
    const modalProduk = document.getElementById('modal-produk');
    const formProduk = document.getElementById('form-produk');
    const produkTbody = document.querySelector('#produkTable tbody');

    function formatRupiah(num) {
        return 'Rp ' + parseInt(num).toLocaleString('id-ID');
    }

    if (btnTambahProduk && modalProduk) {
        btnTambahProduk.addEventListener('click', () => {
            formProduk.reset();
            document.getElementById('produk-edit-id').value = '';
            document.getElementById('modal-produk-title').textContent = 'Tambah Produk';
            document.getElementById('btn-submit-produk').textContent = 'Simpan Produk';
            modalProduk.classList.add('active');
        });
    }

    if (formProduk) {
        formProduk.addEventListener('submit', (e) => {
            e.preventDefault();
            const editId = document.getElementById('produk-edit-id').value;
            const nama = document.getElementById('produk-nama').value;
            const kategori = document.getElementById('produk-kategori').value;
            const stok = parseInt(document.getElementById('produk-stok').value);
            const minimal = parseInt(document.getElementById('produk-minimal').value);
            const satuan = document.getElementById('produk-satuan').value;
            const harga = document.getElementById('produk-harga').value;
            const statusText = stok <= 0 ? 'Stok Habis' : stok < minimal ? 'Stok Rendah' : 'Tersedia';
            const statusClass = stok <= 0 ? 'badge-stok-habis' : stok < minimal ? 'badge-stok-rendah' : 'badge-tersedia';

            if (editId) {
                const tr = document.querySelector(`#produkTable tr[data-id="${editId}"]`);
                if (tr) {
                    tr.querySelector('.row-nama').textContent = nama;
                    tr.querySelector('.row-kategori').textContent = kategori;
                    tr.querySelector('.row-stok').textContent = stok;
                    tr.querySelector('.row-minimal').textContent = minimal;
                    tr.querySelector('.row-satuan').textContent = satuan;
                    tr.querySelector('.row-harga').textContent = formatRupiah(harga);
                    const badge = tr.querySelector('.badge');
                    badge.textContent = statusText;
                    badge.className = `badge ${statusClass}`;
                }
            } else {
                const uid = 'p-' + Date.now();
                const tr = document.createElement('tr');
                tr.setAttribute('data-id', uid);
                tr.innerHTML = `
                    <td class="fw-bold row-nama">${nama}</td>
                    <td class="row-kategori">${kategori}</td>
                    <td class="row-stok">${stok}</td>
                    <td class="row-minimal">${minimal}</td>
                    <td class="row-satuan">${satuan}</td>
                    <td class="row-harga">${formatRupiah(harga)}</td>
                    <td><span class="badge ${statusClass}">${statusText}</span></td>
                    <td>
                        <button class="btn-icon btn-edit-produk" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" /></svg></button>
                        <button class="btn-icon danger ms-2 btn-delete-produk" title="Hapus"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                    </td>`;
                produkTbody.appendChild(tr);
            }
            modalProduk.classList.remove('active');
        });
    }

    if (produkTbody) {
        produkTbody.addEventListener('click', (e) => {
            if (e.target.closest('.btn-delete-produk')) {
                if (confirm('Hapus produk ini?')) e.target.closest('tr').remove();
            }
            if (e.target.closest('.btn-edit-produk')) {
                const tr = e.target.closest('tr');
                document.getElementById('produk-edit-id').value = tr.getAttribute('data-id');
                document.getElementById('produk-nama').value = tr.querySelector('.row-nama').textContent;
                document.getElementById('produk-kategori').value = tr.querySelector('.row-kategori').textContent;
                document.getElementById('produk-stok').value = tr.querySelector('.row-stok').textContent;
                document.getElementById('produk-minimal').value = tr.querySelector('.row-minimal').textContent;
                document.getElementById('produk-satuan').value = tr.querySelector('.row-satuan').textContent;
                document.getElementById('produk-harga').value = tr.querySelector('.row-harga').textContent.replace(/[Rp.,\s]/g, '');
                document.getElementById('modal-produk-title').textContent = 'Edit Produk';
                document.getElementById('btn-submit-produk').textContent = 'Simpan Perubahan';
                modalProduk.classList.add('active');
            }
        });
    }

    // --- Admin: Stok Masuk Page ---
    const formStok = document.getElementById('form-stok-masuk');
    const stokTbody = document.getElementById('stok-tbody');
    if (formStok && stokTbody) {
        formStok.addEventListener('submit', (e) => {
            e.preventDefault();
            const produk = document.getElementById('stok-produk').value;
            const tipe = document.getElementById('stok-tipe').value;
            const jumlah = document.getElementById('stok-jumlah').value;
            const tanggal = document.getElementById('stok-tanggal').value;
            const ref = document.getElementById('stok-referensi').value || '-';
            const catatan = document.getElementById('stok-catatan').value || '-';
            const badgeClass = tipe === 'Stok Masuk' ? 'badge-stok-masuk' : 'badge-stok-keluar';
            const arrow = tipe === 'Stok Masuk' ? '↑' : '↓';
            const dateFormatted = tanggal ? new Date(tanggal).toLocaleDateString('id-ID') : '-';
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${dateFormatted}</td><td class="fw-bold">${produk}</td><td><span class="badge ${badgeClass}">${arrow} ${tipe}</span></td><td>${jumlah} kg</td><td>${ref}</td><td class="text-muted">${catatan}</td>`;
            stokTbody.prepend(tr);
            formStok.reset();
        });
    }

    // --- Admin: Mitra Page ---
    const btnTambahMitra = document.getElementById('btn-tambah-mitra');
    const modalMitra = document.getElementById('modal-mitra');
    const formMitra = document.getElementById('form-mitra');
    const mitraTbody = document.querySelector('#mitraTable tbody');

    if (btnTambahMitra && modalMitra) {
        btnTambahMitra.addEventListener('click', () => {
            formMitra.reset();
            document.getElementById('mitra-edit-id').value = '';
            document.getElementById('modal-mitra-title').textContent = 'Tambah Mitra';
            document.getElementById('btn-submit-mitra').textContent = 'Simpan Mitra';
            modalMitra.classList.add('active');
        });
    }

    if (formMitra && mitraTbody) {
        formMitra.addEventListener('submit', (e) => {
            e.preventDefault();
            const editId = document.getElementById('mitra-edit-id').value;
            const nama = document.getElementById('mitra-nama').value;
            const kontak = document.getElementById('mitra-kontak').value;
            const alamat = document.getElementById('mitra-alamat').value;
            const today = new Date().toLocaleDateString('id-ID');

            if (editId) {
                const tr = document.querySelector(`#mitraTable tr[data-id="${editId}"]`);
                if (tr) {
                    tr.querySelector('.row-nama-mitra').textContent = nama;
                    tr.querySelector('.row-kontak').textContent = kontak;
                    tr.querySelector('.row-alamat').textContent = alamat;
                }
            } else {
                const uid = 'm-' + Date.now();
                const tr = document.createElement('tr');
                tr.setAttribute('data-id', uid);
                tr.innerHTML = `
                    <td class="fw-bold row-nama-mitra">${nama}</td>
                    <td class="row-kontak">${kontak}</td>
                    <td class="row-alamat">${alamat}</td>
                    <td><span class="badge badge-aktif row-status-mitra">Aktif</span></td>
                    <td class="row-terdaftar">${today}</td>
                    <td>
                        <button class="btn-icon btn-edit-mitra" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" /></svg></button>
                        <button class="btn-icon danger ms-2 btn-delete-mitra" title="Hapus"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                    </td>`;
                mitraTbody.appendChild(tr);
            }
            modalMitra.classList.remove('active');
        });

        mitraTbody.addEventListener('click', (e) => {
            if (e.target.closest('.btn-delete-mitra')) {
                if (confirm('Hapus mitra ini?')) e.target.closest('tr').remove();
            }
            if (e.target.closest('.btn-edit-mitra')) {
                const tr = e.target.closest('tr');
                document.getElementById('mitra-edit-id').value = tr.getAttribute('data-id');
                document.getElementById('mitra-nama').value = tr.querySelector('.row-nama-mitra').textContent;
                document.getElementById('mitra-kontak').value = tr.querySelector('.row-kontak').textContent;
                document.getElementById('mitra-alamat').value = tr.querySelector('.row-alamat').textContent;
                document.getElementById('modal-mitra-title').textContent = 'Edit Mitra';
                document.getElementById('btn-submit-mitra').textContent = 'Simpan Perubahan';
                modalMitra.classList.add('active');
            }
        });
    }

    // --- Chart.js init on Dashboard ---
    const ctxTrend = document.getElementById('chartTrend');
    if (ctxTrend) {
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: ['07 Apr', '08 Apr', '09 Apr', '10 Apr', '11 Apr', '12 Apr', '13 Apr'],
                datasets: [{
                    label: 'Penjualan',
                    data: [0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#ccc',
                    fill: false,
                    tension: 0.1,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#ccc'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 4, ticks: { stepSize: 1 } } }
            }
        });
    }

    const ctxDist = document.getElementById('chartDist');
    if (ctxDist) {
        new Chart(ctxDist, {
            type: 'bar',
            data: {
                labels: ['Ayam Broiler', 'Ayam Kampung', 'Bebek'],
                datasets: [{
                    label: 'Penjualan',
                    data: [0, 0, 0],
                    backgroundColor: ['#8c6a5e', '#b89d91', '#d4c5bd']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }
                },
                scales: { y: { beginAtZero: true, max: 4, ticks: { stepSize: 1 } } }
            }
        });
    }
});

