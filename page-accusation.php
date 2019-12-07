<?php get_header(); ?>
<?php if(isset($_POST['submit_accusation'])){
  global $current_user, $wpdb;
  $sql = 'insert into wp_accusation values (NULL, '.$current_user->ID.', "'.$_POST['accusation_path'].'",
  "'.$_POST['accusation_type'].'","'.$_POST['accusation_content'].'","'.date('Y-m-d H:i:s', time()+3600*8).'");';
  $isInsert = $wpdb->query($sql);
  if($isInsert){
?>
<div class="container">
  <div class="col-md-8 col-md-offset-2">
    <div class="wt-container">
      <div class="wp-info wp-info-success">
        <div class="wp-info-icon"><i class="fa fa-check"></i></div>
        <h3>举报成功！</h3>
        <p>我们已经收到你的举报，将会在1-2个工作日处理，处理结果将会通过站内信、邮箱、站内公告等联系方式告知您。</p>
      </div>
    </div>
  </div>
</div>
<?php }}else{ ?>
<div class="container">
	<div class="row">
		<?php get_sidebar('page'); ?>
		<div class="col-md-9">
      <div class="wt-container">
        <div class="breadcrumbs"><?php breadcrumbs(); ?></div>
        <div class="accusation">
          <h1>内容举报</h1>
          <form method="post">
          <p>您要举报内容的网址</p>
          <p><textarea class="form-control" name="accusation_path" placeholder="内容网址"></textarea></p>
          <p>您要举报的类型</p>
          <ul>
            <li><i class="fa fa-check"></i> 垃圾营销</li>
            <li><i class="fa fa-check"></i> 不实信息</li>
            <li><i class="fa fa-check"></i> 有害信息</li>
            <li><i class="fa fa-check"></i> 违法信息</li>
            <li><i class="fa fa-check"></i> 淫秽色情</li>
            <li><i class="fa fa-check"></i> 人身攻击我</li>
            <li><i class="fa fa-check"></i> 抄袭我的内容</li>
            <li><i class="fa fa-check"></i> 冒充我</li>
            <li><i class="fa fa-check"></i> 泄露我的隐私</li>
          </ul>
          <input type="hidden" value="" class="accusation_type" name="accusation_type" />
          <p>举报内容</p>
          <p><textarea class="form-control" name="accusation_content" placeholder="其他举报内容"></textarea></p>
          <p><button type="submit" name="submit_accusation" class="btn btn-primary">提交</button></p>
          </form>
          <div class="accusation_footer">如需帮助 请联系QQ：353207542 邮箱：353207542@qq.com</div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
$(function(){
	$('.accusation li').click(function(){
		$('.accusation li').removeClass('active');
		$(this).addClass('active');
		$('.accusation_type').val($(this).text().replace(/(^\s*)|(\s*$)/g, ""))
	})
})
</script>
<?php } ?>
<?php get_footer();?>