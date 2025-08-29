<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
    <div class="card">
        <?php if (isset($status) && $status === 'success'): ?>
            <div class="card-header d-flex flex-row justify-content-between align-items-center
                text-bg-success bg-gradient">
                <h4 class="mb-0">
                    <i class="fa-solid fa-server"></i>
                    <span>Test Koneksi Database</span>
                </h4>
                <strong>
                    <i class="fa-solid fa-plug me-1"></i>
                    <span>Database telah terkoneksi</span>
                </strong>
            </div>
        <?php else: ?>
            <div class="card-header d-flex flex-row justify-content-between align-items-center
                text-bg-danger bg-gradient">
                <h4 class="mb-0">
                    <i class="fa-solid fa-server"></i>
                    <span>Test Koneksi Database</span>
                </h4>
                <strong>
                    <i class="fa-solid fa-plug-circle-xmark me-1"></i>
                    <span>Database tidak terkoneksi</span>
                </strong>
            </div>
        <?php endif; ?>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered m-0">
                    <thead>
                        <th>Username</th>
                        <th>Nama Database</th>
                        <th>Table Database</th>
                    </thead>
                    <tbody>
                        <?php if (isset($status) && $status === 'success'): ?>
                            <tr>
                                <td><?= esc($username) ?></td>
                                <td><?= esc($database) ?></td>
                                <td>
                                    <?php foreach ($rows as $row): ?>
                                        <?php foreach ($row as $col): ?>
                                            <h6 class="m-0"><?= esc($col) ?></span>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td><?= esc($username) ?></td>
                                <td><?= esc($database) ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>
