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
    'Distribusi SHU Koperasi' => [],
    'Pembagian Internal Pengurus' => [],
    'WhatsApp Gateway' => [],
    'Lainnya' => [],
];
foreach ($pengaturan as $row) {
    $key = $row['pengaturan_key'];
    if (strpos($key, 'koperasi_') === 0) {
        $groups['Identitas Koperasi'][] = $row;
    } elseif (strpos($key, 'shu_pengurus_') === 0 && $key !== 'shu_pengurus_anggota') {
        $groups['Pembagian Internal Pengurus'][] = $row;
    } elseif (strpos($key, 'shu_') === 0) {
        $groups['Distribusi SHU Koperasi'][] = $row;
    } elseif (strpos($key, 'wa_') === 0) {
        $groups['WhatsApp Gateway'][] = $row;
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
    'Distribusi SHU Koperasi' => 'success',
    'Pembagian Internal Pengurus' => 'primary',
    'WhatsApp Gateway' => 'success',
    'Lainnya' => 'secondary',
];
$groupIcons = [
    'Identitas Koperasi' => 'fas fa-building',
    'Pinjaman & Bunga' => 'fas fa-percentage',
    'Tenor & Jenis Pinjaman' => 'fas fa-calendar-alt',
    'Kebijakan Pelunasan' => 'fas fa-shield-alt',
    'Distribusi SHU Koperasi' => 'fas fa-chart-pie',
    'Pembagian Internal Pengurus' => 'fas fa-users-cog',
    'WhatsApp Gateway' => 'fab fa-whatsapp',
    'Lainnya' => 'fas fa-sliders-h',
];
?>

<form action="<?= base_url('pengaturan/update') ?>" method="post" enctype="multipart/form-data" id="formPengaturan">
    <?= csrf_field() ?>
    <div class="row">
        <?php foreach($groups as $groupName => $rows): ?>
            <?php if(empty($rows)) continue; ?>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-<?= $groupColors[$groupName] ?> text-white fw-semibold d-flex justify-content-between align-items-center">
                        <div>
                            <i class="<?= $groupIcons[$groupName] ?? 'fas fa-sliders-h' ?> me-2"></i> 
                            <span <?= ($groupName == 'Lainnya') ? 'onclick="revealLicense(this)" style="cursor:pointer;"' : '' ?>><?= $groupName ?></span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <?php if($groupName == 'Distribusi SHU Koperasi'): ?>
                                <span id="shuGlobalHeaderBadge" class="badge bg-white text-dark font-monospace shadow-sm" style="font-size: 0.82rem;">
                                    Total SHU: <strong id="shuGlobalHeaderTotal">0%</strong>
                                </span>
                            <?php elseif($groupName == 'Pembagian Internal Pengurus'): ?>
                                <span id="shuPengurusHeaderBadge" class="badge bg-white text-dark font-monospace shadow-sm" style="font-size: 0.82rem;">
                                    Total Pengurus: <strong id="shuPengurusHeaderTotal">0%</strong>
                                </span>
                            <?php endif; ?>
                            <?php if(($groupName == 'Kebijakan Pelunasan' || $groupName == 'WhatsApp Gateway') && !is_premium()): ?>
                                <span class="badge bg-warning text-dark" style="font-size: 0.65rem;" title="Aktifkan Lisensi Pro untuk mengubah kebijakan ini."><i class="fas fa-lock"></i> PRO</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if($groupName == 'Distribusi SHU Koperasi'): ?>
                            <div id="shuGlobalAlertBox" class="m-3 mb-2 p-2 px-3 rounded small alert alert-danger" style="display:none;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle fa-lg me-2" id="shuGlobalAlertIcon"></i>
                                    <div><span id="shuGlobalAlertMsg"></span></div>
                                </div>
                            </div>
                        <?php elseif($groupName == 'Pembagian Internal Pengurus'): ?>
                            <div class="px-3 py-2 bg-light border-bottom small text-muted">
                                <i class="fas fa-info-circle text-primary me-1"></i>
                                Membagi porsi SHU Pengurus (<strong><span id="refPorsiPengurus" class="text-dark fw-bold">16%</span></strong>) ke masing-masing personil/jabatan. Total alokasi personil harus <strong>100%</strong>.
                            </div>
                            <div id="shuPengurusAlertBox" class="m-3 mb-2 p-2 px-3 rounded small alert alert-danger" style="display:none;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle fa-lg me-2" id="shuPengurusAlertIcon"></i>
                                    <div><span id="shuPengurusAlertMsg"></span></div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>Parameter</th><th>Keterangan</th><th width="35%">Nilai</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach($rows as $row): ?>
                                <?php 
                                $isGlobalShu = ($groupName == 'Distribusi SHU Koperasi');
                                $isPengurusShu = ($groupName == 'Pembagian Internal Pengurus');
                                ?>
                                <tr>
                                    <td>
                                        <code class="fs-7"><?= $row['pengaturan_key'] ?></code>
                                        <?php if($isPengurusShu): ?>
                                            <span class="badge bg-primary text-white ms-1" style="font-size: 0.65rem;">Jabatan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($row['pengaturan_key'] == 'shu_pengurus_anggota'): ?>
                                            <span class="fw-semibold text-dark">Alokasi Bagian Pengurus (%)</span>
                                            <div class="small text-primary mt-1">
                                                <i class="fas fa-level-down-alt me-1"></i> Dibagi ke personil di tabel Pembagian Internal Pengurus
                                            </div>
                                        <?php elseif($row['pengaturan_key'] == 'telegram_token'): ?>
                                            <span class="fw-semibold text-dark">Token Bot Telegram</span>
                                            <span class="badge bg-secondary ms-1" style="font-size: 0.65rem;">Opsional / Pilihan</span>
                                            <div class="small text-muted mt-1">Token Bot dari @BotFather (hanya diisi jika koperasi menggunakan bot Telegram)</div>
                                        <?php else: ?>
                                            <small class="text-muted"><?= $row['keterangan'] ?></small>
                                        <?php endif; ?>
                                    </td>
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
                                        <?php elseif($row['pengaturan_key'] == 'kode_lisensi'): ?>
                                            <?php if(is_premium()): ?>
                                            <!-- Hidden input always submits the value -->
                                            <input type="hidden" name="settings[<?= $row['id'] ?>]" value="<?= $row['pengaturan_value'] ?>" id="licenseHidden_<?= $row['id'] ?>">
                                            <div id="licenseVisualDummy_<?= $row['id'] ?>" class="form-control form-control-sm bg-light fw-bold text-success">
                                                <i class="fas fa-check-circle"></i> Lisensi Pro
                                            </div>
                                            <input type="text"
                                                   id="licenseSecretInput_<?= $row['id'] ?>"
                                                   style="display:none;"
                                                   class="form-control form-control-sm mt-1"
                                                   placeholder="Masukkan Kode Lisensi PRO di sini..."
                                                   value="<?= $row['pengaturan_value'] ?>"
                                                   oninput="document.getElementById('licenseHidden_<?= $row['id'] ?>').value = this.value">
                                            <?php else: ?>
                                            <input type="text"
                                                   id="licenseSecretInput_<?= $row['id'] ?>"
                                                   class="form-control form-control-sm"
                                                   name="settings[<?= $row['id'] ?>]"
                                                   placeholder="Masukkan Kode Lisensi PRO di sini..."
                                                   value="<?= $row['pengaturan_value'] ?>">
                                            <?php endif; ?>
                                        <?php elseif(strpos($row['pengaturan_key'], '_aktif') !== false && strpos($row['pengaturan_key'], 'wa_') === 0): ?>
                                            <?php $isLocked = (($groupName == 'Kebijakan Pelunasan' || $groupName == 'WhatsApp Gateway') && !is_premium()); ?>
                                            <select class="form-select form-select-sm <?= $isLocked ? 'bg-light text-muted' : '' ?>" name="settings[<?= $row['id'] ?>]" <?= $isLocked ? 'disabled title="Fitur khusus Lisensi Pro"' : 'required' ?>>
                                                <option value="1" <?= ($row['pengaturan_value'] == '1') ? 'selected' : '' ?>>Aktif</option>
                                                <option value="0" <?= ($row['pengaturan_value'] == '0') ? 'selected' : '' ?>>Nonaktif</option>
                                            </select>
                                            <?php if($isLocked): ?>
                                                <input type="hidden" name="settings[<?= $row['id'] ?>]" value="<?= $row['pengaturan_value'] ?>">
                                            <?php endif; ?>
                                        <?php elseif(strpos($row['pengaturan_key'], 'wa_template_') === 0): ?>
                                            <?php $isLocked = (($groupName == 'Kebijakan Pelunasan' || $groupName == 'WhatsApp Gateway') && !is_premium()); ?>
                                            <textarea class="form-control form-control-sm <?= $isLocked ? 'bg-light text-muted' : '' ?>" name="settings[<?= $row['id'] ?>]" rows="3" <?= $isLocked ? 'readonly title="Fitur khusus Lisensi Pro"' : 'required' ?>><?= htmlspecialchars($row['pengaturan_value']) ?></textarea>
                                            <small class="text-muted d-block mt-1">Gunakan tag dinamis seperti {Nama}, {Nominal}, {Tanggal}, dll sesuai keterangan modul.</small>
                                        <?php elseif($row['pengaturan_key'] == 'telegram_token'): ?>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light text-primary"><i class="fab fa-telegram-plane"></i></span>
                                                <input type="text"
                                                       class="form-control form-control-sm"
                                                       name="settings[<?= $row['id'] ?>]"
                                                       value="<?= esc($row['pengaturan_value']) ?>"
                                                       placeholder="Opsional - Kosongkan jika tidak memakai bot Telegram">
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-info-circle text-info me-1"></i>Fitur Telegram Bot bersifat <strong>pilihan / opsional</strong>. Kosongkan jika tidak digunakan.
                                            </small>
                                        <?php else: ?>
                                            <?php 
                                            $isLocked = (($groupName == 'Kebijakan Pelunasan' || $groupName == 'WhatsApp Gateway') && !is_premium());
                                            $isRequired = !in_array($row['pengaturan_key'], ['wa_token', 'kode_lisensi', 'telegram_token']);
                                            ?>
                                            <?php if($isGlobalShu && $row['pengaturan_key'] !== 'shu_metode_modal'): ?>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" 
                                                           step="any" 
                                                           min="0" 
                                                           max="100"
                                                           class="form-control form-control-sm text-end fw-semibold shu-global-input"
                                                           id="input_<?= $row['pengaturan_key'] ?>"
                                                           name="settings[<?= $row['id'] ?>]"
                                                           value="<?= $row['pengaturan_value'] ?>"
                                                           required>
                                                    <span class="input-group-text bg-light text-muted px-2">%</span>
                                                </div>
                                            <?php elseif($isPengurusShu): ?>
                                                <div class="d-flex align-items-center gap-1">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" 
                                                               step="any" 
                                                               min="0" 
                                                               max="100"
                                                               class="form-control form-control-sm text-end fw-semibold shu-pengurus-input"
                                                               name="settings[<?= $row['id'] ?>]"
                                                               value="<?= $row['pengaturan_value'] ?>"
                                                               required>
                                                        <span class="input-group-text bg-light text-muted px-2">%</span>
                                                    </div>
                                                    <a href="<?= base_url('pengaturan/delete_parameter/' . $row['id']) ?>" 
                                                       class="btn btn-outline-danger btn-sm px-2" 
                                                       title="Hapus parameter jabatan ini"
                                                       onclick="return confirm('Apakah Anda yakin ingin menghapus parameter <?= esc($row['pengaturan_key']) ?>?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <input type="text" class="form-control form-control-sm <?= $isLocked ? 'bg-light text-muted' : '' ?>"
                                                       name="settings[<?= $row['id'] ?>]"
                                                       value="<?= $row['pengaturan_value'] ?>"
                                                       <?= $isLocked ? 'readonly title="Fitur khusus Lisensi Pro"' : ($isRequired ? 'required' : '') ?>>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <?php if($groupName == 'Distribusi SHU Koperasi'): ?>
                            <tfoot class="table-light border-top">
                                <tr class="align-middle">
                                    <th colspan="2" class="text-end py-2">
                                        <i class="fas fa-calculator text-muted me-1"></i> Total Alokasi SHU Koperasi:
                                    </th>
                                    <th class="py-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="fs-6 fw-bold" id="shuGlobalFooterTotal">0%</span>
                                            <span id="shuGlobalFooterBadge" class="badge"></span>
                                        </div>
                                    </th>
                                </tr>
                            </tfoot>
                            <?php elseif($groupName == 'Pembagian Internal Pengurus'): ?>
                            <tfoot class="table-light border-top">
                                <tr class="align-middle">
                                    <th colspan="2" class="text-end py-2">
                                        <i class="fas fa-calculator text-muted me-1"></i> Total Pembagian Pengurus:
                                    </th>
                                    <th class="py-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="fs-6 fw-bold" id="shuPengurusFooterTotal">0%</span>
                                            <span id="shuPengurusFooterBadge" class="badge"></span>
                                        </div>
                                    </th>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                        <?php if($groupName == 'Distribusi SHU Koperasi'): ?>
                        <div class="p-2 border-top bg-light text-muted small ps-3">
                            <i class="fas fa-check-circle text-success me-1"></i> Alokasi SHU tingkat koperasi bersumber dari <strong>100%</strong> keuntungan bersih.
                        </div>
                        <?php elseif($groupName == 'Pembagian Internal Pengurus'): ?>
                        <div class="p-2 border-top bg-light d-flex justify-content-between align-items-center">
                            <span class="small text-muted ps-2">
                                <i class="fas fa-info-circle text-primary me-1"></i> Porsi internal dibagi dari jatah pengurus.
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addPengurusModal">
                                <i class="fas fa-plus-circle me-1"></i> Tambah Parameter Jabatan
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-2 mb-4 d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-primary px-4" id="btnSimpanPengaturan">
            <i class="fas fa-save me-1"></i> Simpan Semua Perubahan
        </button>
        <span id="submitWarningShu" class="text-danger small fw-semibold" style="display:none;">
            <i class="fas fa-exclamation-triangle me-1"></i> Total persentase melebihi 100%. Harap sesuaikan sebelum menyimpan.
        </span>
    </div>
</form>

<?php if(is_premium()): ?>
<!-- Info Box: URL Webhook Fonnte -->
<div class="card border-success shadow-sm mb-4">
    <div class="card-header bg-success text-white fw-semibold">
        <i class="fab fa-whatsapp me-2"></i> Panduan Setup WA Chatbot Inbound (Anggota Tanya via WA)
    </div>
    <div class="card-body">
        <p class="mb-2">Agar anggota bisa mengirim pesan WA untuk cek saldo/pinjaman, daftarkan URL berikut ke <strong>Fonnte Dashboard → Device → Webhook</strong>:</p>
        <div class="input-group mb-3">
            <input type="text" class="form-control font-monospace" id="webhookUrl" 
                   value="<?= base_url('webhook/whatsapp') ?>" readonly>
            <button class="btn btn-outline-success" type="button" onclick="copyWebhook()" title="Salin URL">
                <i class="fas fa-copy"></i> Salin
            </button>
        </div>
        <div class="row g-2">
            <div class="col-md-6">
                <div class="alert alert-info py-2 mb-0">
                    <strong><i class="fas fa-info-circle"></i> Cara Setup di Fonnte:</strong>
                    <ol class="mb-0 mt-1 ps-3 small">
                        <li>Login ke <a href="https://fonnte.com" target="_blank">fonnte.com</a></li>
                        <li>Pilih Device → klik <strong>Edit / Detail</strong></li>
                        <li>Tempel URL di atas ke kolom <strong>Webhook</strong></li>
                        <li>Aktifkan toggle <strong>autoread</strong> menjadi <strong>On</strong></li>
                        <li>Klik <strong>Update / Simpan</strong></li>
                    </ol>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-warning py-2 mb-0">
                    <strong><i class="fas fa-comments"></i> Perintah Anggota:</strong>
                    <ul class="mb-0 mt-1 ps-3 small">
                        <li><code>SALDO</code> — cek saldo simpanan</li>
                        <li><code>PINJAMAN</code> — sisa hutang pinjaman</li>
                        <li><code>ANGSURAN</code> — detail cicilan</li>
                        <li><code>INFO</code> — ringkasan akun</li>
                        <li><code>BANTUAN</code> — daftar perintah</li>
                    </ul>
                </div>
            </div>
        </div>
        <p class="text-muted small mt-2 mb-0">
            <i class="fas fa-shield-alt"></i> 
            Hanya anggota dengan nomor HP terdaftar di database yang bisa menggunakan layanan ini.
            Aktifkan toggle <strong>wa_chatbot_aktif</strong> di atas untuk mengaktifkan fitur ini.<br>
            <strong>Penting:</strong> Gunakan nomor pribadi Anda saat menguji coba (jangan mengirim pesan dari nomor Koperasi ke nomor Koperasi itu sendiri).
        </p>
    </div>
</div>
<?php endif; ?>

<script>
let secretClicks = 0;
function revealLicense(el) {
    secretClicks++;
    if (secretClicks >= 3) {
        document.querySelectorAll('[id^=licenseVisualDummy]').forEach(e => e.style.display = 'none');
        document.querySelectorAll('[id^=licenseSecretInput]').forEach(e => {
            e.style.display = 'block';
            e.type = 'text';
        });
        el.innerHTML = 'Lainnya <i class="fas fa-unlock ms-1 text-warning"></i>';
        secretClicks = 0;
    }
}
function copyWebhook() {
    const el = document.getElementById('webhookUrl');
    el.select();
    document.execCommand('copy');
    const btn = el.nextElementSibling;
    btn.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
    btn.classList.replace('btn-outline-success', 'btn-success');
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-copy"></i> Salin';
        btn.classList.replace('btn-success', 'btn-outline-success');
    }, 2000);
}

