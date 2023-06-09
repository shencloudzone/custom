<?php

namespace Plugins\CustomPage;

use Helpers\Renderer;

class Main
{
    public static $url = "";
    public static $description = "自定义页面";
    public static $version = "1.0";
    public static $author = "Noah Zhang";
    public static $authorUrl = "";

    /**
     * 激活插件，注册钩子并执行初始化操作
     *
     * @return void
     */
    public static function activation()
    {
        \Custom\Plugin::factory('admin/navbar.php')->navEnd = __CLASS__ . '::renderNavEnd';
        \Custom\Plugin::factory('includes/Widgets/Hook.php')->action = __CLASS__ . '::action';
    }

    /**
     * 卸载回调
     *
     * @return void
     */
    public static function deactivation()
    {
    }

    /**
     * 插件设置
     *
     * @param Renderer $renderer 渲染器
     * @return void
     */
    public static function config($renderer)
    {
        // $renderer->setValue('message', 'HelloWorld!');

        // $renderer->setTemplate(function ($data) {
        //     include __DIR__ . '/config.php';
        // });
    }

    public static function renderNavEnd($config)
    {
        include __DIR__ . '/views/nav-end.php';
    }

    public static function renderContent($config)
    {
        include __DIR__ . '/views/content.php';
    }

    public static function action($config, $widget)
    {
        if ($widget->params(0) === 'custom') {
            include __DIR__ . '/views/page.php';
        }
    }
}
