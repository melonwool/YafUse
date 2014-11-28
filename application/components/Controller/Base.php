<?php
class Controller_Base extends Yaf_Controller_Abstract{
    public function init(){
        $this->_config = Yaf_Registry::get('config');
        $this->_req = $this->getRequest();
        $this->_session = Yaf_Session::getInstance();
        $this->_session->start();
    }
    /**
     * reffer验证
     * @return boolean
     */
    public function checkReffer($whiteList = array()) {
        $check = new checkReffer();
        if (!$check->check_referer($whiteList)) {
            $result = array("errno" => 5000, "errmsg" => "Error: unaccepted request! Wrong Referer!");
            $callback = isset($_GET['callback']) ? trim($_GET['callback']) : false;
            $this->show_json($result, $callback);
        }
    }    
    /**
     * 输出json结果
     * @param array $data
     * @param string $callback
     */
    public function show_json($data, $callback = false) {
        header("Content-type: application/json;charset=utf-8");
        $callback = isset($_GET['callback']) ? trim($_GET['callback']) : false;
        if ($callback) {
            echo $callback . "(" . json_encode($data) . ")";
        } else {
            echo json_encode($data);
        }
        exit;
    }

    /**
     * 输出 json 格式的数据 
     */
    public function renderJson($data, $encoding = 'utf-8') {
        header("Content-type: application/json;charset=$encoding");
        echo json_encode($data);
        exit;
    }

    public function renderSuccessJson($data = null, $encoding = 'utf-8') {
        $out = array('errno' => 0, 'errmsg' => 'ok');
        if ($data) {
            $out = array_merge($out, $data);
        }
        return $this->renderJson($out, $encoding);
    }
}
