<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<style>
.row-anggota td { background: #f1f5f9; font-weight: 600; }
.row-pinjaman td { background: #fff; font-size: 0.82rem; }
.row-pinjaman .indent { padding-left: 28px !important; }
.row-pinjaman.unchecked td { opacity: 0.45; }

@media (max-width: 768px) {
    #kasPreview { border-radius: 0 !important; bottom: 0 !important; left: 0; position: fixed !important; margin: 0 !important; width: 100%; z-index: 1000 !important; }
    #kasPreview .card-body { padding: 10px 15px !important; }
    .kas-preview-left { display: none !important; }
    .kas-preview-right { min-width: 100% !important; display: flex; flex-direction: row; justify-content: space-between; align-items: center; text-align: left !important; }
    .kas-preview-right .label-total { display: none !important; }
    #kasTotalPreview { font-size: 1.2rem !important; margin: 0 !important; padding: 0 !important; }
    .kas-preview-footer { margin-top: 0 !important; font-size: 0.65rem !important; text-align: right; }
}
</style>

<div class="page-header">
    <div>
        <h2><i class="fas fa-layer-group"></i> Input Massal — Kelompok <?= esc($kelompok) ?></h2>
        <p class="text-muted mb-0" style="font-size:0.85rem;">
            Tanggal Transaksi: <strong><?= date('d F Y', strtotime($tanggal)) ?></strong>
        </p>
    </div>
    <a href="<?= base_url('massal') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali Setup
    </a>
</div>

<?php if (empty($anggotaList)): ?>
<div class="alert alert-warning">
    <i class="fas fa-users-slash me-2"></i>
    Tidak ada anggota aktif pada kelompok <strong><?= esc($kelompok) ?></strong>.
    Silakan atur kelompok anggota di menu <a href="<?= base_url('anggota') ?>">Data Anggota</a>.
</div>
<?php else: ?>

<form action="<?= base_url('massal/store') ?>" method="post" id="formMassal">
    <?= csrf_field() ?>
    <input type="hidden" name="kelompok" value="<?= esc($kelompok) ?>">
    <input type="hidden" name="tanggal"  value="<?= esc($tanggal) ?>">

    <div class="alert alert-info py-2 px-3 mb-3" style="font-size:0.82rem;">
        <i class="fas fa-info-circle text-primary me-1"></i>
        Baris <span class="badge" style="background:#e2e8f0; color:#0f172a;">abu-abu</span> = Simpanan anggota.
        Baris <span class="badge bg-white border">putih</span> = Angsuran per pinjaman aktif.
        Centang baris untuk ikut diproses, kosongkan untuk dilewati. Nilai Pokok & Jasa bisa diedit.
    </div>

    <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
        <div class="card-header fw-bold py-3" style="background:#1e293b; color:#fff; font-size:0.9rem;">
            <i class="fas fa-table me-2" style="color:#06b6d4;"></i>
            Kelompok <?= esc($kelompok) ?>
            <span class="badge ms-2" style="background:#06b6d4;"><?= count($anggotaList) ?> Anggota</span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered mb-0 align-middle" id="tblMassal" style="font-size:0.83rem;">
                <thead style="background:#0f172a; color:#fff;">
                    <tr>
                        <th width="3%"><input type="checkbox" id="checkAllSimpanan" checked title="Centang Semua Simpanan"></th>
                        <th width="18%">Nama / Pinjaman</th>
                        <th width="17%">Jenis Simpanan / Pinjaman</th>
                        <th width="14%" class="text-center">Setoran / Pokok</th>
                        <th width="13%" class="text-center">Jasa / Bunga</th>
                        <th width="13%" class="text-end">Total Bayar</th>
                        <th width="10%" class="text-center text-muted" style="font-size:0.72rem;">Info</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $simpIdx = 0; // index untuk simpanan
                $angIdx  = 0; // index global untuk angsuran
                ?>
                <?php foreach ($anggotaList as $a): ?>

                    <!-- ===== BARIS SIMPANAN ANGGOTA ===== -->
                    <tr class="row-anggota" data-type="simpanan" data-sidx="<?= $simpIdx ?>">
                        <td class="text-center">
                            <input type="checkbox" class="chk-simpanan"
                                   name="simpanan_include[<?= $simpIdx ?>]" value="1" checked>
                        </td>
                        <td>
                            <i class="fas fa-user text-primary me-1"></i>
                            <?= esc($a['nama_lengkap']) ?>
                            <div><small class="text-muted fw-normal"><?= $a['no_anggota'] ?></small></div>
                            <!-- hidden fields simpanan -->
                            <input type="hidden" name="simpanan_anggota_id[<?= $simpIdx ?>]" value="<?= $a['id'] ?>">
                        </td>
                        <td>
                            <select name="simpanan_jenis_id[<?= $simpIdx ?>]" class="form-select form-select-sm">
                                <?php foreach ($jenisSimpanan as $js): ?>
                                <option value="<?= $js['id'] ?>"
                                    <?= ($jenisSimpananWajib && $js['id'] == $jenisSimpananWajib['id']) ? 'selected' : '' ?>>
                                    <?= esc($js['nama_simpanan']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td colspan="2">
                            <input type="text"
                                   name="simpanan_amount[<?= $simpIdx ?>]"
                                   class="form-control form-control-sm text-end fmt-rupiah"
                                   value="<?= number_format($jenisSimpananWajib ? $jenisSimpananWajib['minimal_setoran'] : 0, 0, ',', '.') ?>"
                                   data-sidx="<?= $simpIdx ?>">
                        </td>
                        <td class="text-end fw-bold simpanan-total" id="stotal-<?= $simpIdx ?>">
                            Rp <?= number_format($jenisSimpananWajib ? $jenisSimpananWajib['minimal_setoran'] : 0, 0, ',', '.') ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success">Simpanan</span>
                        </td>
                    </tr>

                    <?php if (empty($a['pinjaman_aktif'])): ?>
                    <!-- Tidak ada pinjaman aktif -->
                    <tr class="row-pinjaman">
                        <td></td>
                        <td colspan="6" class="indent text-muted" style="font-size:0.78rem; background:#fafafa;">
                            <i class="fas fa-check-circle text-success me-1"></i> Tidak ada pinjaman aktif
                        </td>
                    </tr>
                    <?php else: ?>

                    <?php foreach ($a['pinjaman_aktif'] as $pa): ?>
                    <!-- ===== SUB-BARIS ANGSURAN PER PINJAMAN ===== -->
                    <tr class="row-pinjaman" data-type="angsuran" data-aidx="<?= $angIdx ?>">
                        <td class="text-center" style="background:#fff;">
                            <input type="checkbox" class="chk-angsuran"
                                   name="ang_include[<?= $angIdx ?>]" value="1" checked>
                        </td>
                        <td class="indent" style="background:#fff;">
                            <i class="fas fa-file-invoice-dollar text-warning me-1"></i>
                            <?= esc($pa['jenis_pinjaman']) ?>
                            <!-- hidden fields angsuran -->
                            <input type="hidden" name="ang_anggota_id[<?= $angIdx ?>]"  value="<?= $a['id'] ?>">
                            <input type="hidden" name="ang_pinjaman_id[<?= $angIdx ?>]" value="<?= $pa['pinjaman_id'] ?>">
                            <input type="hidden" name="ang_cicilan_ke[<?= $angIdx ?>]"  value="<?= $pa['cicilan_ke'] ?>">
                        </td>
                        <td style="background:#fff;">
                            <span class="badge bg-primary">Cicilan <?= $pa['cicilan_ke'] ?>/<?= $pa['lama_tenor'] ?></span>
                            <div class="text-muted" style="font-size:0.75rem;">Sisa: <?= $pa['sisa_tenor'] ?> bulan</div>
                            <div class="form-check form-switch mt-1 mb-0" style="font-size:0.72rem;">
                                <input class="form-check-input chk-pelunasan" type="checkbox" role="switch"
                                       name="ang_is_pelunasan[<?= $angIdx ?>]" value="1" id="pelunasan_<?= $angIdx ?>"
                                       <?= $pa['is_last_installment'] ? 'checked' : '' ?>
                                       data-pokoknormal="<?= $pa['pokok_per_bulan'] ?>"
                                       data-jasanormal="<?= $pa['jasa_per_bulan'] ?>"
                                       data-pokoklunas="<?= $pa['sisa_pokok_lunas'] ?>"
                                       data-jasalunas="<?= $pa['sisa_jasa_lunas'] ?>"
                                       data-aidx="<?= $angIdx ?>">
                                <label class="form-check-label text-danger fw-bold" for="pelunasan_<?= $angIdx ?>" style="cursor:pointer;">Lunas?</label>
                            </div>
                        </td>
                        <!-- Pokok -->
                        <td style="background:#fff;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" style="font-size:0.75rem;">Rp</span>
                                <input type="text"
                                       name="ang_pokok[<?= $angIdx ?>]"
                                       id="ang_pokok_<?= $angIdx ?>"
                                       class="form-control text-end fmt-rupiah ang-pokok"
                                       value="<?= number_format($pa['is_last_installment'] ? $pa['sisa_pokok_lunas'] : $pa['pokok_per_bulan'], 0, ',', '.') ?>"
                                       data-aidx="<?= $angIdx ?>">
                            </div>
                            <small class="text-muted">Pokok <span id="lbl_pokok_<?= $angIdx ?>" class="text-danger fw-bold" style="display:<?= $pa['is_last_installment'] ? 'inline' : 'none' ?>;">(Lunas)</span></small>
                        </td>
                        <!-- Jasa -->
                        <td style="background:#fff;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" style="font-size:0.75rem;">Rp</span>
                                <input type="text"
                                       name="ang_jasa[<?= $angIdx ?>]"
                                       id="ang_jasa_<?= $angIdx ?>"
                                       class="form-control text-end fmt-rupiah ang-jasa"
                                       value="<?= number_format($pa['is_last_installment'] ? $pa['sisa_jasa_lunas'] : $pa['jasa_per_bulan'], 0, ',', '.') ?>"
                                       data-aidx="<?= $angIdx ?>">
                            </div>
                            <small class="text-muted">Jasa/Bunga <span id="lbl_jasa_<?= $angIdx ?>" class="text-danger fw-bold" style="display:<?= $pa['is_last_installment'] ? 'inline' : 'none' ?>;">(Lunas)</span></small>
                        </td>
                        <!-- Total per pinjaman -->
                        <td class="text-end fw-semibold ang-total" id="atotal-<?= $angIdx ?>" style="background:#fff; color:#3b82f6;">
                            Rp <?= number_format($pa['is_last_installment'] ? $pa['total_lunas'] : $pa['total_per_bulan'], 0, ',', '.') ?>
                        </td>
                        <td class="text-center" style="background:#fff;">
                            <span class="badge bg-warning text-dark">Angsuran</span>
                        </td>
                    </tr>
                    <?php $angIdx++; ?>
                    <?php endforeach; ?>

                    <?php endif; ?>
                    <?php $simpIdx++; ?>

                <?php endforeach; ?>
                </tbody>

                <!-- Grand total footer -->
                <tfoot style="background:#0f172a; color:#fff; font-weight:bold; font-size:0.9rem;">
                    <tr>
                        <td colspan="3" class="text-end">Grand Total:</td>
                        <td class="text-end" id="footSimpanan" style="color:#34d399;">Rp 0</td>
                        <td class="text-end" id="footJasa"     style="color:#93c5fd;">Rp 0</td>
                        <td class="text-end" id="footTotal"    style="color:#fbbf24;">Rp 0</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- ===== PREVIEW BUKU KAS UMUM (Live) ===== -->
    <div class="card mt-2 border-0" id="kasPreview"
         style="border-radius:10px; background:linear-gradient(135deg,#0f172a,#1e3a5f); color:#fff; position:sticky; bottom:12px; z-index:50; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
        <div class="card-body py-2 px-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div style="flex:1; min-width:240px;" class="kas-preview-left">
                    <div class="fw-bold" style="font-size:0.75rem; color:#94a3b8; letter-spacing:.5px; text-transform:uppercase; margin-bottom: 2px;">
                        <i class="fas fa-book-open me-1"></i> Preview Kas Umum (1 Baris)
                    </div>
                    <div id="kasKeterangan" style="font-size:0.8rem; color:#e2e8f0; line-height:1.4;">—</div>
                </div>
                <div class="text-end kas-preview-right" style="min-width:200px;">
                    <div class="label-total" style="font-size:0.68rem; color:#94a3b8; text-transform:uppercase; letter-spacing:.5px; margin-bottom: -2px;">Total Pemasukan Kas</div>
                    <div id="kasTotalPreview" style="font-size:1.35rem; font-weight:800; color:#34d399; letter-spacing:-0.5px; line-height:1.2;">Rp 0</div>
                    <div class="kas-preview-footer" style="font-size:0.7rem; color:#64748b;">
                        Simpanan: <span id="kasFootSimpanan" style="color:#86efac;">Rp 0</span><br class="d-md-none">&nbsp;+&nbsp;
                        Angsuran: <span id="kasFootAngsuran" style="color:#93c5fd;">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-3 mt-3 justify-content-end">
        <a href="<?= base_url('massal') ?>" class="btn btn-secondary px-4">
            <i class="fas fa-times me-1"></i> Batal
        </a>
        <?php if(is_premium()): ?>
        <button type="submit" class="btn btn-success btn-lg px-5">
            <i class="fas fa-paper-plane me-2"></i> Proses Pembayaran Massal
        </button>
        <?php else: ?>
        <a href="<?= base_url('informasi/support') ?>" class="btn btn-success btn-lg px-5">
            <i class="fas fa-crown text-warning me-2"></i> Proses Pembayaran Massal
            <span style="font-size:0.7rem; background:rgba(255,255,255,0.2); border-radius:10px; padding:2px 8px; margin-left:4px;">PRO</span>
        </a>
        <?php endif; ?>
    </div>
</form>

<?php endif; ?>

<script>
function parseNum(v) { return parseInt((v+'').replace(/\./g,'').replace(/[^0-9]/g,''))||0; }
function fmtRp(n)    { return n.toLocaleString('id-ID'); }

// Format on type
document.querySelectorAll('.fmt-rupiah').forEach(function(el) {
    el.addEventListener('input', function() {
        const raw = parseNum(this.value);
        this.value = fmtRp(raw);
        if (this.dataset.sidx !== undefined) recalcSimpanan(this.dataset.sidx);
        if (this.dataset.aidx !== undefined) recalcAngsuran(this.dataset.aidx);
        recalcFooter();
    });
});

function recalcSimpanan(sidx) {
    const chk = document.querySelector('[name="simpanan_include[' + sidx + ']"]');
    const amt  = parseNum(document.querySelector('[name="simpanan_amount[' + sidx + ']"]').value);
    const cell = document.getElementById('stotal-' + sidx);
    cell.innerText = (chk && chk.checked) ? 'Rp ' + fmtRp(amt) : '—';
}

function recalcAngsuran(aidx) {
    const chk   = document.querySelector('[name="ang_include[' + aidx + ']"]');
    const pokok = parseNum(document.querySelector('[name="ang_pokok[' + aidx + ']"]').value);
    const jasa  = parseNum(document.querySelector('[name="ang_jasa[' + aidx + ']"]').value);
    const cell  = document.getElementById('atotal-' + aidx);
    cell.innerText = (chk && chk.checked) ? 'Rp ' + fmtRp(pokok + jasa) : '—';
}

function recalcFooter() {
    let totSimpanan = 0, totJasa = 0, totPokok = 0;
    let cS = 0, cA = 0;

    document.querySelectorAll('.chk-simpanan:checked').forEach(function(chk) {
        const sidx = chk.name.match(/\d+/)[0];
        totSimpanan += parseNum(document.querySelector('[name="simpanan_amount[' + sidx + ']"]').value);
        cS++;
    });

    document.querySelectorAll('.chk-angsuran:checked').forEach(function(chk) {
        const aidx = chk.name.match(/\d+/)[0];
        totPokok += parseNum(document.querySelector('[name="ang_pokok[' + aidx + ']"]').value);
        totJasa  += parseNum(document.querySelector('[name="ang_jasa['  + aidx + ']"]').value);
        cA++;
    });

    const totAngsuran = totPokok + totJasa;
    const grandTotal  = totSimpanan + totAngsuran;

    // Update footer tabel
    document.getElementById('footSimpanan').innerText = 'Rp ' + fmtRp(totSimpanan);
    document.getElementById('footJasa').innerText     = 'Rp ' + fmtRp(totJasa);
    document.getElementById('footTotal').innerText    = 'Rp ' + fmtRp(grandTotal);

    // Update preview Buku Kas Umum
    const tgl     = document.querySelector('[name="tanggal"]').value;
    const klmpk   = document.querySelector('[name="kelompok"]').value;
    const bulanLbl = tgl ? new Date(tgl).toLocaleDateString('id-ID',{month:'long',year:'numeric'}) : '—';

    const ket = grandTotal > 0
        ? `Setoran Massal Potong Gaji Kelompok <strong>${klmpk}</strong> — ${bulanLbl} `
        + `(${cS} simpanan, ${cA} angsuran`
        + ` | Simpanan: Rp ${fmtRp(totSimpanan)}, Angsuran: Rp ${fmtRp(totAngsuran)})`
        : '<span style="color:#64748b;">— Belum ada baris yang dipilih —</span>';

    document.getElementById('kasKeterangan').innerHTML  = ket;
    document.getElementById('kasTotalPreview').innerText = 'Rp ' + fmtRp(grandTotal);
    document.getElementById('kasFootSimpanan').innerText = 'Rp ' + fmtRp(totSimpanan);
    document.getElementById('kasFootAngsuran').innerText = 'Rp ' + fmtRp(totAngsuran);
}

// Checkbox simpanan
document.querySelectorAll('.chk-simpanan').forEach(function(chk) {
    chk.addEventListener('change', function() {
        const sidx = this.name.match(/\d+/)[0];
        recalcSimpanan(sidx);
        recalcFooter();
    });
});

// Checkbox angsuran — dim row when unchecked
document.querySelectorAll('.chk-angsuran').forEach(function(chk) {
    chk.addEventListener('change', function() {
        const row  = this.closest('tr');
        const aidx = this.name.match(/\d+/)[0];
        row.classList.toggle('unchecked', !this.checked);
        recalcAngsuran(aidx);
        recalcFooter();
    });
});

// Checkbox Pelunasan
document.querySelectorAll('.chk-pelunasan').forEach(function(chk) {
    chk.addEventListener('change', function() {
        const aidx = this.dataset.aidx;
        const iPokok = document.getElementById('ang_pokok_' + aidx);
        const iJasa  = document.getElementById('ang_jasa_' + aidx);
        
        if (this.checked) {
            iPokok.value = fmtRp(this.dataset.pokoklunas);
            iJasa.value  = fmtRp(this.dataset.jasalunas);
            document.getElementById('lbl_pokok_' + aidx).style.display = 'inline';
            document.getElementById('lbl_jasa_' + aidx).style.display = 'inline';
        } else {
            iPokok.value = fmtRp(this.dataset.pokoknormal);
            iJasa.value  = fmtRp(this.dataset.jasanormal);
            document.getElementById('lbl_pokok_' + aidx).style.display = 'none';
            document.getElementById('lbl_jasa_' + aidx).style.display = 'none';
        }
        
        recalcAngsuran(aidx);
        recalcFooter();
    });
});

// Check All 
document.getElementById('checkAllSimpanan').addEventListener('change', function() {
    const isChecked = this.checked;
    // Toggle Simpanan
    document.querySelectorAll('.chk-simpanan').forEach(function(c) {
        c.checked = isChecked;
        recalcSimpanan(c.name.match(/\d+/)[0]);
    });
    // Toggle Angsuran
    document.querySelectorAll('.chk-angsuran').forEach(function(c) {
        c.checked = isChecked;
        const row  = c.closest('tr');
        row.classList.toggle('unchecked', !isChecked);
        recalcAngsuran(c.name.match(/\d+/)[0]);
    });
    recalcFooter();
});

// Konfirmasi submit
document.getElementById('formMassal').addEventListener('submit', function(e) {
    const cS = document.querySelectorAll('.chk-simpanan:checked').length;
    const cA = document.querySelectorAll('.chk-angsuran:checked').length;
    if (cS + cA === 0) { e.preventDefault(); alert('Pilih minimal 1 baris untuk diproses.'); return; }
    if (!confirm('Proses ' + cS + ' simpanan dan ' + cA + ' angsuran?\n\nGrand Total: ' + document.getElementById('footTotal').innerText)) {
        e.preventDefault();
    }
});

// Hitung awal
recalcFooter();
</script>

<?= $this->endSection() ?>
