<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/26
 * Time: 上午11:31
 */

class AdminBaseController extends Wk_WebController{
    protected function access() {
        return [];
    }

    public $curUser;

    public function beforeAction() {
        parent::beforeAction();
        Wk_Request::startSession(WAKA_DOMAIN);
        if (isset($_SESSION['user'])) $this->curUser = $_SESSION['user'];
        if (isset($this->curUser)) {
            Wk::app()->user = new Wk_WebUser();
            Wk::app()->user->userid = $this->curUser->id;
            Wk::app()->user->utoken = "";
        }

        if (isset($this->curUser)) {
            WkAdminUserService::getInstance()->setLoginCookie($this->curUser);
        } else {
            unset($_COOKIE['WAKAUID']);
            unset($_COOKIE['WAKAUMB']);
            setcookie('WAKAUID', '', time() - 3600, '/', WAKA_DOMAIN);
            setcookie('WAKAUMB', '', time() - 3600, '/', WAKA_DOMAIN);
        }

        $access = $this->access();
        if (!empty($access['?']) && in_array($this->actionName, $access['?'])) {
            if ($this->isLogin()) {
                throw new Wk_Exception('', TErrorConstants::E_LOGIN);
            }
        } elseif (!empty($access['*']) && in_array($this->actionName, $access['*'])) {

        } elseif ($this->isGuest()) {
            throw new Wk_Exception('', TErrorConstants::E_NOT_LOGIN);
        }
    }

    public function isLogin() {
        return isset($this->curUser);
    }

    public function isGuest() {
        return !isset($this->curUser);
    }
} 