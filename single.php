<?php

//自定义CSS样式
if($custom_css = get_post_meta($post->ID, 'custom_css', true)){
	function add_style_to_head() {
		global $custom_css;
		echo '<style>'.$custom_css.'</style>';
	}
	add_action( 'wp_head', 'add_style_to_head' );
}

//自定义CSS文件
if($custom_css_link = get_post_meta($post->ID, 'custom_css_link', true)){
	function add_style_file_to_head() {
		global $custom_css_link;
		echo '<link href="'.$custom_css_link.'" rel="stylesheet">';
	}
	add_action( 'wp_head', 'add_style_file_to_head' );
}

//自定义JS脚本
if($custom_js = get_post_meta($post->ID, 'custom_js', true)){
	function add_script_to_foot() {
		global $custom_js;
		echo '<script>'.$custom_js.'</script>';
	}
	add_action( 'wp_footer', 'add_script_to_foot' );
}

//自定义JS文件
if($custom_js_link = get_post_meta($post->ID, 'custom_js_link', true)){
	function add_script_to_foot() {
		global $custom_js_link;
		echo '<script src="'.$custom_js_link.'"></script>';
	}
	add_action( 'wp_footer', 'add_script_to_foot' );
}

get_header();
?>
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
<div class="container">
<div class="row">
<div class="col-md-8">
<div class="wt-container">
<div class="breadcrumbs"><?php breadcrumbs(); ?></div>
<?php if(has_tag()){ ?>
<div class="wt-single-tags"><?php the_tags('', '', '');?></div>
<?php } ?>
<h1 class="wt-single-title"><?php the_title(); ?></h1>
<div class="wt-single-meta">
	<span class="wt-info-model">发布于 <?php echo the_time('Y-m-d');?></span>
	<span class="wt-info-model">字数 <?php echo $word_num; ?></span>
	<span class="wt-info-model">浏览 <?php echo $this_views;?></span>
	<span class="wt-info-model">评论 <?php echo $post->comment_count; ?></span>
	<?php if(current_user_can($current_user->ID, $this_post_id)){ ?>
	<?php if(!empty($current_user->roles) && in_array('administrator', $current_user->roles)){ ?>
	<a href="<?php get_edit_post_link(); ?>" pjax="exclude">编辑</a>
	<?php } ?>
	<?php } ?>
</div>
<div class="wt-content"><?php the_content(); ?></div>
<div class="wt-single-do">
	<div class="pull-left">
		<?php
		$follow_text = '关注';
		$follow_action = 'follow';
		$follow_count = collection_count($post->ID, 'post');
		if(is_user_logged_in()){
			//查询用户是否喜欢过
			$user_is_follow = is_follow_item($current_user->ID, $post->ID, 'post');
			if($user_is_follow > 0){
				$follow_text = '已关注';
				$follow_action = 'unfollow';
			}
		}
    
    // 查询关注量
		?>
		<a follow_action = "<?php echo $follow_action; ?>" item_id="<?php echo $post->ID; ?>"
      item_type="post" href="javascript:" class="follow_btn" follow_count="<?php echo $follow_count; ?>">
			<i class="fa fa-heart"></i> <span><?php echo $follow_text; ?></span> <?php echo $follow_count; ?>
		</a>
	</div>
	<div class="pull-right">
		<a href="javascript:" class="share-weixin"><i class="fa fa-weixin"></i></a><span id="qrcode"></span>
		<a class="share_to_weibo" href="http://service.weibo.com/share/share.php?url=<?php the_permalink();?>&title=<?php the_title();?>&appkey=498980039&searchPic=true" target="_blank"><i class="fa fa-weibo"></i></a>
		<a class="share_to_qq" href="http://connect.qq.com/widget/shareqq/index.html?url=<?php the_permalink();?>&title=<?php the_title();?>&desc=<?php echo $post->post_excerpt; ?>&summary=&site=uedsc" target="_blank"><i class="fa fa-qq"></i></a>
	</div>
</div>
<div class="jiaqun">
	<h3>如果你对这篇文章有疑问，欢迎到本站<a href="/topic">社区</a>发帖提问
	参与讨论，获取更多帮助。</h3>
	<img src="/wp-content/themes/wtheme/images/jiaqun_03.jpg" alt="扫码二维码加入Web技术交流群"/>
</div>
</div>

