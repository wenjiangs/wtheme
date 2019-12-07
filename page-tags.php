<?php
$paged = $paged == 0?$paged+1:$paged;
$paged_num = 20;

//缓存列表
$tags = wp_cache_get('tags_page_'.$paged);
if(!$tags){
	$tags = get_tags(
		array(
			'number'		=> $paged_num,
			'orderby'		=> 'count',
			'order'			=> 'DESC',
			'offset'		=> $paged_num*($paged-1)
		)
	);
	wp_cache_add('tags_page_'.$paged, $tags);
}

//缓存最大页数
$max_page = wp_cache_get('tags_max_page');
if(!$max_page){
	global $wpdb;
	$tags_total = $wpdb->get_var('select count(*) from wp_term_taxonomy where taxonomy = "post_tag"');
	$max_page = ceil($tags_total/$paged_num);
	wp_cache_add('tags_max_page', $max_page);
}
get_header();

?>

<div class="container">
	<div class="row">
		<div class="col-md-8">
	<div class="wt-container">
		<div class="wt-nav-tabs">
			<h2 class="pull-left">所有标签</h2>
		</div>
	<div class="row no_pama">
	<?php
	foreach($tags as $key => $tags_item){
		$tag_thumb = get_term_meta($tags_item->term_id, 'thumb', true);
		if(!$tag_thumb){
			$tag_thumb = '/wp-content/themes/wtheme/images/placeholder.png';
		}
		$follow_text = '关注';
		$follow_action = 'follow';
		$follow_count = collection_count($tags_item->term_id, 'tag');
		if(is_user_logged_in()){
			//查询用户是否喜欢过
			$user_is_follow = is_follow_item($current_user->ID, $tags_item->term_id, 'tag');
			if($user_is_follow > 0){
				$follow_text = '已关注';
				$follow_action = 'unfollow';
			}
		}
	?>
		<div class="col-md-6 no_pama">
		<div class="collection-wrap">
			<div class="collection-img"><a href="<?php echo get_tag_link($tags_item->term_id); ?>" target="blank">
        <img src="<?php echo $tag_thumb; ?>" alt="<?php echo $tags_item->name; ?>" />
      </a></div>
			<div class="collection-text">
				<h3>
				<a follow_action = "<?php echo $follow_action; ?>"
          item_id="<?php echo $tags_item->term_id; ?>"
          follow_count="<?php echo $follow_count; ?>"
          item_type="tag" href="javascript:" class="follow_btn pull-right">
				<i class="fa fa-heart"></i> <span><?php echo $follow_text; ?></span> <?php echo $follow_count; ?>
				</a>
				<a href="<?php echo get_tag_link($tags_item->term_id); ?>" class="item_name" target="blank"><?php echo $tags_item->name; ?></a>
				</h3>
				<div>文章 <?php echo $tags_item->count; ?> · 浏览 <?php echo get_term_meta($tags_item->term_id, 'views', true);?></div>
				<p><?php if(empty($tags_item->description)) echo '还没有描述！'; else echo wp_trim_words($tags_item->description, 36);?></p>
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
<?php get_footer(); ?>