// Perhitungan Live Total Persentase Distribusi SHU Koperasi (Level 1)
function hitungTotalSHUGlobal() {
    const globalInputs = document.querySelectorAll('.shu-global-input');
    if (!globalInputs.length) return 0;

    let total = 0;
    globalInputs.forEach(input => {
        let val = parseFloat(input.value.replace(',', '.'));
        if (!isNaN(val)) total += val;
    });

    total = Math.round(total * 100) / 100;

    const headerTotal = document.getElementById('shuGlobalHeaderTotal');
    const headerBadge = document.getElementById('shuGlobalHeaderBadge');
    const footerTotal = document.getElementById('shuGlobalFooterTotal');
    const footerBadge = document.getElementById('shuGlobalFooterBadge');
    const alertBox = document.getElementById('shuGlobalAlertBox');
    const alertMsg = document.getElementById('shuGlobalAlertMsg');
    const alertIcon = document.getElementById('shuGlobalAlertIcon');

    if (headerTotal) headerTotal.textContent = total + '%';
    if (footerTotal) footerTotal.textContent = total + '%';

    // Update referensi porsi pengurus di kartu internal pengurus
    const porsiPengurusInput = document.getElementById('input_shu_pengurus_anggota');
    const refPorsi = document.getElementById('refPorsiPengurus');
    if (porsiPengurusInput && refPorsi) {
        refPorsi.textContent = (porsiPengurusInput.value || 0) + '%';
    }

    if (total === 100) {
        if (headerBadge) {
            headerBadge.className = 'badge bg-white text-success border border-success font-monospace shadow-sm';
            headerBadge.innerHTML = 'Total SHU: <strong>100%</strong> <i class="fas fa-check-circle ms-1"></i>';
        }
        if (footerBadge) {
            footerBadge.className = 'badge bg-success';
            footerBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i> Pas 100%';
        }
        if (alertBox) alertBox.style.display = 'none';
    } else if (total > 100) {
        const selisih = Math.round((total - 100) * 100) / 100;
        if (headerBadge) {
            headerBadge.className = 'badge bg-danger text-white font-monospace shadow-sm';
            headerBadge.innerHTML = 'Total SHU: <strong>' + total + '%</strong> <i class="fas fa-exclamation-triangle ms-1"></i>';
        }
        if (footerBadge) {
            footerBadge.className = 'badge bg-danger';
            footerBadge.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Kelebihan ' + selisih + '%';
        }
        if (alertBox) {
            alertBox.className = 'm-3 mb-2 p-2 px-3 rounded small alert alert-danger';
            if (alertIcon) alertIcon.className = 'fas fa-exclamation-triangle fa-lg me-2 text-danger';
            if (alertMsg) alertMsg.innerHTML = '<strong>Peringatan Defisit:</strong> Total alokasi SHU Koperasi adalah <strong>' + total + '%</strong> (Kelebihan <strong>' + selisih + '%</strong>). Melebihi 100% laba bersih koperasi!';
            alertBox.style.display = 'block';
        }
    } else {
        const sisa = Math.round((100 - total) * 100) / 100;
        if (headerBadge) {
            headerBadge.className = 'badge bg-warning text-dark font-monospace shadow-sm';
            headerBadge.innerHTML = 'Total SHU: <strong>' + total + '%</strong>';
        }
        if (footerBadge) {
            footerBadge.className = 'badge bg-warning text-dark';
            footerBadge.innerHTML = '<i class="fas fa-clock me-1"></i> Sisa ' + sisa + '%';
        }
        if (alertBox) {
            alertBox.className = 'm-3 mb-2 p-2 px-3 rounded small alert alert-warning';
            if (alertIcon) alertIcon.className = 'fas fa-info-circle fa-lg me-2 text-warning';
            if (alertMsg) alertMsg.innerHTML = '<strong>Alokasi Belum Penuh:</strong> Total alokasi SHU Koperasi adalah <strong>' + total + '%</strong>. Masih ada <strong>' + sisa + '%</strong> belum teralokasikan.';
            alertBox.style.display = 'block';
        }
    }

    checkSubmitStatus();
    return total;
}

