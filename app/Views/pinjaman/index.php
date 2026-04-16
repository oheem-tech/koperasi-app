<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Data Pinjaman</h2>
    <a href="<?= base_url('pinjaman/create') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Ajukan Pinjaman</a>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<?php if(has_permission('manage_pinjaman')): ?>
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
<form method="get" id="filterFormPinjaman" class="mb-3">
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <div class="member-search-wrap position-relative" style="min-width:280px;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="pinjamanSearchInput" class="form-control border-start-0 ps-0"
                    placeholder="Cari nama / no. anggota..."
                    value="<?= esc($selectedLabel) ?>"
                    autocomplete="off">
            </div>
            <input type="hidden" name="anggota_id" id="pinjamanAnggotaId" value="<?= $selected_anggota ?>">
            <div id="pinjamanSuggestions" class="member-suggestions shadow"></div>
        </div>
        <button type="submit" class="btn btn-sm btn-primary px-3"><i class="fas fa-filter me-1"></i>Filter</button>
        <?php if($selected_anggota): ?>
            <a href="<?= base_url('pinjaman') ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times me-1"></i>Reset</a>
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

    initMemberSearch('pinjamanSearchInput', 'pinjamanAnggotaId', 'pinjamanSuggestions');
})();
</script>
<?php endif; ?>



<div class="card shadow-sm">
    <div class="card-body overflow-auto">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Waktu Pengajuan</th>
                    <?php if(has_permission('manage_pinjaman')): ?>
                    <th>Nama Anggota</th>
                    <?php endif; ?>
                    <th>ID Pinjaman</th>
                    <th>Jenis</th>
                    <th>Jumlah Pinjaman</th>
                    <th>Tenor & Bunga</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <?php if(has_permission('manage_pinjaman')): ?>
                    <th width="140" class="text-center">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($pinjaman as $row): ?>
                <tr>
                    <td><?= date('d M Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                    <?php if(has_permission('manage_pinjaman')): ?>
                    <td><?= $row['nama_lengkap'] ?> (<?= $row['no_anggota'] ?>)</td>
                    <?php endif; ?>
                    <td><span class="badge bg-secondary">PJ-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                    <td><span class="badge bg-secondary"><?= $row['jenis_pinjaman'] ?></span></td>
                    <td>Rp <?= number_format($row['jumlah_pinjaman'], 0, ',', '.') ?></td>
                    <td><?= $row['lama_tenor'] ?> Bulan<br><small><?= $row['bunga_persen'] ?>% / bln</small></td>
                    <td><?= $row['tanggal_jatuh_tempo'] ? date('d M Y', strtotime($row['tanggal_jatuh_tempo'])) : '-' ?></td>
                    <td>
                        <?php 
                            if($row['status'] == 'pending') echo '<span class="badge bg-warning text-dark">Pending</span>';
                            elseif($row['status'] == 'disetujui') echo '<span class="badge bg-success">Disetujui</span>';
                            elseif($row['status'] == 'ditolak') echo '<span class="badge bg-danger">Ditolak</span>';
                            elseif($row['status'] == 'lunas') echo '<span class="badge bg-info">Lunas</span>';
                        ?>
                    </td>
                    <?php if(has_permission('manage_pinjaman')): ?>
                    <td class="text-center text-nowrap">
                        <a href="<?= base_url('pinjaman/print/'.$row['id']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak Pengajuan"><i class="fas fa-print"></i></a>
                        <a href="<?= base_url('pinjaman/edit/'.$row['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="<?= base_url('pinjaman/delete/'.$row['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus pinjaman ini?')"><i class="fas fa-trash"></i></a>

                        <?php if($row['status'] == 'pending'): ?>
                        <div class="mt-1">
                            <a href="<?= base_url('pinjaman/approve/'.$row['id']) ?>" class="btn btn-sm btn-outline-success" onclick="return confirm('Persetujuan pinjaman akan menambahkan dana tersebut ke daftar angsuran. Lanjutkan?')" title="Setujui"><i class="fas fa-check"></i></a>
                            <a href="<?= base_url('pinjaman/reject/'.$row['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tolak pinjaman ini?')" title="Tolak"><i class="fas fa-times"></i></a>
                        </div>
                        <?php elseif($row['status'] == 'disetujui'): ?>
                        <div class="mt-1">
                            <a href="<?= base_url('angsuran/pelunasan/'.$row['id']) ?>" class="btn btn-sm btn-outline-info" title="Lunasi"><i class="fas fa-hand-holding-usd"></i></a>
                        </div>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($pinjaman)): ?>
                <tr>
                    <td colspan="<?= has_permission('manage_pinjaman') ? 9 : 7 ?>" class="text-center">Belum ada data pinjaman.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
