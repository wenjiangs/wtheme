<?php
get_header();
global $cat, $this_term_id, $current_user;
if(!$this_term_id) $this_term_id = $cat;
$term = get_term($this_term_id);

//更新term的浏览量
$cat_views = get_term_meta($term->term_id, 'views', true);
update_term_meta($term->term_id, 'views', ($cat_views+1));

$follow_text = '关注';
$follow_action = 'follow';

//判断是分类还是标签
if(is_category()){
	$item_type = 'category';
}else{
	$item_type = 'tag';
}

$follow_count = collection_count($this_term_id, $item_type);
if(is_user_logged_in()){
	//查询用户是否喜欢过
	$user_is_follow = is_follow_item($current_user->ID, $this_term_id, $item_type);
	if($user_is_follow > 0){
		$follow_text = '已关注';
		$follow_action = 'unfollow';
	}
}

?>

<div class="container">
	<div class="row">
		<div class="col-md-8">
      <div class="wt-container">
        <div class="category-head">
          <div class="category-img pull-left">
            <img src="/wp-content/themes/wtheme/images/placeholder.png"
            data-original="<?php echo get_term_meta($term->term_id, 'thumb', true); ?>"
            alt="<?php echo $term->name; ?>"></div>
          <div class="category-btn pull-right">
            <a href="/publish?cat=<?php echo $this_term_id; ?>" pjax="exclude" target="_blank" class="btn btn-info">投稿</a>
            <a follow_action = "<?php echo $follow_action; ?>" item_id="<?php echo $this_term_id; ?>"
              item_type="<?php echo $item_type; ?>" href="javascript:"
              follow_count="<?php echo $follow_count; ?>"
              class="follow_btn btn btn-success category-fllow-btn">
              <i class="fa fa-heart"></i>
              <span><?php echo $follow_text; ?></span> <?php echo $follow_count; ?>
            </a>
          </div>
          <div class="category-text">
            <h3><?php echo $term->name; ?></h3>
            <p>
              <span class="wt-info-model"><?php echo $term->count; ?> 文章</span>
              <span class="wt-info-model"><?php echo $cat_views+1; ?> 浏览</span>
            </p>
          </div>
        </div>
        <div class="wt-nav-tabs">
          <h2 class="pull-left">最新文章</h2>
        </div>
        <?php if(have_posts()):?>
        <?php while (have_posts()) : the_post(); $ws_has_thumb = false;?>
        <?php get_template_part('loop'); ?>
        <?php endwhile;?>
        <?php else:?>
        <div class="wp-info wp-info-default">
          <div class="wp-info-icon"><i class="fa fa-info"></i></div>
          <h3>这个页面没有内容！</h3>
          <p>这个页面的内容为空，可能是刚刚添加的分类或者新创建的标签页，管理员还没有添加内容，请过段时间再来访问。</p>
        </div>
        <?php endif;?>
        <?php if(have_posts()):?>
        <ul class="pagenavi">
          <?php pagenavi();?>
        </ul>
        <?php endif;?>
      </div>
    </div>
		<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
?>