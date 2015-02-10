<?php
namespace Home\Model;
use Think\Model;
class CategoryModel extends Model{
    


        //获得搜索下分类ID
    public function getAllCatId($cat_id){
        
        //获得大分类的所在有ID
        $son_cat_id=$this->getChild($cat_id);
        $son_cat_id[]=$cat_id;
        $son_cat_id=  implode(',', $son_cat_id);
        
        //获取大分类下载所有的商品的ID
        $sql="select  GROUP_CONCAT(id) id from php28_goods WHERE cat_id  in(".$son_cat_id.")";
        $all_gid=$this->query($sql);
        $all_gid=$all_gid['0']['id'];
        
        //取出这些分类商品所有品牌
        $sql="SELECT a.id,a.goods_name,b.brand_name FROM php28_goods a LEFT JOIN php28_brand b ON a.brand_id=b.id where a.id in({$all_gid}) AND brand_id>0 GROUP BY a.brand_id";
        $all_brand=$this->query($sql);
      
        //价格区间
        //先出这个分类下所有商品的最小价格和最大价格
        $sql="SELECT MAX(shop_price) map,MIN(shop_price) mip FROM php28_goods WHERE cat_id in($son_cat_id)";
           $price= $this->query($sql);
        
     //取出价格区间
         $price_section=$this->field('cat_section_price,cat_attr_id')->find($cat_id);
         $psection=(int)$price_section['cat_section_price']<0?5:(int)$price_section['cat_section_price'];
         
         
       //计算每个间距的距离
         
   
         $per_section=  ceil(($price[0]['map']-$price[0]['mip'])/$psection);
         
         $firstPrice=$price[0]['mip'];
         //组装价格区间
       
         $priceArr=array();
           
       
         for($i=0;$i<$psection;$i++){
             
             
                        if($i==$psection-1){
                              $priceArr[]= $firstPrice.'-'.$price['0']['map'];
                            
                        }else{
                $priceArr[]= $firstPrice.'-'.($firstPrice+$per_section);
                 $firstPrice=$firstPrice+$per_section+1;     
                        }
                       
         }
         
       
//     3取出这个分类下筛选属性
        
         if($price_section['cat_attr_id']){
             $attrModel=D('Attr');
           $attr=$attrModel->field('id,attr_name')->order('id ASC')->SELECT($price_section['cat_attr_id']);
          //  $sql="select id,attr_name where "
       //遍历取出有属性值的商品
  
           
       foreach ($attr as $k=>$v){
           
           $sql="SELECT DISTINCT goods_attr_val FROM php28_goods_attr WHERE attr_id=".$v['id']." AND goods_id IN($all_gid) ";
          $attr[$k]['attr_value']=$this->query($sql);
         
          //show_bug($aa);
       }
      // show_bug($arr);
       
   
       
         }
         return array(
             'brand_data'=>$all_brand,
             'price_section'=> $priceArr,
             'attr'=>$attr,
         );
      
     
         
        
        
        
    }
    
             //找出所有子孙分类ID
          public function getChild($id){
       
       
            $data=$this->select();
            //show_bug($data);die;
            return $this->reSort2($data,$id,true);
       }
    

        //取出所有子孙的权限ID 删除要用到的
       public function reSort2($data,$pid=0,$isClean=false){
       
           static $ret=array();

           if($isClean)
            $ret=array();
           
           foreach($data as $k=>$v){
            
                if($v['cat_pid']==$pid){
                    
                   
                    $ret[]=$v['id'];

                    $this->reSort2($data,$v['id']);
                
                    
                }
           
           }

       
       return $ret;
       }

}


