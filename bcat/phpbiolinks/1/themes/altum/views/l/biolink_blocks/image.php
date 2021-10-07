<?php defined('ALTUMCODE') || die() ?>

<div data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <?php if($data->link->location_url): ?>
    <a href="<?= $data->link->location_url . $data->link->utm_query ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" target="_blank">
        <img src="data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?= mb_substr($data->link->settings->image, 0, 4) === "http" ? $data->link->settings->image : SITE_URL . UPLOADS_URL_PATH . 'block_images/' . $data->link->settings->image ?>" class="img-fluid rounded bc_lazyload" />
    </a>
    <?php else: ?>
    <img src="data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?= mb_substr($data->link->settings->image, 0, 4) === "http" ? $data->link->settings->image : SITE_URL . UPLOADS_URL_PATH . 'block_images/' . $data->link->settings->image ?>" class="img-fluid rounded bc_lazyload" />
    <?php endif ?>
</div>

