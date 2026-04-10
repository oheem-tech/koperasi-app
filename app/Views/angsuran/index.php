<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Riwayat Angsuran Pinjaman</h2>
    <?php if(has_permission('manage_angsuran')): ?>
    <a href="<?= base_url('angsuran/create') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Input Pembayaran Angsuran</a>
    <?php endif; ?>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if(has_permission('manage_angsuran')): ?>
<?php
$selectedLabel = '';
if ($selected_anggota) {
    foreach ($anggota as $a) {
        if ($a['id'] == $selected_anggota) {
            $selectedLabel = $a['nama_lengkap'] . ' (' . $a['no_anggota'] . ')';
            break;
        }
    }
}
?>
<form method="get" id="filterFormAngsuran" class="mb-3">
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <div class="member-search-wrap position-relative" style="min-width:280px;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="angsuranSearchInput" class="form-control border-start-0 ps-0"
                    placeholder="Cari nama / no. anggota..."
                    value="<?= esc($selectedLabel) ?>"
                    autocomplete="off">
            </div>
            <input type="hidden" name="anggota_id" id="angsuranAnggotaId" value="<?= $selected_anggota ?>">
            <div id="angsuranSuggestions" class="member-suggestions shadow"></div>
        </div>
        <button type="submit" class="btn btn-sm btn-primary px-3"><i class="fas fa-filter me-1"></i>Filter</button>
        <?php if($selected_anggota): ?>
            <a href="<?= base_url('angsuran') ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times me-1"></i>Reset</a>
        <?php endif; ?>
    </div>
</form>

