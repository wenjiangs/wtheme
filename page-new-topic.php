<?php
if(isset($_GET['id'])){  //存在ID则更新话题
	$edit_post = get_post($_GET['id']);
	if(!($current_user->ID == $edit_post->post_author)){
		wp_die('没有权限修改！');
	}
}

$rand_user = $current_user->ID;
if($current_user->ID == 1){
	if(isset($_POST['is_sys_user']) && $_POST['is_sys_user'] == 1){
		global $wpdb;
		$rand_user = $wpdb->get_var('SELECT * FROM wp_sys_user ORDER BY RAND() LIMIT 1');
	}
}

//处理表单
if(isset($_POST['submit'])){
	if(isset($_GET['id'])){  //存在ID则更新话题
		$post_id = $_GET['id'];
		$edit_post = array(
			'ID'		=> $post_id,
			'post_title' => $_POST['topic_title'],
			'post_content' => $_POST['post_content'],
			'post_category' => array($_POST['topic_category']),
		);
		wp_update_post( $edit_post );
		header("Location:".get_permalink($post_id));
	}else{  //不存在ID
		$is_insert = true;
		if($_POST['topic_title']==''){
			$err_msg = '请输入话题的标题，标题为空将不能通过审核！';
			$is_insert = false;
		}else if($_POST['post_content']==''){
			$err_msg = '请输入话题的内容，内容为空将不能通过审核！';
			$is_insert = false;
		}
		$edit_post = array(
			'post_author' => $rand_user,
			'post_title' => $_POST['topic_title'],
			'post_content' => $_POST['post_content'],
			'post_status' => "publish",
			'post_type' => "topic"
		);
		
		if($is_insert){
			$insert_id = wp_insert_post($edit_post);
			
			$taxonomy_id = $wpdb->get_var('select term_taxonomy_id from wp_term_taxonomy where term_id = '.$_POST['topic_category']);
			$wpdb->query('insert into wp_term_relationships values ('.$insert_id.', '.$taxonomy_id.', 0)');
			if($insert_id){
				header("Location:".get_permalink($insert_id));
			}
		}
	}
}

get_header();
wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');
wp_enqueue_script('my-upload');
wp_enqueue_style('thickbox');
$terms = get_terms(array('taxonomy'=>"group", 'hide_empty' => false));
?>
<div class="container">
<div class="col-md-10 col-md-offset-1">
<h1 class="wt-single-title"><?php the_title(); ?></h1>
<?php if(isset($err_msg)){?>
<div class="alert alert-danger"><?php echo $err_msg;?></div>
<?php }?>
<form method="post" class="public_form">
	<div class="editor_title"><input class="form-control topic_title" placeholder="这里输入话题标题" name="topic_title" ></div>
	<div class="editor_categroy">
		<select class="form-control" name="topic_category">
			<option value="0">请选择小组</option>
			<?php foreach($terms as $key => $terms_item){ ?>
			<option value="<?php echo $terms_item->term_id; ?>"><?php echo $terms_item->name; ?></option>
			<?php } ?>
		</select>
	</div>
	<div class="editor_main"><?php
		$arrs = array(
			'tinymce'=>1,
			'textarea_rows'=>20,
			'media_buttons' => false,
			'quicktags' => false,
		);
		wp_editor($edit_post->post_content, 'post_content', $arrs);
		?></div>
	<div class="alert alert-danger showMsg" style="display:none; margin-top:10px;"></div>
	<div class="othor-filed">
	<p class="pull-left">
		<a href="javascript:" class="btn btn-default pull-left media_btn"><i class="fa fa-upload"></i> 上传附件</a>
		<span class="upload-type">允许： jpg，jpeg，png，gif，zip，rar，pdf，psd</span>
	</p>
	<p class="pull-right">
	<input class="btn btn-primary" type="submit" value="发布话题" name="submit"/>
	</p>
	</div>
	<?php if($current_user->ID == 1){ ?>
	<span class="is_sys_user"><label for="is_sys_user"><input type="checkbox" id="is_sys_user" value="1" name="is_sys_user" /> 伪装成随机用户评论文章</label></span>
	<?php } ?>
</form>
</div>
</div>
<script>
$(function(){
	is_after_edit = false;
	
	function insertCon(txt) {
		tinyMCE.execCommand('mceInsertContent', false, txt);
	}
	
	//验证表单
	$('.public_form').submit(function(){
		is_submit = true;
		if($('.topic_title').val() == ''){
			showMsg('话题标题不能为空！')
			is_submit = false;
		}
		
		if(tinyMCE.activeEditor.getContent() == ''){
			showMsg('话题内容不能为空！')
			is_submit = false;
		}
		return is_submit;
		
	})
	function showMsg(txt){
		$('.showMsg').html(txt).fadeIn(400);
		setTimeout(function(){$('.showMsg').fadeOut(400)}, 5000)
	}
	
	//修改过页面，询问是否更改
	//阻止关闭网页
	window.onbeforeunload = function () {
		if(is_after_edit){
			return true;
		}
	};

	
    jQuery('.media_btn').click(function() {
		tb_show('', '<?php echo admin_url(); ?>media-upload.php?type=image&amp;TB_iframe=true');
		return false;
    });
    window.send_to_editor = function(html) {
		imgurl = jQuery('img', html).attr('src');
		insertCon('<img src="'+imgurl+'"/>');
		tb_remove();
    }
})
</script>
<?php
get_footer();
?>