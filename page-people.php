<?php
$paged = $paged == 0?$paged+1:$paged;
$paged_num = 20;

$lop_user = wp_cache_get('people_page_'.$paged);
if(!$lop_user){
	$lop_user = get_users(
		array(
			'number'		=> $paged_num,
			'orderby'		=> 'post_count',
			'order'			=> 'DESC',
			'offset'		=> $paged_num*($paged-1),
			'blog_id'		=> 1
		)
	);
	wp_cache_add('people_page_'.$paged, $lop_user);
}

$max_page = wp_cache_get('people_max_page');
if(!$max_page){
	global $wpdb;
	$user_total = $wpdb->get_var('select count(*) from wp_users');
	$max_page = ceil($user_total/$paged_num);
	wp_cache_add('people_max_page', $max_page);
}

get_header();
?>
<div class="container">
	<div class="row">
		<div class="col-md-8">
	<div class="wt-container">
		<div class="wt-nav-tabs">
			<h2 class="pull-left">所有用户</h2>
		</div>
		<div class="row no_pama">
		<?php
		foreach($lop_user as $key => $user_item){
			$follow_text = '关注';
			$follow_action = 'follow';
			$follow_count = collection_count($user_item->ID, 'user');
			if(is_user_logged_in()){
				//查询用户是否喜欢过
				$user_is_follow = is_follow_item($current_user->ID, $user_item->ID, 'user');
				if($user_is_follow > 0){
					$follow_text = '已关注';
					$follow_action = 'unfollow';
				}
			}
		?>
		<div class="col-md-6 no_pama">
			<div class="collection-wrap">
				<div class="collection-img"><a href="<?php echo get_author_posts_url($user_item->ID);?>"><?php echo get_avatar($user_item->ID);?></a>	</div>
				<div class="collection-text">
					<h3>
					<a follow_action = "<?php echo $follow_action; ?>"
            follow_count="<?php echo $follow_count; ?>"
            item_id="<?php echo $user_item->ID; ?>"
            item_type="user" href="javascript:" class="follow_btn pull-right">
						<i class="fa fa-heart"></i> <span><?php echo $follow_text; ?></span> <?php echo $follow_count; ?>
					</a>
					<a href="<?php echo get_author_posts_url($user_item->ID);?>" class="item_name"><?php echo $user_item->data->display_name?></a></h3>
					<div class=""><span><?php echo get_user_meta($user_item->ID, 'views', true);?></span> 人气 <span class="gap-point">·</span> <?php echo count_user_posts($user_item->ID, 'post');?> 文章</div>
					<p><?php if(get_user_meta($user_item->ID, 'description', true)){
					echo wp_trim_words(get_user_meta($user_item->ID, 'description', true), 36);
					}else{
						echo '这个人比较懒，什么都没有填写！';
					}
					?></p>
				</div>
			</div>
		</div>
		<?php } ?>
		</div>
		<ul class="pagenavi">
			<?php pagenavi($p=5, $max_page);?>
		</ul>
	</div>
	</div>
		<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer();?>