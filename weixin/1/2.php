<?php


require_once './config.php';

require_once './function.php';

$data=json_encode(array(
    'action_name'=>'QR_LIMIT_SCENE',
    'action_info'=>array(
        'scene'=>array(
            'scene_id'=>1,
        ),
    ),
));


$access_token=  getAccessToken();
//echo $access_token;
$url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
var_dump($url);
$ticket=http_post($url, $data,true);
var_dump($ticket);
$ticket=  json_decode($ticket,true);

//通过ticket换取二维码
$url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket['ticket']);
$img=http_get($url,true);

file_put_contents('1.png', $img);