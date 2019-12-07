<?php
if(!is_user_logged_in()){
	header('location:/wp-login.php');
	exit;
}

//更新密码
if(isset($_POST['submit_security'])){
	if($_POST['new_password'] == $_POST['re_new_password']){
		$user = wp_authenticate($current_user->user_login, $_POST['old_password']);
		if(is_wp_error($user)){
			$error_msg = '原始密码不正确';
		}else{
			wp_set_password( $_POST['new_password'], $current_user->ID );
			$success_msg = '密码修改成功，可能需要您重新登录！';
		}
	}else{
		$error_msg = '两次输入的密码不一致！';
	}
}
get_header();
?>
<div class="container">
<div class="row">
<?php get_sidebar('user'); ?>
<div class="col-md-9">
<div class="wt-container">
<h1 class="wt-setting-tit">账号设置</h1>
<form method="post">
	<table width="100%" border="0" class="form-table">
	  <tbody>
	  <tr>
		<td width="140">打包下载文章</td>
		<td><button type="button" class="btn btn-success">下载我的所有文章</button></td>
	  </tr>
	  <tr>
		<td>冻结账户</td>
		<td><button type="button" class="btn btn-success">冻结账户</button></td>
	  </tr>
	  <tr>
		<td>删除账号</td>
		<td>
		<button type="button" class="btn btn-danger">删除我的账号</button>
		<p><i class="fa fa-info-circle"></i> 这将删除你的账号信息，以及和您相关的信息，包括但不限于你在本站发布的文章，你给其他用户的评论和你收到评论，你在社区发布的话题和其他用户回复您的内容，强烈建议先下载您的文章。</p>
		</td>
	  </tr>
	  </tbody>
	</table>
</form>
</div>
</div>
</div>
</div>
<?php get_footer();?>