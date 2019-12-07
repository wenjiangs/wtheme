<div class="loop-list bleat-list">
  <div class="loop-avatar pull-left">
  <a href="<?php echo get_author_posts_url($post->post_author);?>">
  <?php echo get_avatar($post->post_author); ?>
  </a>
  </div>
	<div class="loop-txt">
		<div class="list-top">
			<span class="wt-info-model">
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
    <div class="loop-mul-img-info topic-loop-txt">
        <?php if(!empty($post->post_title)){ ?>
            <?php the_title(); ?> - 
	        <?php echo wp_trim_words($post->post_content, 100); ?>
            <a href="<?php the_permalink();?>">查看全文</a>
        <?php }else{ ?>
        <?php global $Parsedown; echo wpautop($Parsedown->text($post->post_content)); ?>
	    <?php } ?>
    </div>
    <?php
    $imgList = get_post_meta($post->ID, 'imgList', true);
    if(empty($imgList)){
	    $imgList = catch_that_image();
    }
    if(!empty($imgList)){
      if(count($imgList)>1){
    ?>
    <ul class="article-img blear-img">
      <?php
        foreach($imgList as $key => $imgItem){
          $bigImage = $imgItem;
	        $imgItem = creat_thumb($imgItem, 120, 120);
          if($key >= 9) break;
      ?>
          <li><a class="fancybox" rel="group-<?php echo $post->ID; ?>" href="<?php echo $bigImage; ?>">
              <img src="/wp-content/themes/wtheme/images/placeholder.png"
                   data-original="<?php echo $imgItem; ?>"/></a></li>
      <?php } ?>
    </ul>
    <?php }else{ ?>
          <div class="bleat-alone-image">
              <a class="fancybox" rel="group-<?php echo $post->ID; ?>"
                 href="<?php echo $imgList[0]; ?>">
                  <img src="/wp-content/themes/wtheme/images/placeholder.png"
                       data-original="<?php echo creat_thumb($imgList[0], 380, 285); ?>"/></a></div>
    <?php } ?>
    <?php } ?>
		<div class="list-footer">
      <div class="pull-left">
        <span class="wt-info-model">来自网站</span>
      </div>
      <div class="pull-right">
			<span class="wt-info-model"><i class="fa fa-eye"></i> 浏览 <?php echo get_post_meta($post->ID, 'views', true);?></span>
			<span class="wt-info-model"><i class="fa fa-comment-o"></i> 评论 <?php echo $post->comment_count; ?></span>
			<span class="wt-info-model"><i class="fa fa-heart-o"></i> 喜欢 <?php echo $follow_count; ?></span>
      </div>
		</div>
	</div>
</div>