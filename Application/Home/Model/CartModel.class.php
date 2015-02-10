<?php
namespace Home\Model;
use Think\Model;
class CartModel extends Model{
    var $mid;
    public function __construct() {
        parent::__construct();
        $this->mid=  session('id');
    }
//加入购物车方法
    public function addToCart($gid,$gn,$gaid=''){
        
        
      //如果传入商品属性的话，把商品属性转化成字符串
        $gastr='';
        if($gaid){
            
            $sql='select group_concat(concat(b.attr_name,":",a.goods_attr_val) separator "<br/>") gastr from php28_goods_attr a left join php28_attr b on a.attr_id=b.id where a.id in('.$gaid.')';
          
             $ga=$this->query($sql);
             $gastr=$ga[0]['gastr'];
            
        }
          //如果登录的就把购物车的数据，库入数据库
        if($this->mid){
            $res=$this->where(' goods_id='.$gid." and gaid='$gaid'" )->find();
            if($res){
                $this->where('goods_id='.$gid." and gaid='$gaid'" )->setInc('goods_number',$gn);
            }else{
            
                $this->add(array(
                    'member_id'=>$this->mid, 
                    'goods_id'=>$gid,
                    'gaid'=>$gaid,
                      'gastr'=>$gastr,
                        'goods_number'=>$gn,
                ));
            }
            
        }else{
            //如果还没有登录，就把数据放入COOKIE中去
            $cart=  isset($_COOKIE['cart'])?unserialize($_COOKIE['cart']):array();
            
            $key=$gid.'-'.$gaid;
            
            if($cart[$key]){
                $cart[$key][0]+=$gn;
            }  else {
                $cart[$key]=array($gn,$gastr);
                $time=30*24*3600;
                setcookie('cart',serialize($cart),$time+time(),'/','.28.com');
            }
            
            
        }
    }
    //清空购物车
    public function clear(){
        if($this-mid){
            $this->where('member_id='.$this->mid)->delete();
        }else{
            setcookie('cart','',1,'/','.28.com');
        }
    }
    //获取购物车列表
    public function getGoodsList(){
        
        $goods=array();
        $goodsModel=D('Goods');
        
        //如果登录时，就从数据库存取得数据
        if($this->mid){
        $data=$this->where('member_id='.$this->mid)->select();
      
     
                foreach($data as $k=>$v){
                    $price= $goodsModel->getMemberPrice($v['goods_id']);
                     $goodsModel->field('middle_img,goods_name')->find($v['goods_id']);
            
                    $goods[]=array(
                            'goods_id'=>$v['goods_id'],
                            'middle_img'=>$goodsModel->middle_img,
                    'goods_name'=>$goodsModel->goods_name,
                    'gastr'=>$v['gastr'],
                      'gaid'=>$v['gaid'],
                      'price'=>$price,
                      'goods_number'=>$v['goods_number'],
                          'xj'=>$price*$v['goods_number'],
                    );
                 
                }
        }else{
            //如果没有登录时，就从COOKIE取得数
            $cart=  isset($_COOKIE['cart'])?unserialize($_COOKIE['cart']):array();
            
            
             foreach($cart as $k=>$v){
                 
                 $_k=  explode('-', $k);
                    $price= $goodsModel->getMemberPrice($_k[0]);
                     $goodsModel->field('middle_img,goods_name')->find($_k[0]);
            
                    $goods[]=array(
                            'goods_id'=>$_k[0],
                            'middle_img'=>$goodsModel->middle_img,
                    'goods_name'=>$goodsModel->goods_name,
                      'gaid'=>$_k[1],
                    'gastr'=>$v[1],
                      'price'=>$price,
                      'goods_number'=>$v[0],
                          'xj'=>$price*$v[0],
                    );
                 
                }
            
            
        }
        return $goods;
    }
    //登录时，将COOKIE的数据移到数据中，并清空COOKIE
    public function moveToDb(){
      
        if($this->mid){
            
            $cart= isset($_COOKIE['cart'])?unserialize($_COOKIE['cart']):array();
         
            foreach($cart as $k=>$v){
                
                $goods= explode('-',$k);
               
                 
                $this->add(array(
                    'member_id'=>$this->mid,
                    'goods_id'=>$goods[0],
                    'gaid'=>$goods[1],
                    'gastr'=>$v[1],
                    'goods_number'=>$v[0],
                ));
                
                
            }
               
            setcookie('cart','',1,'/','.28.com');
        }
    }
   //更新购物车库存方法
   public function updateGN($gid,$gaid,$gn){
       
         
       //用户登录时，更新数据库
       if($this->mid){
         
           if($gn==0){
              
                    $this->where('member_id='.$this->mid.' and goods_id='.$gid." and gaid='$gaid'")->delete();  //库存等于0，就删除这个记录
           }else{
           
             $this->where('member_id='.$this->mid.' and goods_id='.$gid." and gaid='$gaid'")->setField('goods_number', $gn);
             
               
              
           }
       
           
       }  else {
           //用户没有登录时，只更新COOKIE
           $cart=  isset($_COOKIE['cart'])?unserialize($_COOKIE['cart']):array();
           
           $key=$gid.'-'.$gaid;
           if($gn==0){
               unset($cart[$key]);
           }else{
               $cart[$key][0]=$gn;
               $time=30*24*3600;
               setcookie('cart',  serialize($cart),time()+$time,'/','.28.com');
           }
           
           
       }
   }
            
}
