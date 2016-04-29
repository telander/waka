<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午4:31
 */

class Wk_WebController extends Wk_Controller {
    final public function run($actionName) {
        $controllerName = str_replace('Controller','',get_class($this));
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
        $actionMethod = $actionName . 'Action';
        if (method_exists($this, $actionMethod)) {
            try {
                $this->beforeAction();
                try {
                    $response = $this->$actionMethod();
                    if (isset($response)) {
                        $this->renderAjax($response);
                    } else {
                        $this->renderAjax();
                    }
                } catch (Exception $e) {
                    if ($e instanceof Wk_HttpException) {
                        $this->returnError($e->getMessage(),
                            $e->getCode(),
                            $e->getStatus());
                    } else if ($e instanceof Wk_Exception) {
                        $this->returnError($e->getMessage(), $e->getCode());
                    } else {
                        Wk::logger()->err($e);
                        $this->returnError($e->getMessage(),
                            $e->getCode(),
                            500);
                    }
                }
            } catch (Exception $e) {
                if ($e instanceof Wk_HttpException) {
                    $this->returnError($e->getMessage(),
                        $e->getCode(),
                        $e->getStatus());
                } else if ($e instanceof Wk_Exception) {
                    $this->returnError($e->getMessage(),
                        $e->getCode());
                } else {
                    Wk::logger()->err($e);
                    $this->returnError($e->getMessage(),
                        $e->getCode(),
                        500);
                }
            }
        } else {
            $this->returnError('404 not found', -1, 404);
        }
    }

    /**
     * @param string $_file_
     * @param array $_data_
     * @param bool $_return_
     * @return string|void
     */
    public function renderFile($_file_, array $_data_ = null, $_return_ = false) {
        if (!empty($_data_)) {
            extract($_data_, EXTR_PREFIX_SAME, 'data');
        }
        if ($_return_) {
            ob_start();
            ob_implicit_flush(false);
            /** @noinspection PhpIncludeInspection */
            include $_file_;
            return ob_get_clean();
        } else {
            /** @noinspection PhpIncludeInspection */
            include $_file_;
            //Wk::app()->stop();
        }
    }

    /**
     * @param string $_viewName_
     * @param array $_data_
     * @param bool $_return_
     * @return string|void
     */
    public function renderView($_viewName_, array $_data_ = null, $_return_ = false) {
        if (empty(Wk::$config['viewPath'])) Wk::$config['viewPath'] = APP_ROOT . '/views';
        $_file_ = Wk::$config['viewPath'] . $_viewName_ . '.php';
        if ($_return_) {
            return $this->renderFile($_file_, $_data_, $_return_);
        }
        $this->renderFile($_file_, $_data_, $_return_);
        Wk::app()->stop();
    }

    /**
     * @param string $_viewName_
     * @param array $_data_
     * @param bool $_return_
     * @return string|void
     */
    public function renderPartial($_viewName_, array $_data_ = null, $_return_ = false) {
        if (empty(Wk::$config['viewPath'])) Wk::$config['viewPath'] = APP_ROOT . '/views';
        $_file_ = Wk::$config['viewPath'] . $_viewName_ . '.php';
        if ($_return_) {
            return $this->renderFile($_file_, $_data_, $_return_);
        }
        $this->renderFile($_file_, $_data_, $_return_);
    }


    /**
     * @param string $_templateName_
     * @param bool $_return_
     * @return string|void
     */
    public function renderTemplate($_templateName_, $_return_ = false) {
        if (empty(Wk::$config['templatePath'])) Wk::$config['templatePath'] = APP_ROOT . '/../public/static/template';
        $_file_ = Wk::$config['templatePath'] . $_templateName_ . '/page.html';
        if ($_return_) {
            return $this->renderFile($_file_, null, $_return_);
        }
        $this->renderFile($_file_, null, $_return_);
    }

    public function renderReleaseTemplate($_templateName_, $_return_ = false) {
        if (empty(Wk::$config['releaseTemplatePath'])) Wk::$config['releaseTemplatePath'] = APP_ROOT . '/../public/static/release';
        $_file_ = Wk::$config['releaseTemplatePath'] . $_templateName_ . '/page.html';
        if ($_return_) {
            return $this->renderFile($_file_, null, $_return_);
        }
        $this->renderFile($_file_, null, $_return_);
    }


    public function renderAjax($value = null) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($value, JSON_UNESCAPED_UNICODE);
        Wk::app()->stop();
    }

    public function controllerPath() {
        return APP_ROOT . '/controllers';
    }

    /**
     * 返回错误信息
     *
     * @param  string $errorMsg
     * @param  int $errorCode
     * @param  int $httpStatus
     */
    public function returnError($errorMsg = '', $errorCode = -1, $httpStatus = 200) {
        if ($httpStatus !== 200) {
            switch ($httpStatus) {
                case 404:
                    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
                    echo '404 Not Found';
                    break;
                case 403:
                    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
                    echo '403 Forbidden';
                    break;
                case 500:
                    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
                    echo '500 Internal Server Error';
                    break;

                default:
                    header($_SERVER['SERVER_PROTOCOL'] . ' ' . $httpStatus . ' Http Error');
                    echo $httpStatus . ' Http Error';
                    break;
            }
        } elseif (Wk_Request::isAjax()) {
            header('Content-Type: application/json; charset=utf-8');
            if (!empty($errorMsg)) {
                echo json_encode(['ok' => 0, 'msg' => $errorMsg, 'code' => $errorCode], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['ok' => 0, 'msg' => TErrorConstants::getErrorMsg($errorCode), 'code' => $errorCode], JSON_UNESCAPED_UNICODE);
            }
        } else {
            if (empty($errorMsg)) {
                $errorMsg = TErrorConstants::getErrorMsg($errorCode);
            }
            Wk::logger()->err('page error:' . $errorCode . (empty($errorMsg) ? '' : ('(' . $errorMsg . ')')));
            // $this->renderView('/layouts/404');
            echo 'error: ' . $errorCode . (empty($errorMsg) ? '' : ('(' . $errorMsg . ')'));
        }
        Wk::app()->stop();
    }
} 
