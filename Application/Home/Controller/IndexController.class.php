<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
	       //引入CSS JS
                $this->assign('css',array('index'));
                $this->assign('js',array('index'));
                $this->assign('title','首页');
                $GoodsModel=D('Goods');   
                   $this->assign('rec',array(
                'goods1'=>$GoodsModel->getRecId(1),
                'goods2'=>$GoodsModel->getRecId(2),
           ));
      
           
       $this->display();
    }
    
       public function goods($id){
           
           //引入CSS JS
          
           $this->assign('css',array('goods','common','jqzoom'));
           $this->assign('js',array('goods','jqzoom-core'));
           $this->assign('title','商品页面');
           $this->assign('hide',1);
           //调用取出看了这件商品，还看其它商品的方法
           $goodsModel=D('Goods');
          $ogid= $goodsModel->getOtherGoods($id);
          //show_bug($goodsModel->getLastSql());
         // show_bug($ogid);die;
          $this->assign('ogid',$ogid);
           //取出商品基本信息
             $goods_info=$goodsModel->find($id);
    
           //取出图片本信息
             $pic_info=D('Pics')->where('goods_id='.$id)->select();
             
             //取出商品属性的基本信息
            $gattr=$goodsModel->getGoodsAttr($id);
             //把商品属性分为单选和唯一两个数组
            $radio_arr=array();//单选
            $u_arr=array();//唯一
       foreach ($gattr as $k=>$v){
           if($v['attr_type']=='单选'){
                $radio_arr[$v['attr_id']][]=$v;
           }else{
               $u_arr[]=$v;
           }
       }
       //sshow_bug($radio_arr);
       $this->assign(array(
           'goods_info'=>$goods_info,
           'pic_info'=>$pic_info,
           'radio_attr'=>$radio_arr,
           'u_arr'=>$u_arr,
       ));
       
 
    
       
       
       
       
            
          
        $this->display();
    }

    
    public function regist(){
        
        if(IS_POST){
            $member=D('Member');
                if($member->create()){
                    if($member->add())
                            die('已经发送邮件，请到你的邮箱进行验证');
                        
                        
                }
                    
            
        }  else {
                
            $this->display();
        }
    }
    //登录方法
    public function login(){
        
        if(IS_POST){
            
            //show_bug($_POST);DIE;
            $member=D('Member');
                if($member->create()){
                 
                      $res= $member->login();
                      if($res===true){
            
                                     require_once './config.inc.php';
                                     require_once './uc_client/client.php';
                                      	
                              $html=uc_user_synlogin(session('id'));
                              
                       
                                 $url=  session('url'); 
                                   
                                        if(session('url')){
                                         session('url',null);
                                         $this->success('登录成功'.$html,$url);
                                         exit;
                                     }else{
                                            $this->success('登录成功'.$html,'/');
                                            exit;
                                     }

               
                      
                      }elseif($res==1){
                          $this->success('邮箱必须验证');
                          exit;
                      }elseif($res==2){
                          $this->success('用户或者密码错误');
                          exit;
                      }
                }
                 
                    
        }
        
            $this->display();
        
    }
 
        //检查验证码
    public function chkEmail($code){

        //查数据库，有没有这个验证码;
        $memberModel=D('Member');
        $etime=C('EMAIL_CODE_EXPIRE_TIME');
        //show_bug($code);
       $res= $memberModel->where("email_code='$code'")->find();
     //  show_bug($memberModel->getLastSql());
      
       if($res){
           if(time()-$res['email_code_time']<$etime){
               
               $memberModel->where('id='.$res['id'])->setField('email_code','');
               $this->success("验证成功,请登录",U('login'));
               exit;
           }
            
       }  else {
           die('验证码无效或者过期,<a href="'.U('resendEmailCode',array('code'=>$code)).'">重新发送验证码</a>');
       }
        
        
    }
    public function resendEmailCode($code){
        $memberModel=D("Member");
        $member_info=$memberModel->where('email_code='.$code)->find();
        if($member_info!='')
        {
            $newcode=  uniqid();
            $memberModel->where('id='.$member_info['id'])->save(array(
                'email_code'=>$newcode,
                'email_code_time'=>time()
            ));
            $memberModel->sendEmailCode($newcode,$member_info['email']);
        }
    }
    public function ajaxChkLogin(){
        
        //如果已经登录了，就直接返回用户名和用户ID
        if($id=  session('id')){
         
           
            echo json_encode(array(
                'id'=>$id,
                'username'=>  session('username')
            ));   
       
            exit;
        }else{
          
                //如果没有登录，我就判断COOKIE 有没有用户名和密码，就用这个用户名和密码登录
            $username=$_COOKIE['username'];
            $password=$_COOKIE['password'];
          
                    
            if($username&&$password){
               
                //如果cookie有用户名和密码 先把用户和密码解密出来
                $key=C('DES_KEY');
                $username=  trim(\Think\Crypt::decrypt($username, $key)) ;
                $password= trim(\Think\Crypt::decrypt($password, $key));     
              
                //把用户名和密码传给模型
                $memberModel=D('Member');
                $memberModel->create(array(
                    'username'=>$username,
                    'password'=>$password
                ));
                
                if($memberModel->login()===true){
                      
                    echo json_encode(array(
                        'id'=>  session('id'),
                          'username'=>  session('username')
                    ));
                     
                          exit;
                }else{
                    //如果登录失败，说明cookie有问题，那么清空cookie
                    setcookie('username','',1);
                    setcookie('password','',1);
                }
                        
                
            }
            echo json_encode(array(
                'id'=>0
            ));
            
        }
    }
    public function logout(){
        
    $memberModel=D('Member');
    $memberModel->logout();
    require_once './config.inc.php';
     require_once './uc_client/client.php';
    $html= uc_user_synlogout();
  
    $this->success('退出成功'.$html,'/');
}
    //取历史记录方法
    public function ajaxHistory($gid){
        
    
        if($mid=session('id')){
        
               $hisModel=D('History');
                  
                 $his=$hisModel->where("goods_id=$gid AND member_id=$mid")->find();
                 if($his){
                        $hisModel->where("goods_id=$gid AND member_id=$mid")->setInc('view_count');
                 }else{
                     
                     $hisModel->add(array(
                         'member_id'=>$mid,
                         'goods_id'=>$gid,
                         'view_count'=>1 
                         
                     ));
                 }
        }
     
    }
    //ajax提交商品评论
    public function ajaxRemark(){
         $res=array(
             'error'=>'1',
             'message'=>'',
         );
        if(IS_POST){
            
            $remarkModel=D('Remark');
          
            if($remarkModel->create()){
              
                $remarkModel->add( );
                     show_bug($_POST);
                $res=array(
                    'error'=>0,
                    'message'=>'评论成功',
                );
              
        
            }else{
                $res=array( 
                    'error'=>1,
                        'message'=>$remarkModel->getError(),
                    );
                   
                
               
            }
             echo json_encode($res);
        }
    }
    
    //获取商品评论内容
    public function ajaxGetContent($gid,$p){
        
        $pagesize=3;
        $offset=($p-1)*$pagesize;
      
        //取出评论数据
        $remarkModel=D('Remark');
       
        $sql='select a.*,b.username from php28_remark a left join php28_member b on a.member_id=b.id  where goods_id='.$gid.' order by a.id desc  limit '. $offset.','.$pagesize ;
          
        $data=$remarkModel->query($sql);
       
       
        if($p==1){
            //取出印象
          
            $impressModel=D('Impression');
            $impression=$impressModel->where('goods_id='.$gid)->select();
            
            //取出好评率
            ///取出所有评论
           $stars= $remarkModel->field('stars')->where('goods_id='.$gid)->order('id desc')->select();
           $all=count($stars);
           $hao=0;
           $zhong=0;
           $chai=0;
          
           foreach($stars as $k=>$v){
               if($v['stars']>=4){
                   $hao++;
               }elseif ($v['stars']==3) {
                    $zhong++;
                }else{
                    $chai++;
                }
           }
           //计算好中差评率
             // echo $hao.'- '.$zhong.'- '.$chai;
           $hao_rate=  round($hao/$all,1)*100;
             $zhong_rate=round($zhong/$all,1)*100;
               $chai_rate=round($chai/$all,1)*100;
            
               echo json_encode(array(
                   'data'=>$data,
                   'impression'=>$impression,
                   'hao'=>$hao_rate,
                   'zhong'=>$zhong_rate,
                   'chai'=>$chai_rate,
                   'tp'=>ceil($all/$pagesize),
               ));
           
        }else{
            echo json_encode(array('data'=>$data));
        }
        
    }
    //获取会员价格和折扣率
    public function getMPrice($gid){
       
        $goodsModel=D("Goods");
         
         echo $goodsModel->getMemberPrice($gid);
         
    }
    
    //取出商品属性库存方法
    public function ajaxGetNumber($gid,$gaid){
       
        $goodModel=D('Goods');
         
        echo $goodModel->getNM($gid,$gaid);
    }
    
    //添加一个搜索方法
    public function search(){
        
        $catModel=D('Category');
        //按分类搜索
        $cat_search=$catModel->getAllCatId(I('get.cid'));
      //  show_bug($cat_search);
        $goodModel=D('Goods');
        //按关键字搜索
        $key_search=$goodModel->search();
       // show_bug($key_search);
       $aa= $this->assign(array(
            'cat_search'=>$cat_search,
            'list'=>$key_search['list'],
            'show'=>$key_search['show'],
        ));
        
        $this->display();
    }
    
}