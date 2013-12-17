<?php
class MainController extends Yaf_Controller_Abstract {
    private $_layout;

    public function init(){
         //使用layout页面布局
         $this->_layout = Yaf_Registry::get('layout');
    }

    public function indexAction()
    {
        $this->_layout->setLayoutFile('layout.html');
    	if(!isset($_SESSION['username'])){
    		header('Location:/index/');
    	}
    }

    public function GmtAction()
    {
        $type = $this->getRequest()->getQuery('type');
        $message = $this->getRequest()->getQuery('message');
        $flag = $this->getRequest()->getQuery('flag');
        $is_record = $this->getRequest()->getQuery('is_record');
        print $type.','.$message.','.$flag.','.$is_record.'<br>';
        $GMtool = new GmsocketModel('192.168.0.198',10001);
        //for($i=100;$i--;$i>=0){
            $result = $GMtool ->SendMsg($type, $message,$flag,$is_record);
        //}
        // $result2 = $GMtool ->SendMsg('notice', 'This is a new day!');
        $GMtool->Close();
        print_r($result);
        exit;
    }
}

