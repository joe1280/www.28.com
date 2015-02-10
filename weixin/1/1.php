<?php
include './config.php';
include './function.php';

//构建json
$data=json_encode(array(
    'touser'=>'',//用户ID
    'msgtype'=>'text',
    'text'=>array(
        'content'=>'hello',
    ),
));
$access_token=  getAccessToken();
$url="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$access_token;
 echo http_post($url, $data,true);


                                require_once('./config.inc.php');
                                require_once ('./uc_client/client.php');