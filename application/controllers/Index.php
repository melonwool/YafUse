<?php
class IndexController extends Base {

    private $_layout;

    public function init(){
        $this->_config = Yaf_Registry::get("config");
		$this->_req = $this->getRequest();
		$this->_session = Yaf_Session::getInstance();
		$this->_session->start();
    }
    /*首页展示*/
    public function IndexAction()
    {
        if($this->_req->isXmlHttpRequest()){
            //获取post提交的参数
            $name = $this->_req->getPost('usrname');
            $pwd = $this->_req->getPost('pwd');
            $Admin = new AdminModel();
            if(!$Admin->LoginUsr($name, md5($pwd))){
                exit("101:用户名或密码错误!");
            }
			$this->_session->set('username',$name);
            exit("100:登录成功！");
        }
    }

    /*用户登录*/
    public function LogoutAction()
    {
		$this->_session->__unset('username');
		$this->_req->setRedirect("/index/");
    }
}
