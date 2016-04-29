<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午5:52
 */

class PageController extends Controller{
    public function _templateAction() {
        $config = isset($_GET['__config__']) ? $_GET['__config__'] : null;
        if (!isset($config)) throw new Wk_Exception("", -1);

        if (!empty($config['redirect'])) {
            Wk_Request::redirect($config['redirect']);
        }

        $needLogin = isset($config['needLogin']) ? $config['needLogin'] : 0;
        if (isset($needLogin) && $needLogin == 1 && !$this->isLogin()) {
            $this->redirectLogin();
        }

        $needLogout = isset($config['needLogout']) ? $config['needLogout'] : 0;
        if (isset($needLogout) && $needLogout == 1 && $this->isLogin()) {

            // throw new K_Exception('', TErrorConstants::E_LOGIN);
            Wk_Request::redirect("/");
        }

        $path = $config['path'];

        if (!empty($path)) {
            unset($_GET['__config__']);
            $content = $this->renderReleaseTemplate($path, true);
            echo $content;
        }
        Wk::app()->stop();
    }

    private function redirectLogin() {

    }
} 