<?php if(!isset($memberSearchStylePrinted)): $memberSearchStylePrinted = true; ?>
<style>
.member-search-wrap .input-group { border: 1.5px solid #e2e8f0; border-radius: 8px; overflow: hidden; transition: border-color .2s; }
.member-search-wrap .input-group:focus-within { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
.member-search-wrap .input-group-text, .member-search-wrap .form-control { border: none; background: #fff; }
.member-suggestions {
    position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 999;
    background: #fff; border-radius: 8px; border: 1px solid #e2e8f0;
    max-height: 220px; overflow-y: auto; display: none;
}
.member-suggestions .sugg-item {
    padding: 8px 14px; cursor: pointer; font-size: 0.84rem;
    display: flex; align-items: center; gap: 10px;
    border-bottom: 1px solid #f1f5f9; transition: background .15s;
}
.member-suggestions .sugg-item:last-child { border-bottom: none; }
.member-suggestions .sugg-item:hover, .member-suggestions .sugg-item.active { background: #eff6ff; }
.member-suggestions .sugg-item .sugg-badge { font-size: 0.72rem; background: #dbeafe; color: #1d4ed8; border-radius: 5px; padding: 1px 7px; font-weight: 600; white-space: nowrap; }
.member-suggestions .sugg-item .sugg-name { font-weight: 500; color: #1e293b; }
.member-suggestions .sugg-empty { padding: 12px 14px; font-size: 0.83rem; color: #94a3b8; text-align: center; }
</style>
<?php endif; ?>

<script>
(function() {
    var anggotaData = <?= json_encode(array_map(function($a) {
        return ['id' => $a['id'], 'nama' => $a['nama_lengkap'], 'no' => $a['no_anggota']];
    }, $anggota)) ?>;

    function initMemberSearch(inputId, hiddenId, suggestId) {
        var inp = document.getElementById(inputId);
        var hid = document.getElementById(hiddenId);
        var box = document.getElementById(suggestId);
        if (!inp) return;
        var activeIdx = -1;

        function renderSuggestions(q) {
            box.innerHTML = ''; activeIdx = -1;
            if (!q) { box.style.display = 'none'; return; }
            var filtered = anggotaData.filter(function(a) {
                return a.nama.toLowerCase().includes(q.toLowerCase()) || a.no.toLowerCase().includes(q.toLowerCase());
            }).slice(0, 8);
            if (!filtered.length) {
                box.innerHTML = '<div class="sugg-empty">Anggota tidak ditemukan</div>';
                box.style.display = 'block'; return;
            }
            filtered.forEach(function(a) {
                var div = document.createElement('div');
                div.className = 'sugg-item';
                div.innerHTML = '<span class="sugg-badge">' + a.no + '</span><span class="sugg-name">' + a.nama + '</span>';
                div.addEventListener('mousedown', function(e) { e.preventDefault(); inp.value = a.nama + ' (' + a.no + ')'; hid.value = a.id; box.style.display = 'none'; });
                box.appendChild(div);
            });
            box.style.display = 'block';
        }

        inp.addEventListener('input', function() { hid.value = ''; renderSuggestions(this.value.trim()); });
        inp.addEventListener('keydown', function(e) {
            var items = box.querySelectorAll('.sugg-item');
            if (e.key === 'ArrowDown') { e.preventDefault(); activeIdx = Math.min(activeIdx + 1, items.length - 1); items.forEach(function(el, i) { el.classList.toggle('active', i === activeIdx); }); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); activeIdx = Math.max(activeIdx - 1, 0); items.forEach(function(el, i) { el.classList.toggle('active', i === activeIdx); }); }
            else if (e.key === 'Enter' && activeIdx >= 0 && items[activeIdx]) { e.preventDefault(); items[activeIdx].dispatchEvent(new Event('mousedown')); }
            else if (e.key === 'Escape') { box.style.display = 'none'; }
        });
        inp.addEventListener('blur', function() { setTimeout(function() { box.style.display = 'none'; }, 150); });
        inp.addEventListener('focus', function() { if (this.value.trim() && !hid.value) renderSuggestions(this.value.trim()); });
    }

    initMemberSearch('angsuranSearchInput', 'angsuranAnggotaId', 'angsuranSuggestions');
})();
</script>
<?php endif; ?>



<div class="card shadow-sm">
    <div class="card-body overflow-auto">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Waktu Pembayaran</th>
                    <?php if(has_permission('manage_angsuran')): ?>
                    <th>Nama Anggota</th>
                    <?php endif; ?>
                    <th>ID Pinjaman</th>
                    <th>Jenis Pinjaman</th>
                    <th>Cicilan Ke-</th>
                    <th>Pokok</th>
                    <th>Jasa (Bunga)</th>
                    <th>Total Bayar</th>
                    <th>Denda</th>
                    <th>Status Pinjaman</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($angsuran as $row): ?>
                <tr>
                    <td>
                        <?= date('d M Y', strtotime($row['tanggal_bayar'])) ?>
                        <?php if(isset($row['kas_keterangan']) && strpos($row['kas_keterangan'], '[Massal]') !== false): ?>
                            <br><small><span class="badge bg-warning text-dark mt-1" style="font-size:0.7em;">Massal</span></small>
                        <?php endif; ?>
                    </td>
                    <?php if(has_permission('manage_angsuran')): ?>
                    <td><?= $row['nama_lengkap'] ?> (<?= $row['no_anggota'] ?>)</td>
                    <?php endif; ?>
                    <td><span class="badge bg-secondary">PJ-<?= str_pad($row['pinjaman_id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                    <td><?= $row['jenis_pinjaman'] ?></td>
                    <td><?= $row['cicilan_ke'] ?> / <?= $row['lama_tenor'] ?></td>
                    <td>Rp <?= number_format($row['jumlah_pokok'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($row['jumlah_jasa'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($row['denda'], 0, ',', '.') ?></td>
                    <td>
                        <?php if(isset($row['pinjaman_status']) && strtolower($row['pinjaman_status']) == 'lunas'): ?>
                            <span class="badge bg-success">Lunas</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Berjalan</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center text-nowrap">
                        <a href="<?= base_url('angsuran/print/'.$row['id']) ?>" target="_blank"
                           class="btn btn-sm btn-outline-secondary" title="Cetak Kwitansi">
                            <i class="fas fa-print"></i>
                        </a>
                        <?php if(has_permission('manage_angsuran')): ?>
                        <a href="<?= base_url('angsuran/edit/'.$row['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit Transaksi">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= base_url('angsuran/delete/'.$row['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus Transaksi" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi angsuran ini? Pencatatan pada Kas Koperasi juga akan dibatalkan, dan jika ini adalah angsuran pelunasan, status pinjaman akan dikembalikan ke Belum Lunas.');">
                            <i class="fas fa-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($angsuran)): ?>
                <tr>
                    <td colspan="<?= has_permission('manage_angsuran') ? 11 : 10 ?>" class="text-center">Belum ada data pembayaran angsuran.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
