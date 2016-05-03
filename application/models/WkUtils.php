<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/29
 * Time: 下午10:49
 */

class WkUtils {
    public static function getClientIp() {
        if(getenv('HTTP_CLIENT_IP')){

            $client_ip = getenv('HTTP_CLIENT_IP');

        } elseif(getenv('HTTP_X_FORWARDED_FOR')) {

            $client_ip = getenv('HTTP_X_FORWARDED_FOR');

        } elseif(getenv('REMOTE_ADDR')) {

            $client_ip = getenv('REMOTE_ADDR');

        } else {

            $client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

        }
        $client_ips = explode(",", $client_ip);
        if(count($client_ips) > 0) {
            return $client_ips[0];
        }
        return $client_ip;
    }
} 