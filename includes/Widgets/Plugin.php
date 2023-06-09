<?php

namespace Widgets;

use Custom\Notice;
use Custom\Widget;
use Custom\Plugin as CustomPlugin;
use Helpers\Renderer;

class Plugin extends Widget
{
    /**
     * 已启用插件列表
     *
     * @var array
     */
    private $plugins;

    public function init()
    {
        $this->plugins = CustomPlugin::export();
    }

    /**
     * 获取插件列表
     *
     * @return array
     */
    public function getPlugins()
    {
        User::alloc()->pass('administrator');

        $dirs = glob(ROOT_DIR . 'content/plugins/*/');

        $result = [];

        foreach ($dirs as $dir) {
            $name = basename($dir);
            $class = '\\Plugins\\' . $name . '\\Main';

            if (class_exists($class)) {
                $activated = array_key_exists($name, $this->plugins);
                $result[] = [
                    'name' => $name,
                    'url' => $class::$url ?? '',
                    'description' => $class::$description ?? '',
                    'version' => $class::$version ?? '',
                    'author' => $class::$author ?? '',
                    'authorUrl' => $class::$authorUrl ?? '',
                    'activated' => $activated,
                    'hasConfig' =>  $activated && count($this->plugins[$name]['config']) > 0,
                ];
            }
        }

        return $result;
    }

    /**
     * 启用插件
     *
     * @return void
     */
    private function enable($reset = false)
    {
        User::alloc()->pass('administrator');

        $name = $this->request->get('name', '');

        // 如果是重新安装，则先卸载
        if ($reset) {
            $this->disable(true);
        }

        if (array_key_exists($name, $this->plugins)) {
            Notice::set(['请勿重复安装'], 'warning');
            $this->response->goBack();
        }

        $class = '\\Plugins\\' . $name . '\\Main';

        if (!class_exists($class) || !method_exists($class, 'activation')) {
            if ($reset) {
                Notice::set(['重新安装失败'], 'warning');
            } else {
                Notice::set(['安装失败'], 'warning');
            }

            $this->response->goBack();
        }

        // 获取插件设置
        if (class_exists($class, 'config')) {
            $renderer = new Renderer();
            call_user_func([$class, 'config'], $renderer);

            $config = $renderer->getValues();
        }

        // 激活插件
        call_user_func([$class, 'activation']);
        CustomPlugin::activation($name, $config);

        Option::alloc()->set('plugin', serialize(CustomPlugin::export()));

        if ($reset) {
            Notice::set(['重新安装成功'], 'success');
        } else {
            Notice::set(['安装成功'], 'success');
        }
        $this->response->goBack();
    }

    /**
     * 禁用插件
     *
     * @return void
     */
    private function disable($reset = false)
    {
        User::alloc()->pass('administrator');

        $name = $this->request->get('name', '');

        if (!array_key_exists($name, $this->plugins)) {
            if (!$reset) {
                Notice::set(['请勿重复卸载'], 'warning');
                $this->response->goBack();
            } else {
                return;
            }
        }

        $class = '\\Plugins\\' . $name . '\\Main';

        // 执行卸载回调
        if (class_exists($class) && method_exists($class, 'deactivation')) {
            call_user_func([$class, 'deactivation']);
        }

        CustomPlugin::deactivation($name);

        Option::alloc()->set('plugin', serialize(CustomPlugin::export()));

        if (!$reset) {
            Notice::set(['卸载成功'], 'success');
            $this->response->goBack();
        } else {
            // 更新启用插件列表
            $this->plugins = CustomPlugin::export();
        }
    }

    /**
     * 插件配置
     *
     * @return void
     */
    public function config()
    {
        User::alloc()->pass('administrator');

        $name = $this->request->get('name', '');
        $class = '\\Plugins\\' . $name  . '\\Main';
        if (!array_key_exists($name, $this->plugins)) {
            Notice::set(['插件未启用'], 'warning');
            $this->response->goBack();
        }

        // 判断插件是否具备配置功能
        if ([] === $this->plugins[$name]['config']) {
            Notice::set(['配置功能不存在'], 'warning');
            $this->response->goBack();
        }

        $renderer = new Renderer();
        call_user_func([$class, 'config'], $renderer);
        $renderer->render($this->plugins[$name]['config']);
    }

    public function updateConfig()
    {
        User::alloc()->pass('administrator');

        $name = $this->request->post('name', '');

        if (null === $name) {
            Notice::set(['插件名不能为空'], 'warning');
            $this->response->goBack();
        }

        if (!array_key_exists($name, $this->plugins)) {
            Notice::set(['插件未启用'], 'warning');
            $this->response->goBack();
        }

        $data = $this->request->post();

        CustomPlugin::updateConfig($name, $data);

        Option::alloc()->set('plugin', serialize(CustomPlugin::export()));

        Notice::set(['更新成功'], 'success');
        $this->response->goBack();
    }

    public function action()
    {
        // 安装插件
        $this->on($this->params(0) === 'enable')->enable();
        $this->on($this->params(0) === 'reset')->enable(true);
        // 卸载插件
        $this->on($this->params(0) === 'disable')->disable();
        // 更新插件配置
        $this->on($this->params(0) === 'update-config')->updateConfig();
    }
}
