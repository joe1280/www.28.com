<?php
namespace Home\Controller;
use Think\Controller;
class  CartController extends Controller{
    
    public function addToCart(){
        
       
        $gid=(int)I('post.gid');
        $gaid=I('post.gaId');
      
        $gn=(int)I('post.amount');
     
        if($gid<=0){
        
            $this->error('参数无效');
        }
        if($gn<=0){
            echo 22;
            $this->error('参数无效');
        }
        if($gaid){
                sort($gaid);
                $gaid=  implode(',', $gaid);
        }else{
                $gaid='';
        }
    
        $cartModel=D('Cart');
        $cartModel->addToCart($gid,$gn,$gaid);
        $this->success('加入成功',U('showlist'));
        
        
    }
    
    //显示购物车列表方法
    public function showlist(){
    
        $cartModel=D('Cart');
        $glist=$cartModel->getGoodsList();
        $this->assign('glist',$glist);
        $this->display();
        
    }
    
     //更新购物车库存方法
    public function updateGoodsNm($gid,$gaid,$gn){
        
        $cartModel=D('Cart');
        $GoodsModel=D('Goods');
        $dbGN=$GoodsModel-> getNM($gid,$gaid);
       
      
        if($dbGN>=$gn){
             
                
            $cartModel->updateGN($gid,$gaid,$gn);
            echo 0;
        }else{
            
             $cartModel->updateGN($gid,$gaid,$dbGN);
            echo $dbGN;
        }
    }
    //删除购物车商品
    public function ajaxDelGood($gid,$gaid=''){
        
         $cartModel=D('Cart');
          $cartModel->updateGN($gid,$gaid,0);
    }
    
    //结算页面
    public function info(){
        $mid=  session('id');
        if(!$mid){
            
            session('url',__SELF__);
            $this->error('请必须登录',U('Index/login'));
        }
        
        $this->display();
    }
            
            
}
