<div class="loop-list">
	<?php
	//如果是手册，直接获取分类
	if($post->post_type == 'doc'){
		$loop_category = get_the_terms($post->ID, 'docs');
	}
	$trim_words_num = 80;
	$thumb = catch_that_image();
  if(!empty($thumb)){
    $temThumb = array();
    foreach($thumb as $thumbItem){
      $temImage = creat_thumb($thumbItem);
      if(!empty($temImage)){
        $temThumb[] = $temImage;
      }
      if(count($temThumb) == 4) break; // 多于 4 张图停止生成
    }
    $thumb = $temThumb;
  }
	$follow_count = $wpdb->get_var('select count(*) from wp_user_item_taxonomy where item_id = '.$post->ID.' and item_type = "'.$post->post_type.'"');
	if(count($thumb) < 4){
	if(has_post_thumbnail()){
		$ws_has_thumb = true;
	?>
	<div class="loop-img pull-right">
	<a href="<?php the_permalink();?>" title="<?php the_title(); ?>"><?php the_post_thumbnail(); ?></a>
	</div>
	<?php
	}else{
	if(!empty($thumb)){
		$thumb_item = $thumb[0];
		$ws_has_thumb = true;
	?>
	<div class="loop-img pull-right">
	<a href="<?php the_permalink();?>" title="<?php the_title(); ?>">
  <img src="/wp-content/themes/wtheme/images/placeholder.png" data-original="<?php echo $thumb_item; ?>" alt="<?php the_title(); ?>" />
  </a>
	</div>
	<?php }}} ?>
	
	<div class="loop-txt <?php if(!$ws_has_thumb){ echo 'loop_no_thumb'; } ?>">
		<h3>
		<a href="<?php the_permalink();?>" title="<?php the_title(); ?>">
		<?php if(isset($loop_category) && !empty($loop_category)){ echo $loop_category[0]->name.' - '; } ?>
		<?php the_title(); ?>
		</a>
		<?php if(is_sticky()) echo '<span class="loop-sticky">置顶</span>'; ?>
		</h3>
		<?php if(count($thumb) > 3){ ?>
		<ul class="article-img">
			<?php
			foreach($thumb as $key => $thumb_item){
				if($key > 3) break;
			?>
			<li><div><a href="<?php the_permalink();?>"><img src="/wp-content/themes/wtheme/images/placeholder.png" alt="<?php the_title(); ?>" data-original="<?php echo $thumb_item; ?>"/></a></div></li>
			<?php } ?>
		</ul>
		<?php }else{ /*$trim_words_num = 50;*/ } ?>
    <?php if(empty($post->post_excerpt)){ ?>
			<p class="loop-mul-img-info"><?php echo wp_trim_words($post->post_content, $trim_words_num); ?></p>
    <?php }else{ ?>
			<p class="loop-mul-img-info"><?php if(!(get_the_excerpt() == $post->post_title)) echo wp_trim_words(get_the_excerpt(), $trim_words_num); ?></p>
    <?php } ?>
    <div class="loopMeta">
      <div class="list-top pull-left">
        <span class="wt-info-model">
        <a href="<?php echo get_author_posts_url($post->post_author);?>" class="loop-user-name">
          <?php echo get_avatar($post->post_author); ?>
        </a>
        <a href="<?php echo get_author_posts_url($post->post_author);?>">
        <?php
        $author_name = get_the_author();
        if(empty($author_name)){
          $author_obj = get_user_by('id', $post->post_author);
          $author_name = $author_obj->data->display_name;
        }
        echo $author_name;
        ?>
        </a></span>
        <span class="wt-info-model"><?php echo time_since(get_the_time('Y-m-d H:i:s')); ?></span>
      </div>
      <div class="list-footer pull-right">
        <span class="wt-info-model"><i class="fa fa-eye"></i> 浏览 <?php echo get_post_meta($post->ID, 'views', true);?></span>
        <span class="wt-info-model"><i class="fa fa-comment-o"></i> 评论 <?php echo $post->comment_count; ?></span>
        <span class="wt-info-model"><i class="fa fa-heart-o"></i> 喜欢 <?php echo $follow_count; ?></span>
      </div>
    </div>
	</div>
</div>