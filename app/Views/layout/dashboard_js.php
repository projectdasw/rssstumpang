<script>
    // Cegah back ke halaman sebelumnya
    if (window.history.replaceState) {
        // replaceState: hapus state terakhir, jadi back tidak kembali ke halaman login/dashboard lama
        window.history.replaceState(null, null, window.location.href);
    }

    // Saat user tekan tombol back, langsung maju lagi
    window.onpopstate = function () {
        window.history.go(1);
    };
</script>

<!-- SweetAlert2 Handler -->
<?php if (session()->getFlashdata('error')): ?>
    <script>
        Toast.fire({
            icon: 'error',
            title: '<?= session()->getFlashdata('error') ?>'
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <script>
        Toast.fire({
            icon: 'success',
            title: '<?= session()->getFlashdata('success') ?>'
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('info')): ?>
    <script>
    Toast.fire({
        icon: 'info',
        title: '<?= session()->getFlashdata('info') ?>'
    });
    </script>
<?php endif; ?>