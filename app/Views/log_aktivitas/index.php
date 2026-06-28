<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-history me-2"></i> Log Aktivitas Sistem</h1>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius:12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3" style="width:180px;">Waktu</th>
                        <th class="px-4 py-3">Pengguna</th>
                        <th class="px-4 py-3">Aktivitas</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 text-end">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada log aktivitas terekam.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($logs as $log): ?>
                        <tr>
                            <td class="px-4 py-3" style="font-size:0.85rem; color:#64748b;">
                                <?= date('d M Y, H:i', strtotime($log['created_at'])) ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($log['user_id'] == 0): ?>
                                    <span class="badge bg-secondary">System</span>
                                <?php else: ?>
                                    <div class="fw-semibold text-dark"><?= esc($log['username']) ?></div>
                                    <div style="font-size:0.75rem; color:#64748b;"><?= esc(ucfirst($log['role'])) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 fw-medium text-primary">
                                <?= esc($log['aktivitas']) ?>
                            </td>
                            <td class="px-4 py-3" style="font-size:0.9rem;">
                                <?= esc($log['keterangan']) ?>
                            </td>
                            <td class="px-4 py-3 text-end" style="font-size:0.8rem; font-family:monospace;">
                                <?= esc($log['ip_address']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
