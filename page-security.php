<?php
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
<h1 class="wt-setting-tit">密码设置</h1>
<?php if(isset($success_msg)){ ?>
<div class="alert alert-success setting_alert"><?php echo $success_msg;?></div>
<?php }?>
<?php if(isset($error_msg)){?>
<div class="alert alert-danger setting_alert"><?php echo $error_msg;?></div>
<?php }?>
<form method="post">
	<table width="100%" border="0" class="form-table">
	  <tbody>
	  <tr>
		<td width="120">原始密码</td>
		<td><input type="password" class="form-control" style="width:50%" name="old_password" placeholder="原始密码"></td>
	  </tr>
	  <tr>
		<td>新密码</td>
		<td><input type="password" class="form-control" style="width:50%" name="new_password" placeholder="新密码"></td>
	  </tr>
	  <tr>
		<td>确认密码</td>
		<td><input type="password" class="form-control" style="width:50%" name="re_new_password" placeholder="确认密码"></td>
	  </tr>
	  <tr>
		<td></td>
		<td><button type="submit" name="submit_security" class="btn btn-primary">修改密码</button></td>
	  </tr>
	  </tbody>
	</table>
</form>
</div>
</div>
</div>
</div>
<?php get_footer();?>