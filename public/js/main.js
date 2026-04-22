document.addEventListener('DOMContentLoaded', () => {
    // CSRF Token for Laravel
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '';

    // -------------------------------------------------------
    // Shared Helpers
    // -------------------------------------------------------
    function formatRupiah(num) {
        return 'Rp ' + parseInt(num).toLocaleString('id-ID');
    }

    const EDIT_SVG   = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/></svg>`;
    const DELETE_SVG = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>`;

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-8 right-8 px-6 py-4 rounded-xl shadow-2xl z-[100] transform transition-all duration-500 translate-y-20 opacity-0 flex items-center gap-3 font-medium ${
            type === 'success' ? 'bg-blue-900 text-white' : 'bg-red-600 text-white'
        }`;
        
        const icon = type === 'success' 
            ? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>'
            : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>';

        toast.innerHTML = `${icon} <span>${message}</span>`;
        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-y-20', 'opacity-0');
        }, 10);

        // Remove after 3s
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }

    let confirmCallback = null;
    const modalConfirm = document.getElementById('modal-confirm');
    const btnConfirmYes = document.getElementById('btn-confirm-yes');
    const btnConfirmNo  = document.getElementById('btn-confirm-no');

    if (btnConfirmYes) {
        btnConfirmYes.addEventListener('click', () => {
            if (confirmCallback) confirmCallback();
            modalConfirm.classList.remove('active');
            modalConfirm.querySelector('div')?.classList.remove('active');
            confirmCallback = null;
        });
    }
    if (btnConfirmNo) {
        btnConfirmNo.addEventListener('click', () => {
            modalConfirm.classList.remove('active');
            modalConfirm.querySelector('div')?.classList.remove('active');
            confirmCallback = null;
        });
    }

    function showConfirm(title, message, onYes) {
        if (!modalConfirm) return;
        const titleEl   = document.getElementById('confirm-title');
        const messageEl = document.getElementById('confirm-message');
        const childEl   = modalConfirm.querySelector('div');
        if (title)   titleEl.textContent   = title;
        if (message) messageEl.textContent = message;
        confirmCallback = onYes;
        modalConfirm.classList.add('active');
        if (childEl) childEl.classList.add('active');
    }

    function buildUserRow(u) {
        const tr = document.createElement('tr');
        tr.setAttribute('data-id', u.id);
        const date = u.created_at ? new Date(u.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }) : new Date().toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const roleClass = u.role === 'Admin' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700';
        
        tr.innerHTML = `
            <td class="font-bold row-name">${u.name}</td>
            <td class="row-email">${u.email}</td>
            <td>
                <span class="px-3 py-1.5 rounded-full text-xs font-semibold ${roleClass} row-role">
                    ${u.role}
                </span>
            </td>
            <td class="row-date">${date}</td>
            <td>
                <button class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer border-none bg-transparent btn-edit" title="Edit">${EDIT_SVG}</button>
                <button class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors cursor-pointer border-none bg-transparent ml-2 btn-delete" title="Hapus">${DELETE_SVG}</button>
            </td>`;
        return tr;
    }

    // -------------------------------------------------------
    // Sidebar Toggle Logic
    // -------------------------------------------------------
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    if (sidebarToggle && sidebar) {
        // Initial state for sidebar transition speed is handled in tailwind
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-collapsed');
        });
    }

    // -------------------------------------------------------
    // Shared Modal Logic (close on button / backdrop click)
    // -------------------------------------------------------
    document.querySelectorAll('[data-close-modal]').forEach(btn => {
        btn.addEventListener('click', () => btn.closest('.modal-overlay')?.classList.remove('active'));
    });
    document.querySelectorAll('.modal-overlay').forEach(m => {
        m.addEventListener('click', (e) => { if (e.target === m) m.classList.remove('active'); });
    });

    // -------------------------------------------------------
    // Users Page (DB-backed)
    // -------------------------------------------------------
    const btnTambahPengguna = document.getElementById('btn-tambah-pengguna');
    const modalPengguna     = document.getElementById('modal-tambah-pengguna');
    const formPengguna      = document.getElementById('form-pengguna');
    const usersTableBody    = document.querySelector('#usersTable tbody');
    const fId      = document.getElementById('edit-row-id');
    const fName    = document.getElementById('user-name');
    const fEmail   = document.getElementById('user-email');
    const fPass    = document.getElementById('user-password');
    const fRole    = document.getElementById('user-role');
    const pwdHint  = document.getElementById('password-hint');
    const modalTitle = document.getElementById('modal-title');
    const modalDesc  = document.getElementById('modal-desc');
    const btnSubmit  = document.getElementById('btn-submit-modal');

    function resetUserForm() {
        if (!formPengguna) return;
        formPengguna.reset();
        if (fId) fId.value = '';
        if (modalTitle) modalTitle.textContent = 'Tambah Pengguna Baru';
        if (modalDesc) modalDesc.textContent = 'Buat akun pengguna baru untuk sistem (Register).';
        if (btnSubmit) btnSubmit.textContent = 'Simpan Pengguna';
        if (fPass) fPass.required = true;
        if (pwdHint) pwdHint.textContent = '';
        
        // Ensure fields are editable for new users
        if (fName)  fName.disabled = false;
        if (fEmail) fEmail.disabled = false;
        if (fRole)  fRole.disabled = false;
    }

    if (btnTambahPengguna && modalPengguna) {
        btnTambahPengguna.addEventListener('click', () => { resetUserForm(); modalPengguna.classList.add('active'); });
    }

    if (formPengguna) {
        formPengguna.addEventListener('submit', (e) => {
            e.preventDefault();
            const isEdit = fId.value !== '';
            const url    = isEdit ? `/users/${fId.value}` : '/users';
            const method = isEdit ? 'PUT' : 'POST';
            btnSubmit.disabled = true; btnSubmit.textContent = 'Menyimpan...';
            fetch(url, {
                method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ name: fName.value, email: fEmail.value, password: fPass.value, role: fRole.value })
            })
            .then(r => r.ok ? r.json() : r.json().then(e => { throw e; }))
            .then(data => {
                if (isEdit) {
                    const old = document.querySelector(`#usersTable tr[data-id="${fId.value}"]`);
                    if (old) old.replaceWith(buildUserRow(data.user));
                    showToast('Data pengguna berhasil di perbaharui');
                } else {
                    const empty = usersTableBody.querySelector('td[colspan]');
                    if (empty) empty.closest('tr').remove();
                    usersTableBody.prepend(buildUserRow(data.user));
                    showToast('Pengguna berhasil di tambahkan');
                }
                modalPengguna.classList.remove('active');
                btnSubmit.disabled = false; btnSubmit.textContent = 'Simpan Pengguna';
            })
            .catch(() => { alert("Gagal menyimpan pengguna. Pastikan email unik & password minimal 8 huruf."); btnSubmit.disabled = false; btnSubmit.textContent = 'Simpan Pengguna'; });
        });
    }

    document.addEventListener('click', (e) => {
        // Delete User
        const btnDelete = e.target.closest('.btn-delete');
        if (btnDelete && document.getElementById('usersTable')?.contains(btnDelete)) {
            const tr = btnDelete.closest('tr');
            showConfirm('Hapus Pengguna', 'Apakah Anda yakin ingin menghapus data pengguna ini?', () => {
                fetch(`/users/${tr.getAttribute('data-id')}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
                .then(r => { 
                    if (r.ok) {
                        tr.remove();
                        showToast('data pengguna berhasil di hapus');
                    } else {
                        alert("Gagal menghapus pengguna."); 
                    }
                });
            });
        }

        // Edit User
        const btnEdit = e.target.closest('.btn-edit');
        if (btnEdit && document.getElementById('usersTable')?.contains(btnEdit)) {
            const tr = btnEdit.closest('tr');
            fId.value = tr.getAttribute('data-id');
            fName.value  = tr.querySelector('.row-name').textContent;
            fEmail.value = tr.querySelector('.row-email').textContent;
            fRole.value  = tr.querySelector('.row-role').textContent.trim();
            fPass.value  = ''; fPass.required = false;
            pwdHint.textContent = '*Kosongkan jika tidak ingin mengubah password';
            modalTitle.textContent = 'Edit Kata Sandi Pengguna';
            modalDesc.textContent  = 'Nama, Email, dan Role tidak dapat diubah di form ini.';
            btnSubmit.textContent  = 'Simpan Perubahan';
            
            fName.disabled = true;
            fEmail.disabled = true;
            fRole.disabled = true;
            
            modalPengguna.classList.add('active');
        }
    });

    // -------------------------------------------------------
    // Admin: Produk Page (DB-backed via Fetch API)
    // -------------------------------------------------------
    const btnTambahProduk = document.getElementById('btn-tambah-produk');
    const modalProduk     = document.getElementById('modal-produk');
    const formProduk      = document.getElementById('form-produk');
    const produkTbody     = document.querySelector('#produkTable tbody');

    function produkStatusInfo(stok, minimal) {
        stok = parseFloat(stok); minimal = parseFloat(minimal);
        if (stok <= 0)        return { text: 'Stok Habis',  cls: 'badge-stok-habis' };
        if (stok < minimal)   return { text: 'Stok Rendah', cls: 'badge-stok-rendah' };
        return                       { text: 'Tersedia',    cls: 'badge-tersedia' };
    }


    function buildProdukRow(p) {
        const st     = produkStatusInfo(p.stok, p.stok_minimal);
        const harga  = p.harga_format || formatRupiah(p.harga);
        const status = p.status || st.text;
        const badge  = p.status_badge || st.cls;
        const tr = document.createElement('tr');
        tr.setAttribute('data-id', p.id);
        tr.innerHTML = `
            <td class="fw-bold row-nama">${p.nama}</td>
            <td class="row-stok">${p.stok}</td>
            <td class="row-minimal">${p.stok_minimal}</td>
            <td class="row-harga">${harga}</td>
            <td><span class="badge ${badge}">${status}</span></td>
            <td>
                <button class="bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-800 px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors cursor-pointer border border-blue-200 btn-tambah-stok">
                    + Tambah Stok
                </button>
            </td>`;
        return tr;
    }

    if (btnTambahProduk && modalProduk) {
        btnTambahProduk.addEventListener('click', () => {
            formProduk.reset();
            document.getElementById('produk-edit-id').value = '';
            document.getElementById('modal-produk-title').textContent = 'Tambah Produk';
            document.getElementById('modal-produk-desc').textContent  = 'Tambahkan produk baru ke inventaris.';
            document.getElementById('btn-submit-produk').textContent  = 'Simpan Produk';
            modalProduk.classList.add('active');
        });
    }

    if (formProduk) {
        formProduk.addEventListener('submit', (e) => {
            e.preventDefault();
            const editId  = document.getElementById('produk-edit-id').value;
            const payload = {
                nama:         document.getElementById('produk-nama').value,
                kategori:     'Unggas',
                stok:         editId ? undefined : 0, // Only set initially
                stok_minimal: document.getElementById('produk-minimal').value,
                satuan:       'Ekor',
                harga:        document.getElementById('produk-harga').value,
            };
            const url = editId ? `/admin/produk/${editId}` : '/admin/produk';
            const btn = document.getElementById('btn-submit-produk');
            btn.disabled = true; btn.textContent = 'Menyimpan...';

            fetch(url, { method: editId ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify(payload) })
            .then(r => r.ok ? r.json() : r.json().then(e => { throw e; }))
            .then(data => {
                if (editId) {
                    const old = document.querySelector(`#produkTable tr[data-id="${editId}"]`);
                    if (old) old.replaceWith(buildProdukRow(data.produk));
                    showToast('Produk berhasil diubah');
                } else {
                    const empty = produkTbody?.querySelector('td[colspan]');
                    if (empty) empty.closest('tr').remove();
                    produkTbody?.prepend(buildProdukRow(data.produk));
                    showToast('Produk berhasil ditambahkan');
                }
                modalProduk.classList.remove('active');
                btn.disabled = false; btn.textContent = 'Simpan Produk';
            })
            .catch(() => { alert('Gagal menyimpan produk. Cek input Anda.'); btn.disabled = false; btn.textContent = 'Simpan Produk'; });
        });
    }

    // -------------------------------------------------------
    // Admin: Tambah Stok
    // -------------------------------------------------------
    const btnSubmitStok = document.getElementById('btn-submit-stok');
    const formTambahStok = document.getElementById('form-tambah-stok');
    const modalTambahStok = document.getElementById('modal-tambah-stok');

    if (formTambahStok) {
        formTambahStok.addEventListener('submit', (e) => {
            e.preventDefault();
            const id = document.getElementById('stok-produk-id').value;
            const jumlah = document.getElementById('stok-jumlah-input').value;
            
            if (!id || !jumlah) return;

            btnSubmitStok.disabled = true;
            btnSubmitStok.textContent = 'Menyimpan...';

            fetch(`/admin/produk/${id}/stok`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': csrfToken, 
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({ jumlah: jumlah })
            })
            .then(r => r.ok ? r.json() : r.json().then(e => { throw e; }))
            .then(data => {
                const oldRow = document.querySelector(`#produkTable tr[data-id="${id}"]`);
                if (oldRow) {
                    oldRow.replaceWith(buildProdukRow(data.produk));
                    // re-attach local listener for the new + tambah stok button because of replaceWith
                    // We can just rely on event delegation which is better! We'll just define logic in the listener below.
                }
                modalTambahStok.classList.remove('active');
                formTambahStok.reset();
                showToast(data.message || 'Stok berhasil ditambahkan');
            })
            .catch(err => {
                showToast(err.message || 'Gagal menambahkan stok', 'error');
            })
            .finally(() => {
                btnSubmitStok.disabled = false;
                btnSubmitStok.textContent = 'Simpan Stok';
            });
        });
    }

    produkTbody?.addEventListener('click', (e) => {
        const btnDelete = e.target.closest('.btn-delete-produk');
        const btnEdit = e.target.closest('.btn-edit-produk');
        const btnStok = e.target.closest('.btn-tambah-stok');

        if (btnDelete) {
            if (confirm('Hapus produk ini dari database?')) {
                const tr = btnDelete.closest('tr');
                fetch(`/admin/produk/${tr.getAttribute('data-id')}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
                .then(r => { if (r.ok) tr.remove(); });
            }
        }
        
        if (btnEdit) {
            const tr = btnEdit.closest('tr');
            document.getElementById('produk-edit-id').value   = tr.getAttribute('data-id');
            document.getElementById('produk-nama').value      = tr.querySelector('.row-nama').textContent;
            document.getElementById('produk-minimal').value   = tr.querySelector('.row-minimal').textContent;
            document.getElementById('produk-harga').value     = tr.querySelector('.row-harga').textContent.replace(/[Rp\s.]/g, '').replace(',','.');
            document.getElementById('modal-produk-title').textContent = 'Edit Produk';
            document.getElementById('modal-produk-desc').textContent  = 'Perbarui informasi produk.';
            document.getElementById('btn-submit-produk').textContent  = 'Simpan Perubahan';
            modalProduk.classList.add('active');
        }

        if (btnStok && modalTambahStok) {
            const tr = btnStok.closest('tr');
            const nama = tr.querySelector('.row-nama').textContent;
            document.getElementById('stok-produk-id').value = tr.getAttribute('data-id');
            document.getElementById('stok-nama-produk').textContent = nama;
            modalTambahStok.classList.add('active');
        }
    });

    // -------------------------------------------------------
    // Admin: Mitra Page (DB-backed via Fetch API)
    // -------------------------------------------------------
    const btnTambahMitra = document.getElementById('btn-tambah-mitra');
    const modalMitra     = document.getElementById('modal-mitra');
    const formMitra      = document.getElementById('form-mitra');
    const mitraTbody     = document.querySelector('#mitraTable tbody');

    function buildMitraRow(m) {
        const terdaftar = m.created_at ? new Date(m.created_at).toLocaleDateString('id-ID') : new Date().toLocaleDateString('id-ID');
        const tr = document.createElement('tr');
        tr.setAttribute('data-id', m.id);
        tr.innerHTML = `
            <td class="fw-bold row-nama-mitra">${m.nama}</td>
            <td class="row-kontak">${m.kontak || '-'}</td>
            <td class="row-alamat">${m.alamat || '-'}</td>
            <td><span class="badge badge-aktif">${m.status || 'Aktif'}</span></td>
            <td>${terdaftar}</td>
            <td>
                <button class="btn-icon btn-edit-mitra" title="Edit">${EDIT_SVG}</button>
                <button class="btn-icon danger ms-2 btn-delete-mitra" title="Hapus">${DELETE_SVG}</button>
            </td>`;
        return tr;
    }

    if (btnTambahMitra && modalMitra) {
        btnTambahMitra.addEventListener('click', () => {
            formMitra.reset();
            document.getElementById('mitra-edit-id').value = '';
            document.getElementById('modal-mitra-title').textContent = 'Tambah Mitra';
            document.getElementById('btn-submit-mitra').textContent  = 'Simpan Mitra';
            modalMitra.classList.add('active');
        });
    }

    if (formMitra && mitraTbody) {
        formMitra.addEventListener('submit', (e) => {
            e.preventDefault();
            const editId  = document.getElementById('mitra-edit-id').value;
            const payload = {
                nama:   document.getElementById('mitra-nama').value,
                kontak: document.getElementById('mitra-kontak').value,
                alamat: document.getElementById('mitra-alamat').value,
            };
            const url = editId ? `/admin/mitra/${editId}` : '/admin/mitra';
            const btn = document.getElementById('btn-submit-mitra');
            btn.disabled = true; btn.textContent = 'Menyimpan...';

            fetch(url, { method: editId ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify(payload) })
            .then(r => r.ok ? r.json() : r.json().then(e => { throw e; }))
            .then(data => {
                if (editId) {
                    const old = document.querySelector(`#mitraTable tr[data-id="${editId}"]`);
                    if (old) old.replaceWith(buildMitraRow(data.mitra));
                    showToast('Mitra berhasil diubah');
                } else {
                    const empty = mitraTbody.querySelector('td[colspan]');
                    if (empty) empty.closest('tr').remove();
                    mitraTbody.prepend(buildMitraRow(data.mitra));
                    showToast('Mitra berhasil ditambahkan');
                }
                modalMitra.classList.remove('active');
                btn.disabled = false; btn.textContent = 'Simpan Mitra';
            })
            .catch(() => { alert('Gagal menyimpan mitra.'); btn.disabled = false; btn.textContent = 'Simpan Mitra'; });
        });

        mitraTbody.addEventListener('click', (e) => {
            if (e.target.closest('.btn-delete-mitra')) {
                if (confirm('Hapus mitra ini dari database?')) {
                    const tr = e.target.closest('tr');
                    fetch(`/admin/mitra/${tr.getAttribute('data-id')}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
                    .then(r => { if (r.ok) tr.remove(); });
                }
            }
            if (e.target.closest('.btn-edit-mitra')) {
                const tr = e.target.closest('tr');
                const kontak = tr.querySelector('.row-kontak').textContent.trim();
                const alamat = tr.querySelector('.row-alamat').textContent.trim();
                document.getElementById('mitra-edit-id').value = tr.getAttribute('data-id');
                document.getElementById('mitra-nama').value    = tr.querySelector('.row-nama-mitra').textContent;
                document.getElementById('mitra-kontak').value  = kontak === '-' ? '' : kontak;
                document.getElementById('mitra-alamat').value  = alamat === '-' ? '' : alamat;
                document.getElementById('modal-mitra-title').textContent = 'Edit Mitra';
                document.getElementById('btn-submit-mitra').textContent  = 'Simpan Perubahan';
                modalMitra.classList.add('active');
            }
        });
    }

    // -------------------------------------------------------
    // Dashboard Charts (Chart.js)
    // -------------------------------------------------------
    const dash = window._dashboardData || {};
    
    const ctxTrend = document.getElementById('chartTrend');
    if (ctxTrend) {
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: dash.trendLabels || ['07 Apr', '08 Apr', '09 Apr', '10 Apr', '11 Apr', '12 Apr', '13 Apr'],
                datasets: [{ 
                    label: 'Penjualan (Rp)', 
                    data: dash.trendData || [0,0,0,0,0,0,0], 
                    borderColor: '#1e3a8a', 
                    backgroundColor: 'rgba(30, 58, 138, 0.08)',
                    fill: true, 
                    tension: 0.4, 
                    pointBackgroundColor: '#1e3a8a', 
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { legend: { display: false } }, 
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        ticks: { stepSize: 1 } 
                    } 
                } 
            }
        });
    }

    const ctxDist = document.getElementById('chartDist');
    if (ctxDist) {
        const palette = ['#1e3a8a', '#93c5fd', '#fbcfe8', '#bbf7d0', '#fed7aa', '#e9d5ff'];
        const labelsData = dash.distLabels || ['Ayam Broiler', 'Ayam Kampung', 'Bebek'];
        const valData = dash.distData || [0,0,0];
        
        new Chart(ctxDist, {
            type: 'bar',
            data: {
                labels: labelsData,
                datasets: [{ 
                    label: 'Penjualan', 
                    data: valData, 
                    backgroundColor: labelsData.map((_, i) => palette[i % palette.length]),
                    borderRadius: 6
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } }, 
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } 
            }
        });
    }

    // -------------------------------------------------------
    // Kasir: POS Transaksi Penjualan
    // -------------------------------------------------------
    const btnTambahKeranjang = document.getElementById('btn-tambah-keranjang');
    const cartTbody   = document.getElementById('cart-tbody');
    const cartEmpty   = document.getElementById('cart-empty');
    const cartItems   = document.getElementById('cart-items');
    const cartCount   = document.getElementById('cart-count');
    const cartCountSummary = document.getElementById('cart-count-summary');
    const cartTotal   = document.getElementById('cart-total');
    const btnCheckout = document.getElementById('btn-checkout');
    const btnClearCart = document.getElementById('btn-clear-cart');

    let kasirCart = [];

    function updateCartUI() {
        if (!cartTbody) return;
        const count = kasirCart.reduce((s, i) => s + i.jumlah, 0);
        const total = kasirCart.reduce((s, i) => s + (i.harga * i.jumlah), 0);

        if (cartCount) cartCount.textContent = count;
        if (cartCountSummary) cartCountSummary.textContent = count;
        if (cartTotal) cartTotal.textContent = formatRupiah(total);

        if (kasirCart.length === 0) {
            if (cartEmpty) cartEmpty.classList.remove('hidden');
            if (cartItems) cartItems.classList.add('hidden');
            if (btnClearCart) btnClearCart.classList.add('hidden');
            if (btnCheckout) btnCheckout.disabled = true;
        } else {
            if (cartEmpty) cartEmpty.classList.add('hidden');
            if (cartItems) cartItems.classList.remove('hidden');
            if (btnClearCart) btnClearCart.classList.remove('hidden');
            if (btnCheckout) btnCheckout.disabled = false;
        }

        cartTbody.innerHTML = '';
        kasirCart.forEach((item, idx) => {
            const tr = document.createElement('tr');
            tr.className = 'border-b border-gray-100 group';
            tr.innerHTML = `
                <td class="py-3 pr-4">
                    <div class="text-sm font-medium text-gray-800">${item.nama}</div>
                    <div class="text-xs text-gray-400">Rp ${parseInt(item.harga).toLocaleString('id-ID')} / item</div>
                </td>
                <td class="py-3 text-center">
                    <div class="inline-flex items-center gap-1">
                        <button type="button" class="w-7 h-7 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-100 cursor-pointer flex items-center justify-center text-sm font-bold cart-minus transition-all" data-idx="${idx}">−</button>
                        <span class="w-8 text-center text-sm font-semibold text-gray-800">${item.jumlah}</span>
                        <button type="button" class="w-7 h-7 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-100 cursor-pointer flex items-center justify-center text-sm font-bold cart-plus transition-all" data-idx="${idx}">+</button>
                    </div>
                </td>
                <td class="py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <span class="text-sm font-semibold text-gray-800">${formatRupiah(item.harga * item.jumlah)}</span>
                        <button type="button" class="w-6 h-6 rounded-full flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 cursor-pointer bg-transparent border-none cart-remove transition-all opacity-0 group-hover:opacity-100" data-idx="${idx}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </td>`;
            cartTbody.appendChild(tr);
        });
    }

    if (btnTambahKeranjang) {
        btnTambahKeranjang.addEventListener('click', () => {
            const sel = document.getElementById('kasir-produk');
            const opt = sel.options[sel.selectedIndex];
            const jumlahInput = document.getElementById('kasir-jumlah');
            const jumlah = parseInt(jumlahInput.value) || 1;

            if (!opt || !opt.value) {
                showToast('Pilih produk terlebih dahulu', 'error');
                sel.focus();
                return;
            }

            const produkId = parseInt(opt.value);
            const nama     = opt.dataset.nama;
            const harga    = parseFloat(opt.dataset.harga);
            const stok     = parseInt(opt.dataset.stok);

            const existing = kasirCart.find(i => i.produk_id === produkId);
            const currentQty = existing ? existing.jumlah : 0;

            if (currentQty + jumlah > stok) {
                showToast(`Stok ${nama} tidak cukup. Tersisa: ${stok}`, 'error');
                return;
            }

            if (existing) {
                existing.jumlah += jumlah;
            } else {
                kasirCart.push({ produk_id: produkId, nama, harga, jumlah, stok });
            }

            updateCartUI();
            sel.value = '';
            jumlahInput.value = 1;
            showToast(`${nama} ditambahkan ke keranjang`);
        });
    }

    if (btnClearCart) {
        btnClearCart.addEventListener('click', () => {
            if (confirm('Kosongkan seluruh keranjang?')) {
                kasirCart = [];
                updateCartUI();
                showToast('Keranjang dikosongkan');
            }
        });
    }

    if (cartTbody) {
        cartTbody.addEventListener('click', (e) => {
            const minusBtn  = e.target.closest('.cart-minus');
            const plusBtn   = e.target.closest('.cart-plus');
            const removeBtn = e.target.closest('.cart-remove');

            if (minusBtn) {
                const idx = parseInt(minusBtn.dataset.idx);
                if (kasirCart[idx].jumlah > 1) kasirCart[idx].jumlah--;
                else kasirCart.splice(idx, 1);
                updateCartUI();
            }
            if (plusBtn) {
                const idx = parseInt(plusBtn.dataset.idx);
                if (kasirCart[idx].jumlah < kasirCart[idx].stok) kasirCart[idx].jumlah++;
                else showToast('Stok tidak mencukupi', 'error');
                updateCartUI();
            }
            if (removeBtn) {
                const idx = parseInt(removeBtn.dataset.idx);
                kasirCart.splice(idx, 1);
                updateCartUI();
            }
        });
    }

    if (btnCheckout) {
        btnCheckout.addEventListener('click', () => {
            const mitraId = document.getElementById('kasir-mitra')?.value;
            if (!mitraId) {
                showToast('Pilih mitra terlebih dahulu', 'error');
                document.getElementById('kasir-mitra').focus();
                return;
            }
            if (kasirCart.length === 0) {
                showToast('Keranjang masih kosong', 'error');
                return;
            }

            btnCheckout.disabled = true;
            const originalText = btnCheckout.textContent;
            btnCheckout.textContent = 'Memproses...';

            fetch('/kasir/transaksi', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({
                    mitra_id: mitraId,
                    items: kasirCart.map(i => ({ produk_id: i.produk_id, jumlah: i.jumlah })),
                }),
            })
            .then(r => r.ok ? r.json() : r.json().then(e => { throw e; }))
            .then(data => {
                showToast(`Transaksi ${data.transaksi.no_transaksi} berhasil!`);
                kasirCart = [];
                updateCartUI();
                document.getElementById('kasir-mitra').value = '';

                data.transaksi.items.forEach(item => {
                    const opt = document.querySelector(`#kasir-produk option[value="${item.produk_id}"]`);
                    if (opt) {
                        const newStok = parseInt(opt.dataset.stok) - item.jumlah;
                        opt.dataset.stok = newStok;
                        if (newStok <= 0) opt.remove();
                    }
                });

                btnCheckout.disabled = true;
                btnCheckout.textContent = originalText;
            })
            .catch(err => {
                showToast(err.message || 'Gagal menyimpan transaksi', 'error');
                btnCheckout.disabled = false;
                btnCheckout.textContent = originalText;
            });
        });
    }

    // -------------------------------------------------------
    // Kasir: Riwayat Transaksi (expand rows + search)
    // -------------------------------------------------------
    document.querySelectorAll('.riwayat-row').forEach(row => {
        row.addEventListener('click', () => {
            const id = row.dataset.id;
            const detail = document.querySelector(`.detail-row[data-parent="${id}"]`);
            const icon = row.querySelector('.expand-icon');
            if (detail) {
                const isHidden = detail.classList.contains('hidden');
                // Close others
                document.querySelectorAll('.detail-row').forEach(d => {
                    if (d !== detail) {
                        d.classList.add('hidden');
                        const pId = d.dataset.parent;
                        const pIcon = document.querySelector(`.riwayat-row[data-id="${pId}"] .expand-icon`);
                        if (pIcon) pIcon.style.transform = '';
                    }
                });
                detail.classList.toggle('hidden');
                if (icon) icon.style.transform = isHidden ? 'rotate(180deg)' : '';
            }
        });
    });

    const searchRiwayat = document.getElementById('search-riwayat');
    if (searchRiwayat) {
        searchRiwayat.addEventListener('input', () => {
            const q = searchRiwayat.value.toLowerCase();
            document.querySelectorAll('.riwayat-row').forEach(row => {
                const text = row.textContent.toLowerCase();
                const show = text.includes(q);
                row.style.display = show ? '' : 'none';
                const detail = document.querySelector(`.detail-row[data-parent="${row.dataset.id}"]`);
                if (detail && !show) detail.classList.add('hidden');
            });
        });
    }

    // -------------------------------------------------------
    // Kasir: Tagihan Bulanan (expand mitra sections)
    // -------------------------------------------------------
    document.querySelectorAll('.tagihan-mitra-header').forEach(header => {
        header.addEventListener('click', () => {
            const detail = header.nextElementSibling;
            const icon = header.querySelector('.tagihan-expand-icon');
            if (detail && detail.classList.contains('tagihan-mitra-detail')) {
                const isHidden = detail.classList.contains('hidden');
                detail.classList.toggle('hidden');
                if (icon) icon.style.transform = isHidden ? 'rotate(180deg)' : '';
            }
        });
    });

    // -------------------------------------------------------
    // IGLOO.INC STYLE ANIMATIONS (GSAP)
    // -------------------------------------------------------
    if (typeof gsap !== 'undefined') {
        // 1. Staggered Entrance Animations (Igloo style page reveal)
        gsap.from('.card', {
            y: 40, opacity: 0, duration: 0.8, stagger: 0.1, ease: 'back.out(1.7)'
        });
        
        gsap.from('tbody tr', {
            y: 20, opacity: 0, duration: 0.6, stagger: 0.05, ease: 'power2.out', delay: 0.2
        });

        // 2. Interactive Mascot (Dashboard Duck)
        const dashDuck = document.getElementById('dashboard-duck');
        if (dashDuck) {
            // Breathing / idle animation
            gsap.to('.dash-duck-body', { y: -5, duration: 2, yoyo: true, repeat: -1, ease: 'sine.inOut' });
            gsap.to('.dash-duck-wing', { rotation: -15, transformOrigin: 'top right', duration: 0.8, yoyo: true, repeat: -1, ease: 'sine.inOut' });

            // Eye tracking / Magnetic look
            document.addEventListener('mousemove', (e) => {
                const rect = dashDuck.getBoundingClientRect();
                const duckX = rect.left + rect.width / 2;
                const duckY = rect.top + rect.height / 2;
                const deltaX = e.clientX - duckX;
                const deltaY = e.clientY - duckY;
                
                // Move duck body slightly towards mouse
                gsap.to(dashDuck, {
                    x: deltaX * 0.05,
                    y: deltaY * 0.05,
                    rotation: deltaX * 0.02,
                    duration: 1.5,
                    ease: 'power2.out'
                });
            });
            
            // Interaction on click
            dashDuck.addEventListener('click', () => {
                gsap.fromTo(dashDuck, { scale: 0.7, rotation: 20 }, { scale: 1, rotation: 0, duration: 0.8, ease: 'elastic.out(1, 0.3)' });
            });
        }

        // 3. Magnetic Hover Items (Igloo style UI)
        document.querySelectorAll('.btn-primary, .nav-item, .btn-icon').forEach(item => {
            item.addEventListener('mousemove', (e) => {
                const rect = item.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                gsap.to(item, { x: x * 0.2, y: y * 0.2, duration: 0.4, ease: 'power2.out' });
            });
            item.addEventListener('mouseleave', () => {
                gsap.to(item, { x: 0, y: 0, duration: 0.6, ease: 'elastic.out(1, 0.3)' });
            });
        });
    }
});
