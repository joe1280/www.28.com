   var mid=0;
  var chk_login=0;

$(function(){
 
    $.ajax({
        type:'GET',
        url:'/index.php/Home/index/ajaxChkLogin',
        dataType:'json',
        success:function(data){
          
            if(data.id==0){
                  $("#loginfo").html("你好，欢迎来到京东商城 <a href='/index.php/Home/index/login'>[登录]</a><a href='/index.php/Home/index/regist'>免费注册</a>");
            }else{
                mid=data.id;
                    $('#loginfo').html('你好，'+data.username+'!<a href="/index.php/Home/index/logout">退出</a>');
                   chk_login=1;
            }
            
             
                
                  
        
        
        }
      
        
        
    });
});