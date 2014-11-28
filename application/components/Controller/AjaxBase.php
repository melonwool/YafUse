<?php
class Controller_AjaxBase extends Controller_Qipai
{
    public function init() {
        parent::init();
        //关闭自动加载template功能
        Yaf_Dispatcher::getInstance()->autoRender(false);
        if ($this->_req->isPost()) {
            $this->checkReffer(array(
                isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ""
            ));
        }
    }
}
