<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午5:52
 */

class Controller extends Wk_WebController{

    protected function access() {
        return [];
    }

    public $curUser;
    public $curToken;

    public function beforeAction() {
        parent::beforeAction();
        $this->authWeb();

        $webUser = new Wk_WebUser();
        if (!empty($this->curUser)) {
            $webUser->utoken = $this->curToken;
            $webUser->userid = $this->curUser->userid;
        }
        Wk::app()->user = $webUser;

        $access = $this->access();
        if (!empty($access['@']) && in_array($this->actionName, $access['@']) && !$this->isLogin()) {
            throw new Wk_Exception('', TErrorConstants::E_NOT_LOGIN);
        }
        if (!empty($access['?']) && in_array($this->actionName, $access['?']) && $this->isLogin()) {
            throw new Wk_Exception('', TErrorConstants::E_LOGIN);
        }

        // deal with wx token leak bug
//        if ($this->getClientType() == 'wx' && AppIdentity::getInstance()->isApp()) {
//            unset($_SESSION['appParam']);
//            AppIdentity::getInstance()->init('','tweb','','','');
//            $_SESSION['appParam'] = AppIdentity::getInstance()->toParamArr();
//        }
    }

    private function authWeb() {
        try {
            Wk_Request::startSession(WAKA_DOMAIN);
            if(isset($_SESSION['appParam'])) {
                $token = $_SESSION['appParam']['token'];
                $retUser = WkUserService::getInstance()->getUserByToken($token);
                $this->curUser = $retUser;
            }
            else {
                $token = '';

            }

            $this->curToken = $token;
            if (isset($this->curUser)) {
                WkUserService::getInstance()->setLoginCookie($this->curUser);
            } else {
                unset($_COOKIE['WAKAUID']);
                setcookie('WAKAUID', '', time() - 3600, '/', WAKA_DOMAIN);
            }
        } catch (Exception $e) {
            Wk::logger()->err($e);
            throw new Wk_Exception('', -1);
        }
    }

    public function isLogin() {
        return isset($this->curUser);
    }

    public function isGuest() {
        return !isset($this->curUser);
    }

    public function renderAjax($obj = null) {
        $json = [
            'ok' => 1
        ];
        if (isset($obj)) {
            $json['obj'] = $obj;
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($json, JSON_UNESCAPED_UNICODE);
        Wk::app()->stop();
    }
}