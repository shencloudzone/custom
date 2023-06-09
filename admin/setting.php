<?php require __DIR__ . '/modules/init.php'; ?>
<?php require __DIR__ . '/modules/header.php'; ?>
<?php require __DIR__ . '/modules/navbar.php'; ?>
<div class="main container">
    <h3 class="my-3"><?= _t('站点设置') ?></h3>
    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#site-setting" type="button">站点设置</button>
        </li>
        <?php \Custom\Plugin::factory('admin/setting.php')->tab() ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="site-setting">
            <form class="form" action="<?= site_url('/option/update-setting') ?>" method="post">
                <div class="mb-3">
                    <label class="form-label" for="title">站点名称</label>
                    <input class="form-control" id="title" type="text" name="title" value="<?= $option->get('title'); ?>" placeholder="">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="description">站点描述</label>
                    <input class="form-control" id="description" type="text" name="description" value="<?= $option->get('description'); ?>" placeholder="">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="language">语言</label>
                    <select class="form-select" id="language" name="language">
                        <?php foreach (\Widgets\Language::alloc()->getLanguages() as $lang) : ?>
                            <option value="<?= $lang['value'] ?>" <?= $option->get('language') == $lang['value'] ? 'selected' : '' ?>><?= $lang['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="allowRegister">是否允许注册</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" id="allowRegister" type="checkbox" name="allowRegister" value="1" <?= $option->get('allowRegister') === '1'  ? 'checked' : '' ?>>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">保存</button>
            </form>
        </div>
        <?php \Custom\Plugin::factory('admin/setting.php')->content() ?>
    </div>
</div>
<?php require __DIR__ . '/modules/footer.php'; ?>
