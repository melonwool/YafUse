<?php

/**
 * Description of Captcha
 *
 * @author shybily
 */
class Captcha {

    public $_redis;
    public $_sid;
    public $_config;

    public function __construct() {
        $this->_config = Yaf_Registry::get("config");
        $this->_redis = new Redis();
        $this->_redis->connect($this->_config->redis->host);
        $this->_redis->setOption(Redis::OPT_PREFIX, $this->_config->redis->prefix);
        $this->_sid = session_id();
        $this->_error = new errorCode();
    }

    public function setCacheCode($code) {
        $this->_redis->set($this->_sid, $code);
        return $this->_redis->expire($this->_sid, $this->expire);
    }

    /**
     * 
     * 获取缓存的验证码
     * 
     * @param type $input
     */
    public function getCacheCode() {
        return $this->_redis->get($this->_sid);
    }

    /**
     * 验证码校验
     * @param string $code
     * @param boolean $clean
     * @return boolean
     */
    public function verifyCode($code, $clean = false) {
        $cacheCode = $this->getCacheCode();
        if (!$cacheCode) {
            return $this->_error->getError(4003, 'api');
        } elseif (strtolower($cacheCode) !== strtolower($code)) {
            return $this->_error->getError(4004, 'api');
        }
        $clean ? $this->cleanCacheCode() : null;
        return true;
    }
    /*
     * 
     * 清除验证码缓存
     * 
     */
    public function cleanCacheCode() {
        $this->_redis->del($this->_sid);
    }

}
