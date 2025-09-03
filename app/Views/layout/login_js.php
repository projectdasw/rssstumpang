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

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('passwordReveal');
        const icon = this.querySelector('i');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>