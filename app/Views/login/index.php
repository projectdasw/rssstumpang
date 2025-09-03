<?= $this->extend('layout/login_template'); ?>
<?= $this->section('content'); ?>
    <div class="container d-flex flex-row justify-content-center align-items-center vh-100">
        <form class="card needs-validation" action="<?= base_url('login/processLogin'); ?>" method="post" autocomplete="off" style="width: 35%;" novalidate>
            <?= csrf_field(); ?>
            <div class="card-header">
                <h6 class="m-0">Login - RSSS Tumpang</h6>
            </div>
            <div class="card-body">
                <div class="input-group mb-2">
                    <span class="input-group-text">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" class="form-control" name="username" placeholder="Username Anda"
                        aria-label="Username Anda" required>
                    <div class="invalid-feedback">
                        Masukkan Username Anda
                    </div>
                </div>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa-solid fa-key"></i>
                    </span>
                    <input type="password" class="form-control" name="password" id="passwordReveal" placeholder="Password Anda"
                        aria-label="Password Anda" required>
                    <button class="btn btn-primary" type="button" id="togglePassword">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    <div class="invalid-feedback">
                        Masukkan Password Anda
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Login</span>
                </button>
            </div>
        </form>
    </div>
<?= $this->endSection(); ?>