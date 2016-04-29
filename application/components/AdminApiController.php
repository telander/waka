<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/26
 * Time: 下午12:07
 */

class AdminApiController extends AdminBaseController {

    const DOMAIN_PATTERN = '/^.*(\.iwakasoccer.com?)|(\.iwakasoccer.local(:8011)?)$/';

    /**
     * 返回错误信息
     *
     * @param  string $errorMsg
     * @param  int $errorCode
     * @param  int $httpStatus
     */
    public function returnError($errorMsg='', $errorCode=-1, $httpStatus=200) {
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
        if(!empty($errorMsg)) {
            echo json_encode(['ok'=>$errorCode, 'msg'=>$errorMsg],JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['ok'=>$errorCode, 'msg'=>TErrorConstants::getErrorMsg($errorCode)],JSON_UNESCAPED_UNICODE);
        }
        Wk::app()->stop();
    }

    public function renderAjax($obj = null) {
        $json = [
            'ok' => 1
        ];
        if (isset($obj)) {
            $json['obj'] = $obj;
        }
        $jsonStr = json_encode($json, JSON_UNESCAPED_UNICODE);
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            $origin = $_SERVER['HTTP_ORIGIN'];
            if (preg_match(self::DOMAIN_PATTERN, $origin) === 1) {
                header('Access-Control-Allow-Origin: '.$origin);
                header('Access-Control-Allow-Credentials: true');
            }
        }
        header('Content-Type: application/json; charset=utf-8');
        echo $jsonStr;
        Wk::app()->stop();
    }
} 