// Perhitungan Live Total Persentase Pembagian Internal Pengurus (Level 2)
function hitungTotalSHUPengurus() {
    const pengurusInputs = document.querySelectorAll('.shu-pengurus-input');
    if (!pengurusInputs.length) return 0;

    let total = 0;
    pengurusInputs.forEach(input => {
        let val = parseFloat(input.value.replace(',', '.'));
        if (!isNaN(val)) total += val;
    });

    total = Math.round(total * 100) / 100;

    const headerTotal = document.getElementById('shuPengurusHeaderTotal');
    const headerBadge = document.getElementById('shuPengurusHeaderBadge');
    const footerTotal = document.getElementById('shuPengurusFooterTotal');
    const footerBadge = document.getElementById('shuPengurusFooterBadge');
    const alertBox = document.getElementById('shuPengurusAlertBox');
    const alertMsg = document.getElementById('shuPengurusAlertMsg');
    const alertIcon = document.getElementById('shuPengurusAlertIcon');
    const modalSisaShu = document.getElementById('modalSisaShuPengurus');

    if (headerTotal) headerTotal.textContent = total + '%';
    if (footerTotal) footerTotal.textContent = total + '%';

    if (total === 100) {
        if (headerBadge) {
            headerBadge.className = 'badge bg-white text-primary border border-primary font-monospace shadow-sm';
            headerBadge.innerHTML = 'Total Pengurus: <strong>100%</strong> <i class="fas fa-check-circle ms-1"></i>';
        }
        if (footerBadge) {
            footerBadge.className = 'badge bg-success';
            footerBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i> Pas 100%';
        }
        if (alertBox) alertBox.style.display = 'none';
        if (modalSisaShu) modalSisaShu.textContent = '0% (Sudah pas 100%)';
    } else if (total > 100) {
        const selisih = Math.round((total - 100) * 100) / 100;
        if (headerBadge) {
            headerBadge.className = 'badge bg-danger text-white font-monospace shadow-sm';
            headerBadge.innerHTML = 'Total Pengurus: <strong>' + total + '%</strong> <i class="fas fa-exclamation-triangle ms-1"></i>';
        }
        if (footerBadge) {
            footerBadge.className = 'badge bg-danger';
            footerBadge.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Kelebihan ' + selisih + '%';
        }
        if (alertBox) {
            alertBox.className = 'm-3 mb-2 p-2 px-3 rounded small alert alert-danger';
            if (alertIcon) alertIcon.className = 'fas fa-exclamation-triangle fa-lg me-2 text-danger';
            if (alertMsg) alertMsg.innerHTML = '<strong>Peringatan Defisit Pengurus:</strong> Total porsi personil pengurus adalah <strong>' + total + '%</strong> (Kelebihan <strong>' + selisih + '%</strong>). Melebihi 100% dari jatah alokasi pengurus!';
            alertBox.style.display = 'block';
        }
        if (modalSisaShu) modalSisaShu.textContent = '0% (Sudah melebihi 100% sebesar ' + selisih + '%)';
    } else {
        const sisa = Math.round((100 - total) * 100) / 100;
        if (headerBadge) {
            headerBadge.className = 'badge bg-warning text-dark font-monospace shadow-sm';
            headerBadge.innerHTML = 'Total Pengurus: <strong>' + total + '%</strong>';
        }
        if (footerBadge) {
            footerBadge.className = 'badge bg-warning text-dark';
            footerBadge.innerHTML = '<i class="fas fa-clock me-1"></i> Sisa ' + sisa + '%';
        }
        if (alertBox) {
            alertBox.className = 'm-3 mb-2 p-2 px-3 rounded small alert alert-warning';
            if (alertIcon) alertIcon.className = 'fas fa-info-circle fa-lg me-2 text-warning';
            if (alertMsg) alertMsg.innerHTML = '<strong>Pembagian Belum Penuh:</strong> Total porsi personil pengurus saat ini <strong>' + total + '%</strong>. Masih ada <strong>' + sisa + '%</strong> jatah pengurus yang belum dialokasikan.';
            alertBox.style.display = 'block';
        }
        if (modalSisaShu) modalSisaShu.textContent = sisa + '%';
    }

    checkSubmitStatus();
    return total;
}

