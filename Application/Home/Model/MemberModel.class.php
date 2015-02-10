<?php
namespace Home\Model;
use Think\Model;
class MemberModel extends Model{
    
    	protected $_validate = array(
		
	);
        
        
        //登录验证方法
        public function login(){
            $password=$this->password;
            $username=$this->username;
           
            $res=  $this->where("username='$username'")->find();
            
            
            if($res){
                
                    if($res['email_code']!='')
                        return 1;
                    if($res['password']!= md5($password))
                    
                        return 2;
                     
                        session('id',$res['id']);
                        session('username',$res['username']);
                        
                        //计算会员级别ID和折扣率
                        $member_levelModel=D("MemberLevel");
                      $member_levelModel->where("{$res['jifen']} between top and bottom")->find();
                     
                      
                         //会员等级 和折扣摔      
                      session('level_id',  $member_levelModel->id);
                      
                       session('rate',$member_levelModel->rate/100);
                      
                       $CartModel=D('Cart');
                   $CartModel->moveToDb();
                      // show_bug($aa);die;
                    
                        if(I('post.remember')){
                            
                        
                            //加密码COOKIE用户名和密码
                            $key=C('DES_KEY');
                            $un=  \Think\Crypt::encrypt($res['username'], $key);
                             $pd= \Think\Crypt::encrypt($password, $key);
                             $time=30*24*3600;
                             setcookie('username',$un,time()+$time,'/','.28.com');
                             setcookie('password',$pd,time()+$time,'/','.28.com');
                             
                        }
                         
                   return true;
                        
            }   
        
             
            return 2;
        }
        
        //前置钩子函数
        protected  function _before_insert(&$data, $options) {
         
            
            
                                     require_once './config.inc.php';
                                     require_once './uc_client/client.php';
           //同步注册到Ucenter
            $res=uc_user_register($data['username'], $data['password'], $data['email']);
            if($res>0){
           $data['id']=$res;     
           $data['password']=  md5($data['password']);
           $data['email_code']=  uniqid();
           $data['email_code_time']=time();
            }
           
            
            //生成验证码
            
            
        }
        
        //加一个后置的钩子，发送验证码
        protected function _after_insert($data, $options) {
          
            $this->sendEmailCode($data['email_code'],$data['email']);
        }
        
        //发邮件的方法
        public function sendEmailCode($code,$email){
            sendMail('验证码',"请点击以下链接进行邮箱验证:<br/><a href='http://www.28.com/index.php/Home/index/chkEmail/code/{$code}'>激活</a>", $email);
        }
        public function logout(){
            
                                     
            session(null);
            setcookie('username','',1,'/','.28.com');
            setcookie('password','',1,'/','.28.com');
        }
        
    
}
