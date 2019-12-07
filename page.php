<?php
get_header();

$donation_list = array(
	array('刘**', '10.00', '刘宇华'),
	array('夏目漱石', '10.00', '霍抒杨'),
	array('唐**', '12.80', '唐亭亭'),
	array('王**', '10.00', '王明星'),
	array('张**', '30.00', '张华涛'),
	array('张兵*', '30.00', '张兵兵'),
	array('方*', '50.00', '方'),
	array('何*', '100.00', '何'),
	array('刘*', '5.00', '刘'),
	array('伟*', '2.00', '伟杰'),
	array('zhangt', '2.00', ''),
	array('leung', '2.00', ''),
	array('熊熊', '2.00', ''),
	array('Say Bye', '2.00', ''),
	array('军魂', '2.00', ''),
	array('倣棄、', '2.00', ''),
	array('Dir', '2.00', ''),
	array('指着心说这里*', '2.00', ''),
	array('四月是你的谎言', '2.00', ''),
	array('小行星', '2.00', ''),
	array('二黑', '8.88', ''),
	array('栓子', '36.00', ''),
);

?>
<div class="container">
	<div class="row">
		<?php get_sidebar('page'); ?>
		<div class="col-md-9">
			<div class="wt-container">
			<div class="breadcrumbs"><?php breadcrumbs(); ?></div>
			<?php if(have_posts()):?>
			<?php while (have_posts()):the_post();
			$this_post_id = $post->ID; //记录当前postID
			$this_post_author = $post->post_author; //记录当前作者ID
			$this_views = get_post_meta($this_post_id, 'views', true);
			if($this_views < 500){
				$rand_views = rand(300, 500);
				$this_views = $this_views + $rand_views;
			}else{
				$this_views = $this_views + 1;
			}
			update_post_meta($this_post_id, 'views', $this_views); //更新浏览量
			$word_num = mb_strwidth($post->post_content);
			$comment_args = array(
				'post_author' => $post->post_author, // use post_id, not post_ID
				'count' => true //return only the count
			);
			$comment_count = get_comments($comment_args);
			?>
			<?php if(has_tag()){ ?>
			<div class="wt-single-tags"><?php the_tags('', '', '');?></div>
			<?php } ?>
			<h1 class="wt-single-title"><?php the_title(); ?></h1>
			<div class="wt-single-meta">
				<span class="wt-info-model">发布于 <?php echo the_time('Y-m-d');?></span>
				<span class="wt-info-model">字数 <?php echo $word_num; ?></span>
				<span class="wt-info-model">浏览 <?php echo $this_views;?></span>
				<span class="wt-info-model">评论 <?php echo $post->comment_count; ?></span>
				
			</div>
			<div class="wt-content"><?php the_content(); ?></div>
			<?php endwhile;?>
			<?php endif;?>
			</div>

			<?php if(is_page('friendship')){ //友情链接?>
			<div class="wt-container wt-pre-model">
			<ul class="friend_link_list">
			<?php
			$friend_link = get_bookmarks( array('category'=>82) );
			foreach($friend_link as $friend_link_item):
			?>
			<li><a href="<?php echo $friend_link_item->link_url;?>" target="_blank" title="<?php echo $friend_link_item->link_description;?>"><i class="fa fa-globe"></i> <?php echo $friend_link_item->link_name;?></a></li>
			<?php endforeach;?>
			</ul>
			</div>
			<?php } ?>

			<?php if(is_page('donation')){ //友情链接?>
			<div class="donation alert alert-warning">
				<h2><i class="fa fa-heart"></i> 爱心墙 <i class="fa fa-heart"></i></h2>
				<ul>
				<?php foreach($donation_list as $key => $donation_item){?>
					<li <?php if($key%2==1) echo 'class="li-odd"';?>><span class="pull-right">捐赠：￥<?php echo $donation_item[1];?></span><i class="fa fa-circle"></i> <?php echo $donation_item[0];?></li>
				<?php }?>
				</ul>
			</div>
			<?php }?>
			<?php include('comment.php'); ?>
		</div>
	</div>
</div>
<?php
get_footer();
?>