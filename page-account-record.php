<?php
$sql = 'select * from wp_loginlog where username = "'.$current_user->user_login.'" order by id desc';
$loginlog = $wpdb->get_results($sql);
get_header();
?>
<div class="container">
<div class="row">
<?php get_sidebar('user'); ?>
<div class="col-md-9">
<div class="wt-container">
<h1 class="wt-setting-tit">登录历史</h1>
<table class="table list_table">
  <tr>
    <th>时间</th>
    <th>IP</th>
    <th>浏览器</th>
    <th>设备</th>
    <th>状态</th>
  </tr>
  <?php foreach($loginlog as $loginlog_item){ ?>
  <tr>
    <td><?php echo date('m月d日 H:i', $loginlog_item->time); ?></td>
    <td><?php echo $loginlog_item->ip; ?></td>
    <td><?php echo get_broswer($loginlog_item->user_agent); ?></td>
    <td><?php echo get_os($loginlog_item->user_agent); ?></td>
    <td><?php echo $loginlog_item->success?'成功':'失败'; ?></td>
  </tr>
  <?php } ?>
</table>
</div>
</div>
</div>
</div>
<?php get_footer();?>