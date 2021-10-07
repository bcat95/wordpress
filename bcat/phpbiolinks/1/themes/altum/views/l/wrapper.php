<?php defined('ALTUMCODE') || die() ?>
<!DOCTYPE html>
<html lang="<?= language()->language_code ?>" class="link-html">
    <head>
        <title><?= !empty($this->link->settings->seo->title) ? $this->link->settings->seo->title : \Altum\Title::get() ?></title>
        <base href="<?= SITE_URL; ?>">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <?php if(\Altum\Meta::$description): ?>
            <meta name="description" content="<?= \Altum\Meta::$description ?>" />
        <?php endif ?>

        <?php if(\Altum\Meta::$open_graph['url']): ?>
            <!-- Open Graph / Facebook / Twitter -->
            <?php foreach(\Altum\Meta::$open_graph as $key => $value): ?>
                <?php if($value): ?>
                    <meta property="og:<?= $key ?>" content="<?= $value ?>" />
                    <meta property="twitter:<?= $key ?>" content="<?= $value ?>" />
                <?php endif ?>
            <?php endforeach ?>
        <?php endif ?>

        <?php if($this->link->settings->seo->block): ?>
            <meta name="robots" content="noindex">
        <?php endif ?>

        <?php if(!empty(settings()->favicon)): ?>
            <link href="<?= SITE_URL . UPLOADS_URL_PATH . 'favicon/' . settings()->favicon ?>" rel="shortcut icon" />
        <?php endif ?>

        <?php if(!$this->link->settings->font): ?>
            <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <?php endif ?>

        <?php foreach(['bootstrap.min.css', 'custom.css', 'link-custom.css', 'animate.min.css'] as $file): ?>
            <link href="<?= SITE_URL . ASSETS_URL_PATH . 'css/' . $file . '?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen">
        <?php endforeach ?>

        <?php if($this->link->settings->font): ?>
            <?php $biolink_fonts = require APP_PATH . 'includes/biolink_fonts.php' ?>
            <link href="https://fonts.googleapis.com/css?family=<?= $biolink_fonts[$this->link->settings->font]['font-family'] ?>&display=swap" rel="stylesheet">
            <style>
                body {
                    font-family: '<?= $biolink_fonts[$this->link->settings->font]['name'] ?>', sans-serif !important;
                }
            </style>
        <?php endif ?>

        <?= \Altum\Event::get_content('head') ?>

        <?php if(!empty(settings()->custom->head_js)): ?>
            <?= settings()->custom->head_js ?>
        <?php endif ?>

        <link rel="canonical" href="<?= $this->link->full_url ?>" />
    </head>

    <?= $this->views['content'] ?>

    <?php require THEME_PATH . 'views/partials/js_global_variables.php' ?>

    <?php foreach(['libraries/jquery.min.js', 'libraries/popper.min.js', 'libraries/bootstrap.min.js', 'lazysizes.min.js', 'main.js', 'functions.js', 'libraries/fontawesome.min.js'] as $file): ?>
        <script src="<?= SITE_URL . ASSETS_URL_PATH ?>js/<?= $file ?>?v=<?= PRODUCT_CODE ?>"></script>
    <?php endforeach ?>

    <script type="text/javascript">
        function bc_lazyload(){
            $(".bc_lazyload:not(.lazyload)").each(function() {
                $(this).addClass('lazyload');
            });
        }
        $(window).on("load",function(){
            bc_lazyload();
        });
    </script>

    <?= \Altum\Event::get_content('javascript') ?>
</html>
