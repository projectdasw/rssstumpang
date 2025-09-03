<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
    <a href="<?= site_url('logout') ?>" class="btn btn-danger">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
    <?= $this->include('layout/js_core'); ?>
    <?= $this->include('layout/dashboard_js'); ?>
<?= $this->endSection(); ?>