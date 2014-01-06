<?php
class IndexController extends Yaf_Controller_Abstract {

    private $_layout;

    public function init(){
        //使用layout页面布局
        $this->_layout = new LayoutPlugin('layout.html');
        $this->dispatcher = Yaf_Registry::get("dispatcher");
        $this->dispatcher->registerPlugin($this->_layout);
        $this->_config = Yaf_Registry::get("config");
    }
    /*首页展示*/
    public function IndexAction()
    {
        if($_POST){
            //获取post提交的参数
            $name = $this->getRequest()->getPost('usrname');
            $pwd = $this->getRequest()->getPost('pwd');
            $Admin = new AdminModel();
            if(!$Admin->LoginUsr($name, md5($pwd))){
                exit("101:用户名或密码错误!");
            }
            $_SESSION['username'] = $name;
            exit("100:登录成功！");
        }
        /*layout*/
        $this->_layout->meta_title = '登录';
    }

    /*用户登录*/
    public function LogoutAction()
    {
        unset($_SESSION['usrname']);
        header('Location:/index/');
    }
}
