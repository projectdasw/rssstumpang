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