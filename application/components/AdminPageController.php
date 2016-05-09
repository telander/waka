<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/26
 * Time: 上午11:39
 */

class AdminPageController extends AdminBaseController {
    protected function access() {
        return ['*' => ['_template']];
    }

    public function _templateAction() {
        if ($this->isGuest() && strpos($_SERVER['REQUEST_URI'], '/admin/login') !== 0) {
            Wk_Request::redirect('/admin/login');
        }
        if ($this->isLogin() && strpos($_SERVER['REQUEST_URI'], '/admin/login') === 0) {
            Wk_Request::redirect('/admin');
        }
        $config = isset($_GET['__config__'])?$_GET['__config__']:null;
        if(!isset($config)) throw new Wk_Exception("", -1);

        $path = $config['path'];
        if(!empty($path)) {
            unset($_GET['__config__']);
            $content = $this->renderReleaseTemplate($path,true);
            echo $content;
        }
        Wk::app()->stop();
    }
} 