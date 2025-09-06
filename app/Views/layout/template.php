<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title; ?></title>
        <?= $this->include('layout/css_core'); ?>
    </head>
    <body>
        <!-- Modal -->
         <?= $this->include('layout/modal/modal_setting_user.php'); ?>
        <div class="container-fluid d-flex flex-row p-0">
            <div class="col-2 bg-white vh-100 border border-1">
                <div class="card border-primary m-2">
                    <div class="card-header text-center border-primary bg-transparent">
                        <h6 class="m-0 text-primary"><?= session()->get('username'); ?></h6>
                    </div>
                    <div class="card-body text-center text-primary">
                        <img src="<?= base_url('img/user-icon.webp'); ?>" class="card-img-top w-50" alt="user-icon">
                        <h5 class="card-title m-0 mt-2"><?= session()->get('nama_lengkap'); ?></h5>
                    </div>
                    <div class="card-footer text-center border-primary bg-transparent">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-success"
                                tooltip-toggle="tooltip" data-bs-title="Pengaturan Akun"
                                data-bs-toggle="modal" data-bs-target="#exampleModal">
                                <i class="fa-solid fa-user-gear"></i>
                            </button>
                            <button type="button" class="btn btn-danger"
                                tooltip-toggle="tooltip" data-bs-title="Logout">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="list-group">
                    <button type="button" class="list-group-item list-group-item-action list-group-item-success p-1"
                        data-bs-toggle="collapse" data-bs-target="#collapseExample"
                        aria-expanded="false" aria-controls="collapseExample">
                        <div class="d-flex flex-row align-items-center">
                            <i class="fa-solid fa-cog fa-fw me-1"></i>
                            <small>Pengaturan Website</small>
                        </div>
                    </button>
                    <div class="collapse" id="collapseExample">
                        <div class="card card-body">
                            Some placeholder content for the collapse component. This panel is hidden by default but revealed when the user activates the relevant trigger.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-10 h-100 bg-white border border-1">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </body>
</html>