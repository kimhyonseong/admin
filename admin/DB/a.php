<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-03-28
 * Time: 오후 4:03
 */
$conn=mysqli_connect('localhost','root','khs3326','focus');
$delete_img_file = mysqli_query($conn,'select img_url from img where art_num=');

//while ($delete_img_file1 = mysqli_fetch_array($delete_img_file))
//{
//    $img_name=substr($delete_img_file1['img_url'],strpos($delete_img_file1['img_url'],'/edt_img/'));
//    echo $img_name;
//    echo $delete_img_file1['img_url'];
//    //unlink('.'.$img_name);
//    unlink($delete_img_file1['img_url']);
//}
$query = 'select a.title,a.art_num,c.s_name,a.w_name,date_format(a.post_date,"%y.%m.%d") post_date,
                                  date_format(a.modi_date,"%y.%m.%d") modi_date,a.ok from article a
                          join cate c on a.code=c.code 
                                       and c.big_num=7
                          where ok=1';
$all_page_query = 'select ceil(count(*)/15) all_page from article a
                                   join cate c on a.code=c.code
                                                and c.big_num=7
                                   where a.ok=1';
mysqli_query();
 ?>