<?php
$post_arr = array(
	'numberposts'	=> 3,
	'author'		=> $current_user->ID,
	'post_author'	=> $current_user->ID
);
$posts = get_posts($post_arr);

get_header();
?>
<div class="container">
	<div class="row">
		<?php get_sidebar('user'); ?>
		<div class="col-md-9">
			<ul class="user_quick_publish">
				<li><a href="/publish"><i class="fa fa-file-text-o"></i> 写文章</a></li>
				<li><a href="/"><i class="fa fa-commenting-o"></i> 发评论</a></li>
				<li><a href="/new-topic"><i class="fa fa-sticky-note-o"></i> 提问题</a></li>
				<li><a href="/topic"><i class="fa fa-comments-o"></i> 社区讨论</a></li>
				<li><a href="/group/feedback"><i class="fa fa-question-circle-o"></i> 需要帮助</a></li>
			</ul>
			
			<div class="wt-container" style="overflow:auto;">
				<h3 class="wt-setting-tit">我的文章 <a href="/public" class="btn btn-success btn-xs">写文章</a></h3>
				<?php if(empty($posts)){ ?>
				<div class="wp-info wp-info-warning">
					<div class="wp-info-icon"><i class="fa fa-info"></i></div>
					<h3>没有找到任何内容！</h3>
					<p>您查找的内容可能被删除或者更换了名字，亦或者是您手残输错误，当然不排除阁下人品问题，也有可能是电信网通那头接口生锈了。</p>
				</div>
				<?php }else{ ?>
					<?php foreach($posts as $post){ setup_postdata($post); ?>
					<?php get_template_part('loop'); ?>
					<?php } ?>
				<?php } ?>
				<a class="btn btn-info user_item_more" href="/user/articles">查看更多文章</a>
			</div>
			
		</div>
	</div>
</div>
<?php get_footer(); ?>