<?php
global $curauth;
$desc = get_user_meta($curauth->ID, 'description', true);
if(!$desc) $desc = '未填写';
$mobile_phone = get_user_meta($curauth->ID, 'mobile_phone', true);
if(!$mobile_phone) $mobile_phone = '未填写';
if($mobile_phone) $mobile_phone = substr_replace($mobile_phone, '****', 3, 4);
$gender = get_user_meta($curauth->ID, 'gender', true);
if(!$gender) $gender = '保密';
$location = get_user_meta($curauth->ID, 'location', true);
if(!$location) $location = '未填写';
$email = $curauth->data->user_email;
$email = substr($email, 0, 3).'****'.substr($email, strpos($email, "@"));
?>
<div class="authorProfile">
  <table class="form-table" width="100%">
  <tr><th width="120">昵称</th><td><?php echo $curauth->data->display_name; ?></td></tr> 
  <tr><th>性别</th><td><?php echo $gender; ?></td></tr>
  <tr><th>居住地</th><td><?php echo $location; ?></td></tr>
  <tr><th>个人网站</th><td><?php echo $curauth->data->user_url; ?></tr>
  <tr><th>电子邮箱</th><td><?php echo $email; ?></td></tr>
  <tr><th>手机号码</th><td><?php echo $mobile_phone; ?></td></tr>
  <tr><th>简介</th><td><?php echo $desc; ?></td></tr>
  <tr><th>注册时间</th><td><?php echo $curauth->data->user_registered; ?></td></tr>
  </table>
</div>