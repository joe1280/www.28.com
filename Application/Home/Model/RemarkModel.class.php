<?php
namespace Home\Model;
use Think\Model;
class RemarkModel extends Model{
    
        protected $_validate = array(
         
            array('goods_id','require','商品ID不能为空',1),
            array('content','require','评论商品不能空',1),
            array('stars','require','评分不能为空',1),
	);
        
    //加一个前置的钩子函数
    protected function _before_insert(&$data, $options) {
       
       $data['member_id']=  (int)session('id');
       $data['addtime']=  date("Y-m-d H:m:s");
       
       //处理印象数据
       $goods_id=$data['goods_id'];
       $impression=I('post.impression');
     
       if($impression){
           $impressionModel=D('Impression');
           $res=$impressionModel->where("name='$impression'")->find();
           if($res){
               $impressionModel->where("id=".$res['id'])->setInc('count');
           }else{
            
               $impressionModel->add(array(
                   'goods_id'=>$goods_id,
                   'name'=>$impression,
                   'count'=>1,
               ));
                    show_bug($impression);die;
           }
       }
   
       
    }
}