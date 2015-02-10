<?php
namespace Home\Model;
use Think\Model;
class GoodsModel extends Model{
    
    //验码字段

    //商品搜索方法
        public function search(){
                $where=1;
                
                //按商品名称搜索
                $gn=I('get.gn');
              
                if($gn){
                    
                    $where.=" AND goods_name LIKE '%$gn%'";
                  
                }
                
                //按价格搜索
                $price=I('get.price');
             
                if($price){
                    $price= explode('-', $price);
                    $where.=' AND shop_price BETWEEN '.$price[0].' AND '.$price[1];
                }
                
                //按品牌搜索
                $bid=I('bid');
                if($bid){
                    $where.=' AND brand_id='.$bid;
                }
              
                //按分类搜索
                $cid=I('get.cid');
                if($cid){
                    
                    //取出所有的分类孙ID
                    $catModel=D('Category');
                    $son_cat_id=$catModel->getChild($cid);
                    $son_cat_id[]=$cid;
                    $son_cat_id=  implode(',', $son_cat_id);
                    
                    
                    $where.=" AND cat_id IN($son_cat_id)";
                }
             
                //商品属性搜索
                $fa=I('get.fa');
                if($fa){
                    $attr_id=$catModel->field('cat_attr_id')->find($cid);
                   $attr_id=  explode(',', $attr_id['cat_attr_id']);
                    sort($attr_id);
                    $_fa=  explode('.',$fa);
                  
                            
                    //遍历这个分类每个属性值
                    foreach ($_fa as $k=>$v){
                        
                        if($v!='0'){
                                      $where.=" AND id IN(SELECT goods_id from php28_goods_attr where attr_id=".$attr_id[$k]." AND goods_attr_val='".$v."')";
                        }
              
                        
                    }
                    
                }
                
                //按销量搜索
               $orderby='xl';
                
                $orderway='desc';
                $ob=I('ob');
                $ow=I('ow');
                if($ob&&  in_array($ob, array('xl','shop_price','pl')));
                $orderby=$ob;
                  if($ow&&  in_array($ow, array('asc','desc')));
                $orderway=$ow;
                //联表查询
              //  $sql='select * from php28_goods  where id IN(select goods_id from php28_order_goods a left join php28_order on a.order=b.id where order_status=2)'
              
                $sql='select a.* ,b.sum(goods_number) from php28_goods a left join php28_order_goods b on a.id=b.goods_id AND b.order_id in(select id from php28_order where order_status=2)';
                
           
                //商品搜索颁布显示
                $count      = $this->where($where)->count();
              $Page       = new \Think\Page($count,5);// 实例化分页类 传入总记录数和每页显示的记录数(25)
              $show       = $Page->show();// 分页显示输出
              //
              if($orderby=='xl'){
                  
                       // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
                $list =  $this->field('a.*,b.sum(IFNULL(goods_number),0)')->alias('a')->join("left join php28_order_goods b on a.id=b.goods_id AND b.order_id in(select id from php28_order where order_status=2)")->where($where)->order("$orderby $orderway")->limit($Page->firstRow.','.$Page->listRows)->select();
             //return $this->where($where)->select();
               // $sql="SELECT * FROM php28_goods  WHERE 1";
          
               // return $this->query($sql);
              }elseif($orderby='pl') {
           
                  
                  //$sql='select a.*,count(b.id) from php28_goods a left join php28_remark on a.id=b.goods_id';
                  
                            $list =  $this->field('a.*,count(b.id) pl')->alias('a')->join("left join php28_remark b on a.id=b.goods_id")->where($where)->order("$orderby $orderway")->limit($Page->firstRow.','.$Page->listRows)->select();
              }else{
                   echo 11;
                   $list =  $this->where($where)->alias('a')->order("$orderby $orderway")->limit($Page->firstRow.','.$Page->listRows)->select();
                    
              }
              return array(
                  'list'=>$list,
                  'show'=>$show,
              );
              //
        
    }


    //获得推荐位ID
    public function getRecId($rec_id,$limit=5) {
        
        return $this->where("FIND_IN_SET($rec_id,rec_id)")->limit($limit)->select();
    }
    
    //浏览该商品还哪里商品
    public function getOtherGoods($gid,$limit=10){
        
        $sql="SELECT A.*,SUM(VIEW_COUNT) FROM PHP28_GOODS A LEFT JOIN PHP28_HISTORY B ON A.ID=B.GOODS_ID"
                . " WHERE MEMBER_ID IN(SELECT MEMBER_ID FROM PHP28_HISTORY WHERE GOODS_ID=$gid) AND GOODS_ID <>$gid GROUP BY GOODS_ID ORDER BY VIEW_COUNT DESC limit ".$limit;
        return $this->query($sql);
    }
    
    //获取商品属性
    public function getGoodsAttr($goods_id){
       
         $sql="select a.*,b.attr_name,b.attr_type from php28_goods_attr a left join php28_attr b on a.attr_id=b.id where goods_id=".$goods_id;
         return $this->query($sql);
    }
     //获取会员价格
     public function getMemberPrice($gid){
        
         if($mid= session('id')){
           
            $level_id=  session('level_id');
            $rate=  session('rate');
            
             
         }else{
             $level_id=0;
             $rate=1;
         }
         //如果商品设置会员价格就用会员价格，
         $mpModel=D("MemberPrice");
         $mp=$mpModel->where('goods_id='.$gid.' and level_id='.$level_id)->find();
         
         
         if($mp){
            
             return $mpModel->price;
            
             
         }else{
             
             //否则就用折扣率*本店的商品
             $aa=$this->field('shop_price')->where('id='.$gid)->find();
             
             return $this->shop_price*$rate;
               
         }
         return array(
             'list'=>$list,
             'show'=>$show,
         );
         
     }
     //获取商品库存方法
     public function getNM($gid,$gaid){
         $numModel=D('GoodsNumber');
       
  $numModel->where('goods_id='.$gid." and goods_attr_id='$gaid'")->find();
         
        
         return $numModel->goods_number;
        
     }
}
