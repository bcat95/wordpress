<?php defined('ALTUMCODE') || die() ?>

<input type="hidden" name="link_base" value="<?= $this->link->domain ? $this->link->domain->url : url() ?>" />

<header class="mb-4">
    <div class="container">

        <nav aria-label="breadcrumb">
            <small>
                <ol class="custom-breadcrumbs">
                    <li><a href="<?= url('dashboard') ?>"><?= language()->dashboard->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                    <li><a href="<?= url('links') ?>"><?= language()->links->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                    <?php if($data->link->type == 'biolink'): ?>

                        <?php if($data->method == 'settings'): ?>
                            <li class="active" aria-current="page">
                                <?= language()->link->breadcrumb_biolink . ' ' . language()->link->settings->breadcrumb ?>
                            </li>
                        <?php elseif($data->method == 'statistics'): ?>
                            <li class="active" aria-current="page">
                                <?= language()->link->breadcrumb_biolink . ' ' . language()->link->statistics->breadcrumb ?>
                            </li>
                        <?php endif ?>

                    <?php elseif($data->link->type == 'link'): ?>

                        <?php if($data->method == 'settings'): ?>
                            <li class="active" aria-current="page">
                                <?= language()->link->breadcrumb_link . ' ' . language()->link->settings->breadcrumb ?>
                            </li>
                        <?php elseif($data->method == 'statistics'): ?>
                            <li class="active" aria-current="page">
                                <?= language()->link->breadcrumb_link . ' ' . language()->link->statistics->breadcrumb ?>
                            </li>
                        <?php endif ?>

                    <?php endif ?>
                </ol>
            </small>
        </nav>

        <div class="d-flex flex-column flex-md-row justify-content-between">
            <div class="d-flex align-items-center">
                <h1 id="link_url" class="h3 mr-3 mb-0"><?= sprintf(language()->link->header->header, $data->link->url) ?></h1>

                <div class="custom-control custom-switch mr-3" data-toggle="tooltip" title="<?= language()->links->is_enabled_tooltip ?>">
                    <input
                            type="checkbox"
                            class="custom-control-input"
                            id="link_is_enabled_<?= $data->link->link_id ?>"
                            data-row-id="<?= $data->link->link_id ?>"
                            onchange="ajax_call_helper(event, 'link-ajax', 'is_enabled_toggle')"
                        <?= $data->link->is_enabled ? 'checked="checked"' : null ?>
                    >
                    <label class="custom-control-label clickable" for="link_is_enabled_<?= $data->link->link_id ?>"></label>
                </div>

                <div class="dropdown">
                    <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                        <i class="fa fa-ellipsis-v"></i>

                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="<?= url('link/' . $data->link->link_id) ?>" class="dropdown-item"><i class="fa fa-fw fa-pencil-alt"></i> <?= language()->global->edit ?></a>
                            <a href="<?= url('link/' . $data->link->link_id . '/statistics') ?>" class="dropdown-item"><i class="fa fa-fw fa-chart-bar"></i> <?= language()->link->statistics->link ?></a>
                            <a href="<?= $data->link->full_url . '?export=qr' ?>" target="_blank" class="dropdown-item" rel="noreferrer"><i class="fa fa-fw fa-qrcode"></i> <?= language()->link->qr->link ?></a>
                            <a href="#" data-toggle="modal" data-target="#link_delete" class="dropdown-item" data-link-id="<?= $data->link->link_id ?>"><i class="fa fa-fw fa-times"></i> <?= language()->global->delete ?></a>
                        </div>
                    </a>
                </div>
            </div>

            <div class="d-none d-md-block">
                <?php if($data->method != 'statistics'): ?>
                <a href="<?= url('link/' . $data->link->link_id . '/statistics') ?>" class="btn btn-light mr-3"><i class="fa fa-fw fa-sm fa-chart-bar"></i> <?= language()->link->statistics->link ?></a>
                <?php endif ?>

                <?php if($data->method != 'settings'): ?>
                <a href="<?= url('link/' . $data->link->link_id . '/settings') ?>" class="btn btn-light mr-3"><i class="fa fa-fw fa-sm fa-cog"></i> <?= language()->link->settings->link ?></a>
                <?php endif ?>
            </div>
        </div>

        <div class="d-flex align-items-baseline">
            <span class="mr-1" data-toggle="tooltip" title="<?= language()->link->{$data->link->type}->name ?>">
                <i class="fa fa-fw fa-circle fa-sm" style="color: <?= language()->link->{$data->link->type}->color ?>"></i>
            </span>
            <div class="col-md-auto text-muted text-truncate pl-0">
                <?= sprintf(language()->link->header->subheader, '<a id="link_full_url" href="' . $data->link->full_url . '" target="_blank" rel="noreferrer">' . $data->link->full_url . '</a>') ?>
            </div>

            <button
                    id="link_full_url_copy"
                    type="button"
                    class="btn btn-link"
                    data-toggle="tooltip"
                    title="<?= language()->global->clipboard_copy ?>"
                    aria-label="<?= language()->global->clipboard_copy ?>"
                    data-copy="<?= language()->global->clipboard_copy ?>"
                    data-copied="<?= language()->global->clipboard_copied ?>"
                    data-clipboard-text="<?= $data->link->full_url ?>"
            >
                <i class="fa fa-fw fa-sm fa-copy"></i>
            </button>
        </div>
    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?= \Altum\Alerts::output_alerts() ?>

    <?= $this->views['method'] ?>

</section>

<?php ob_start() ?>
<link href="<?= SITE_URL . ASSETS_URL_PATH . 'css/pickr.min.css' ?>" rel="stylesheet" media="screen">
<link href="<?= SITE_URL . ASSETS_URL_PATH . 'css/daterangepicker.min.css' ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>


<script>
    let clipboard = new ClipboardJS('[data-clipboard-text]');

    /* Copy full url handler */
    $('#link_full_url_copy').on('click', event => {
        let copy = $(event.currentTarget).data('copy');
        let copied = $(event.currentTarget).data('copied');

        $(event.currentTarget).attr('data-original-title', copied).tooltip('show');

        setTimeout(() => {
            $(event.currentTarget).attr('data-original-title', copy);
        }, 500);
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