// Cek status tombol submit secara terpadu
function checkSubmitStatus() {
    const globalInputs = document.querySelectorAll('.shu-global-input');
    const pengurusInputs = document.querySelectorAll('.shu-pengurus-input');
    
    let totalGlobal = 0;
    globalInputs.forEach(i => {
        let v = parseFloat(i.value.replace(',', '.'));
        if (!isNaN(v)) totalGlobal += v;
    });
    totalGlobal = Math.round(totalGlobal * 100) / 100;

    let totalPengurus = 0;
    pengurusInputs.forEach(i => {
        let v = parseFloat(i.value.replace(',', '.'));
        if (!isNaN(v)) totalPengurus += v;
    });
    totalPengurus = Math.round(totalPengurus * 100) / 100;

    const submitBtn = document.getElementById('btnSimpanPengaturan');
    const submitWarning = document.getElementById('submitWarningShu');

    const isOverGlobal = totalGlobal > 100;
    const isOverPengurus = totalPengurus > 100;

    if (isOverGlobal || isOverPengurus) {
        if (submitBtn) submitBtn.disabled = true;
        if (submitWarning) {
            let pesan = [];
            if (isOverGlobal) pesan.push('Alokasi SHU Koperasi (' + totalGlobal + '%) melebihi 100%');
            if (isOverPengurus) pesan.push('Pembagian Pengurus (' + totalPengurus + '%) melebihi 100%');
            submitWarning.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> ' + pesan.join(' & ') + '. Harap perbaiki sebelum menyimpan.';
            submitWarning.style.display = 'inline';
        }
    } else {
        if (submitBtn) submitBtn.disabled = false;
        if (submitWarning) submitWarning.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    hitungTotalSHUGlobal();
    hitungTotalSHUPengurus();

    document.querySelectorAll('.shu-global-input').forEach(input => {
        input.addEventListener('input', hitungTotalSHUGlobal);
        input.addEventListener('change', hitungTotalSHUGlobal);
    });

    document.querySelectorAll('.shu-pengurus-input').forEach(input => {
        input.addEventListener('input', hitungTotalSHUPengurus);
        input.addEventListener('change', hitungTotalSHUPengurus);
    });

    const addModal = document.getElementById('addPengurusModal');
    if (addModal) {
        addModal.addEventListener('show.bs.modal', function() {
            hitungTotalSHUPengurus();
        });
    }

    const form = document.getElementById('formPengaturan');
    if (form) {
        form.addEventListener('submit', function(e) {
            let totalG = 0;
            document.querySelectorAll('.shu-global-input').forEach(i => {
                let v = parseFloat(i.value.replace(',', '.'));
                if (!isNaN(v)) totalG += v;
            });
            totalG = Math.round(totalG * 100) / 100;

            let totalP = 0;
            document.querySelectorAll('.shu-pengurus-input').forEach(i => {
                let v = parseFloat(i.value.replace(',', '.'));
                if (!isNaN(v)) totalP += v;
            });
            totalP = Math.round(totalP * 100) / 100;

            if (totalG > 100) {
                e.preventDefault();
                alert('Tidak dapat menyimpan: Total alokasi Distribusi SHU Koperasi adalah ' + totalG + '%, melebihi batas 100%.');
                return false;
            }
            if (totalP > 100) {
                e.preventDefault();
                alert('Tidak dapat menyimpan: Total Pembagian Internal Pengurus adalah ' + totalP + '%, melebihi batas 100%.');
                return false;
            }
        });
    }
});
</script>

