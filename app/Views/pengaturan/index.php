<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<h2 class="mb-4"><i class="fas fa-cogs"></i> Pengaturan Master Koperasi</h2>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<?php
// Kelompokkan parameter berdasarkan prefix key
$groups = [
    'Identitas Koperasi' => [],
    'Pinjaman & Bunga' => [],
    'Tenor & Jenis Pinjaman' => [],
    'Kebijakan Pelunasan' => [],
    'Distribusi SHU' => [],
    'Lainnya' => [],
];
foreach ($pengaturan as $row) {
    $key = $row['pengaturan_key'];
    if (strpos($key, 'koperasi_') === 0) {
        $groups['Identitas Koperasi'][] = $row;
    } elseif (strpos($key, 'shu_') === 0) {
        $groups['Distribusi SHU'][] = $row;
    } elseif (strpos($key, 'pelunasan_') === 0 || strpos($key, 'kebijakan_') === 0) {
        $groups['Kebijakan Pelunasan'][] = $row;
    } elseif (strpos($key, 'tenor') !== false || strpos($key, 'jenis') !== false || strpos($key, 'maks_') !== false) {
        $groups['Tenor & Jenis Pinjaman'][] = $row;
    } elseif (strpos($key, 'bunga') !== false || strpos($key, 'denda') !== false) {
        $groups['Pinjaman & Bunga'][] = $row;
    } else {
        $groups['Lainnya'][] = $row;
    }
}
$groupColors = [
    'Identitas Koperasi' => 'dark',
    'Pinjaman & Bunga' => 'primary',
    'Tenor & Jenis Pinjaman' => 'info',
    'Kebijakan Pelunasan' => 'warning',
    'Distribusi SHU' => 'success',
    'Lainnya' => 'secondary',
];
?>

<form action="<?= base_url('pengaturan/update') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <?php foreach($groups as $groupName => $rows): ?>
            <?php if(empty($rows)) continue; ?>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-<?= $groupColors[$groupName] ?> text-white fw-semibold">
                        <i class="fas fa-sliders-h me-2"></i> <?= $groupName ?>
                        <?php if($groupName == 'Kebijakan Pelunasan' && !is_premium()): ?>
                            <span class="badge bg-warning text-dark float-end mt-1" style="font-size: 0.65rem;" title="Aktifkan Lisensi Pro untuk mengubah kebijakan ini."><i class="fas fa-lock"></i> PRO</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>Parameter</th><th>Keterangan</th><th width="30%">Nilai</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach($rows as $row): ?>
                                <tr>
                                    <td><code class="fs-7"><?= $row['pengaturan_key'] ?></code></td>
                                    <td><small class="text-muted"><?= $row['keterangan'] ?></small></td>
                                    <td>
                                        <?php if($row['pengaturan_key'] == 'koperasi_logo'): ?>
                                            <?php if(!empty($row['pengaturan_value'])): ?>
                                                <div class="mb-2">
                                                    <img src="<?= base_url('uploads/logo/' . $row['pengaturan_value']) ?>" alt="Logo Koperasi" style="max-height: 48px; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                                </div>
                                            <?php endif; ?>
                                            <input type="file" class="form-control form-control-sm" name="koperasi_logo" accept="image/png, image/jpeg, image/jpg">
                                            <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah logo.</small>
                                        <?php elseif($row['pengaturan_key'] == 'shu_metode_modal'): ?>
                                            <select class="form-select form-select-sm" name="settings[<?= $row['id'] ?>]" required>
                                                <option value="akumulasi_akhir" <?= ($row['pengaturan_value'] == 'akumulasi_akhir') ? 'selected' : '' ?>>Nilai Akumulasi Keseluruhan Terakhir</option>
                                                <option value="rata_rata_berjalan" <?= ($row['pengaturan_value'] == 'rata_rata_berjalan') ? 'selected' : '' ?>>Rata-rata Nominal Saldo Berjalan Tiap Bulan dalam Tahun (Bulan-Poin)</option>
                                            </select>
                                        <?php else: ?>
                                            <?php $isLocked = ($groupName == 'Kebijakan Pelunasan' && !is_premium()); ?>
                                            <input type="text" class="form-control form-control-sm <?= $isLocked ? 'bg-light text-muted' : '' ?>"
                                                   name="settings[<?= $row['id'] ?>]"
                                                   value="<?= $row['pengaturan_value'] ?>"
                                                   <?= $isLocked ? 'readonly title="Fitur khusus Lisensi Pro"' : 'required' ?>>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-2 mb-4">
        <button type="submit" class="btn btn-primary px-4">
            <i class="fas fa-save me-1"></i> Simpan Semua Perubahan
        </button>
    </div>
</form>
<?= $this->endSection() ?>
