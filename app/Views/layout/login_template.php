<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title; ?></title>
        <?= $this->include('layout/css_core'); ?>
    </head>
    <body>
        <div class="container">
            <?= $this->renderSection('content') ?>
        </div>
    </body>
    <?= $this->include('layout/js_core'); ?>
    <?= $this->include('layout/login_js'); ?>
</html>