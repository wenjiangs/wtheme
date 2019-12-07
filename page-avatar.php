<?php
if(!is_user_logged_in()){
	header('location:/wp-login.php');
	exit;
}

if($_FILES['user_avatar']){
	$file = $_FILES['user_avatar'];
	$tmpname = $file['tmp_name'];
	if(is_uploaded_file($tmpname)){
		if($file['error'] == 0){
			if($file['type'] == "image/jpeg" || $file['type'] == "image/jpg" || $file['type'] == "image/png" || $file['type'] == "image/gif"){
				if($file['size'] < 1024*1024){
					$upload_dir = wp_upload_dir();
					$file_dir = $upload_dir['basedir'];
					
					//获取文件名和文件扩展名
					$file_info = pathinfo($file["name"]);

					//移动文件
					$move = move_uploaded_file($tmpname, $file_dir.'/avatar/'.$current_user->ID.'.'.$file_info['extension']);
					
					if($move){
						$suc = '上传成功！';
						//更新数据库
						update_usermeta($current_user->ID, 'user_avatar', get_bloginfo('url').'/wp-content/uploads/avatar/'.$current_user->ID.'.'.$file_info['extension']);
					}else{
						$err = '上传失败！';
					}
				}else{
					$err = '文件太大！';
				}
			}else{
				$err = '文件格式错误！';
			}
		}else{
			$err = '文件上传失败！';
		}
    }
}

if(isset($_GET['select'])){
	update_usermeta($current_user->ID, 'user_avatar', '/wp-content/uploads/sysavatar/'.$_GET['select'].'.jpg');
}

get_header();
?>
<div class="container">
<div class="row">
<?php get_sidebar('user'); ?>
<div class="col-md-9">
<div class="wt-container">
<h1 class="wt-setting-tit">基本资料</h1>
<?php if(isset($err)){ ?>
	<div class="alert alert-danger setting-alert"><?php echo $err; ?></div>
<?php } ?>
<?php if(isset($suc)){ ?>
	<div class="alert alert-success setting-alert"><?php echo $suc; ?></div>
<?php } ?>
<table class="form-table" width="100%">
  <tr>
	<td width="106"><div class="wt-setting-avatar"><?php echo get_avatar($current_user->ID); ?></div></td>
	<td>
	<button class="btn btn-success user_avatar_btn" type="button">更换头像</button>
	<p><i class="fa fa-info-circle"></i> 由于Gavatar服务器不是很稳定，本站默认关闭了Gavatar头像的显示，如果你不上传自定义的头像，系统会默认设置选择一张图片作为头像。</p>
	<form enctype="multipart/form-data" method="post" class="hidde-form"><input type="file" name="user_avatar" class="user_avatar"/></form>
	</td>
  </tr>
  <tr>
	<td class="sysavatar_list" colspan="2">
	<h3>也可选择下方自己喜欢的头像</h3>
	<?php for($i=0; $i<=100; $i++){ ?>
		<a href="?select=<?php echo $i; ?>"><img src="http://www.wenjiangs.com/wp-content/uploads/sysavatar/<?php echo $i; ?>.jpg"></img></a>
	<?php } ?>
	</td>
  </tr>
</table>
</div>
</div>
</div>
</div>
<script>
$(function(){
	$('.user_avatar_btn').click(function(){
		$('.user_avatar').trigger('click');
	})
	$('.user_avatar').change(function(){
		$(this).parent('form').submit();
	})
})
</script>
<?php get_footer();?>

