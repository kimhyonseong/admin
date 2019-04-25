<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-04-25
 * Time: 오전 11:38
 */
session_start();
$img_abs_route = ($_SESSION['img_route']);
$img_old_name = unserialize($_SESSION['img_name']);

//var_dump($_SESSION['img']);
//var_dump($img_array);
$img_count = count($img_old_name);
for ($i=0; $i<$img_count; $i++)
{
    echo $img_abs_route . ' 의 ' . $img_old_name[$i] .'<br>';
}
for ($i=0; $i<$img_count; $i++)
{
    //여기서 이름을 새로운 이름으로 바꾸는 작업해야되요
    //echo strpos($img_old_name[$i],'_').'<br>';
    echo substr($img_old_name[$i],strpos($img_old_name[$i],'_')).'<br>';

    $real_img_name[$i] = substr($img_old_name[$i],strpos($img_old_name[$i],'_'));
    $img_new_name[$i] ='1111'.$real_img_name[$i];

    echo $img_new_name[$i].'<br>';
    echo $img_abs_route.'/'.$img_new_name[$i].'<br><br>';

    $img_rel_route = substr($img_abs_route,strpos($img_abs_route,'/img/'));
    if (is_file('.'.$img_rel_route.'/'.$img_old_name[$i]))
        rename('.'.$img_rel_route.'/'.$img_old_name[$i],'.'.$img_rel_route.'/'.$img_new_name[$i]);
}
?>