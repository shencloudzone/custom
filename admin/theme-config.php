<?php require __DIR__ . '/modules/init.php'; ?>
<?php require __DIR__ . '/modules/header.php'; ?>
<?php require __DIR__ . '/modules/navbar.php'; ?>
<div class="main container">
    <div class="my-3 d-flex align-items-baseline">
        <h3 class="me-2"><?= _t('主题设置') ?></h3>
        <a href="<?= base_url('/admin/theme.php'); ?>">返回</a>
    </div>
    <form class="form" action="<?= site_url('/theme/update-config') ?>" method="post">
        <input name="name" type="text" hidden value="<?= $request->get('name') ?>">
        <?php \Widgets\Theme::alloc()->config() ?>
        <button class="btn btn-primary" type="submit">保存设置</button>
    </form>
</div>
<?php require __DIR__ . '/modules/footer.php'; ?>
