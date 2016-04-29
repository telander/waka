<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/26
 * Time: 上午11:57
 */

class ApiController extends Controller{
    public function access() {
        return [];
    }

    const DOMAIN_PATTERN = '/^.*(\.iwakasoccer.com?)|(\.iwakasoccer.local(:8011)?)$/';

    public $curLat;

    public $curLng;

    public $curDest;


    public function beforeAction() {
//            if (empty($_COOKIE['PHPSESSID'])) {
//                throw new K_Exception('illegal request', -1);
//            }
//                $queries = array_merge((!empty($_GET) ? $_GET : []), (!empty($_POST) ? $_POST : []));
//                ksort($queries);
        parent::beforeAction();
        $this->curLat = Wk_Request::getRequestFloat('curLat', 0);
        $this->curLng = Wk_Request::getRequestFloat('curLng', 0);
        $this->curDest = Wk_Request::getRequestFloat('curDest', 0);
    }

    public function renderAjax($obj = null) {
        $json = [
            'ok' => 1
        ];
        if (isset($obj)) {
            $json['obj'] = $obj;
        }
        $json = json_encode($json, JSON_UNESCAPED_UNICODE);
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            $origin = $_SERVER['HTTP_ORIGIN'];
            if (preg_match(self::DOMAIN_PATTERN, $origin) === 1) {
                header('Access-Control-Allow-Origin: '.$origin);
                header('Access-Control-Allow-Credentials: true');
            }
        }
        if (!empty($_GET['callback'])) {
            header('Content-Type: application/javascript; charset=utf-8');
            echo $_GET['callback'].'(' . $json . ')';
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo $json;
        }
        Wk::app()->stop();
    }

    public function returnError($errorMsg = '', $errorCode = -1, $httpStatus = 200) {
        if(preg_match("/too many connection/i", $errorMsg)) {
            $errorMsg = "流量爆表啦！小伙伴们请稍后重试吧~我们正在马不停蹄地解决！";
        }

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            $origin = $_SERVER['HTTP_ORIGIN'];
            if (preg_match(self::DOMAIN_PATTERN, $origin) === 1) {
                header('Access-Control-Allow-Origin: '.$origin);
                header('Access-Control-Allow-Credentials: true');
            }
        }
        header("Expires: 0");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/json; charset=utf-8');
        if (!empty($errorMsg)) {
            echo json_encode(['ok' => 0, 'code' => $errorCode, 'msg' => $errorMsg], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['ok' => 0, 'code' => $errorCode, 'msg' => TErrorConstants::getErrorMsg($errorCode)], JSON_UNESCAPED_UNICODE);
        }
        Wk::app()->stop();
    }

} 