<?php
// 查询当前文章分类
$catObj = get_the_category($post->ID);
$args = array( 'numberposts' => 3, 'cat' => $catObj[0]->term_id, 'orderby' => 'rand', 'post_status' => 'publish' );
$rand_posts = get_posts( $args );
?>

<div class="singleRec">
  <div class="wt-container">
    <div class="mod-tit">
      <a href="<?php echo get_term_link($catObj[0]->term_id, 'category'); ?>" class="pull-right">更多 <i class="fa fa-angle-right"></i></a>
      <h3>你可能也喜欢</h3>
    </div>
    <div class="mod-con">
      <div class="sRecList">
        <?php
        $default_img = '/wp-content/themes/wtheme/images/placeholder.png';
        foreach($rand_posts as $rp){
          $img_url = '/wp-content/themes/wtheme/images/post_default_thumb/post_'.($rp->ID%10).'.jpg';
          if(has_post_thumbnail()){
            // 有特色图片
            $img_id = get_post_thumbnail_id();
            $img_arr = wp_get_attachment_image_src($img_id);
            $img_url = $img_arr[0];
          }else{
            // 文章里面有图
            $thumb = catch_that_image($rp);
            if(!empty($thumb)){
              foreach($thumb as $thumbItem){
                $ctImg = creat_thumb($thumbItem);
                if(!empty($ctImg)){
                  $img_url = $ctImg;
                  break;
                }
              }
            }
          }
        ?>
        <div class="sRecItem">
          <div class="sRecItem2">
            <div class="sRecImage">
              <a href="<?php echo get_permalink($rp->ID);?>">
              <img src="<?php echo $default_img; ?>"
                alt="<?php echo $rp->post_title; ?>" data-original="<?php echo $img_url; ?>"/>
              </a>
            </div>
            <h2><a href="<?php echo get_permalink($rp->ID);?>"><?php echo $rp->post_title; ?></a></h2>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<?php
$args = array(
  'numberposts' => 6,
  'orderby' => 'rand',
  'post_status' => 'publish',
  'orderby' => 'meta_value',
  'meta_key' => 'views',
  'order' => 'desc',
);
$rand_posts = get_posts( $args );
?>
<div class="singleRec">
  <div class="wt-container">
    <div class="mod-tit">
      <a href="/" class="pull-right">更多 <i class="fa fa-angle-right"></i></a>
      <h3>热门文章</h3>
    </div>
    <div class="mod-con">
      <div class="sRecList">
        <?php
        $default_img = '/wp-content/themes/wtheme/images/placeholder.png';
        $img_url = $default_img;
        foreach($rand_posts as $key => $rp){
          $img_url = '/wp-content/themes/wtheme/images/post_default_thumb/post_'.($rp->ID%10).'.jpg';
          if(has_post_thumbnail()){
            // 有特色图片
            $img_id = get_post_thumbnail_id();
            $img_arr = wp_get_attachment_image_src($img_id);
            $img_url = $img_arr[0];
          }else{
            // 文章里面有图
            $thumb = catch_that_image($rp);
            if(!empty($thumb)){
              foreach($thumb as $thumbItem){
                $ctImg = creat_thumb($thumbItem);
                if(!empty($ctImg)){
                  $img_url = $ctImg;
                  break;
                }
              }
            }
          }
        ?>
        <div class="sRecItem">
          <div class="sRecItem2">
            <div class="sRecImage">
              <a href="<?php echo get_permalink($rp->ID);?>">
              <img src="<?php echo $default_img; ?>"
                alt="<?php echo $rp->post_title; ?>" data-original="<?php echo $img_url; ?>"/>
              </a>
            </div>
            <h2><a href="<?php echo get_permalink($rp->ID);?>"><?php echo $rp->post_title; ?></a></h2>
          </div>
        </div>
        <?php if($key==2) echo '<div class="clearfix"></div>'; ?>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<div class="wt-container wt-pre-model">
	<div class="wt-prev-and-next">
		<p><?php if (get_previous_post()) { previous_post_link('上一篇：%link');} else {echo '没有了，已经是最后文章';} ?></p>
		<p><?php if (get_next_post()) { next_post_link('下一篇：%link');} else {echo '没有了，已经是最新文章';} ?></p>
	</div>
</div>
<?php include('comment.php'); ?>
</div>
<?php get_sidebar(); ?>
</div>
</div>
<?php endwhile; endif; ?>
<?php
get_footer();
?>