<!-- Modal Tambah Parameter Jabatan Pengurus -->
<div class="modal fade" id="addPengurusModal" tabindex="-1" aria-labelledby="addPengurusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addPengurusModalLabel"><i class="fas fa-plus-circle"></i> Tambah Parameter Jabatan Pengurus</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('pengaturan/add_parameter') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-body">
            <div class="alert alert-info py-2 px-3 small mb-3">
                <i class="fas fa-info-circle me-1"></i> Sisa alokasi internal pengurus yang tersedia: <strong id="modalSisaShuPengurus">0%</strong>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Jabatan Pengurus</label>
                <input type="text" class="form-control" name="nama_jabatan" placeholder="Contoh: Wakil Ketua" required>
                <small class="text-muted">Parameter otomatis berawalan <code>shu_pengurus_...</code></small>
            </div>
            <div class="mb-3">
                <label class="form-label">Persentase dari Jatah Pengurus (%)</label>
                <div class="input-group">
                    <input type="number" step="any" min="0" max="100" class="form-control" name="nilai" value="0" required>
                    <span class="input-group-text">%</span>
                </div>
                <small class="text-muted">Persentase dihitung dari 100% total porsi jatah pengurus.</small>
            </div>
        </div>
        <div class="modal-footer border-top-0">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Jabatan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
