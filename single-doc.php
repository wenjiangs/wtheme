<?php
global $this_post_id;

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

get_header();
$this_post_id = $post->ID; //记录当前postID
$docs = get_the_terms($post->ID, 'docs');
$docs = $docs[0];
$salong_posts = new WP_Query(
	array(
		'post_type' => 'doc',//自定义文章类型
		'ignore_sticky_posts' => 1,//忽略置顶文章
		'posts_per_page' => 10000,//显示的文章数量
		'order'		=> 'ASC',
		'tax_query' => array(
			array(
				'taxonomy' => 'docs',//分类法名称
				'field'    => 'id',//根据分类法条款的什么字段查询，这里设置为ID
				'terms'    => $docs->term_id,//分类法条款，输入分类的ID，多个ID使用数组：array(1,2)
			)
		),
	)
);
$prev = $next = null;
?>
<a href="<?php echo get_term_link($docs->term_id); ?>" class="backDocs"><i class="fa fa-chevron-circle-left"></i> 返回介绍</a>
<div class="doc_left">
  <div class="wt-container dlnBox">
    <ul class="doc_left_nav">
      <?php
      foreach($salong_posts->posts as $key => $cpost){
      if($this_post_id == $cpost->ID){
        if($key-1>=0){
          $prev = $salong_posts->posts[$key-1];
        }
        if($key+1<=count($salong_posts->posts)){
          $next = $salong_posts->posts[$key+1];
        }
      }
      ?>
      <li><a <?php if($this_post_id == $cpost->ID) echo 'class="active"';?> href="<?php echo get_permalink($cpost->ID); ?>">
        <?php echo $cpost->post_title; ?></a></li>
      <?php } ?>
      <li class="docNavCopy"><a href="<?php echo get_bloginfo('url'); ?>">Publish by wenjiangs</a></li>
    </ul>
  </div>
</div>
<div class="docBread">
  <div class="breadcrumbs pull-left doc_breadcrumbs"><a href="http://www.wenjiangs.com">文江博客</a> <small>&gt;</small>
  <?php if(!empty($docs)){ ?>
  <a href="<?php echo get_term_link($docs->term_id); ?>" rel="category tag">
  <?php echo $docs->name; ?></a> <small>&gt;</small>
  <?php } ?>
  文章详情</div>
  <div class="pull-right message_search">
    <form action="/">
    <button class="btn btn-info message_search_btn"><i class="fa fa-search"></i></button>
    <input class="form-control" name="s" placeholder="搜索当前分类文档">
    </form>
  </div>
</div>
<div class="nextPrev">
  <?php if(!empty($prev)){ ?>
  <a href="<?php echo get_permalink($prev->ID); ?>"><i class="fa fa-chevron-left"></i></a>
  <?php } ?>
  <?php if(!empty($next)){ ?>
  <a href="<?php echo get_permalink($next->ID); ?>" class="docnext"><i class="fa fa-chevron-right"></i></a>
  <?php } ?>
</div>
<div class="singleDocWrap">
  <div class="container afterdocBread">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="wt-container">
          
          <?php if(have_posts()):?>
          <?php
          while (have_posts()) : the_post();
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
          <h1 class="doc_right_tit"><?php the_title(); ?></h1>
          <div class="wt-single-meta">
            <span class="wt-info-model">发布于 <?php echo the_time('Y-m-d');?></span>
            <span class="wt-info-model">字数<?php echo $word_num; ?></span>
            <span class="wt-info-model">浏览 <?php echo $this_views;?></span>
            <span class="wt-info-model">评论 <?php echo $post->comment_count; ?></span>
            <?php if(current_user_can($current_user->ID, $this_post_id)){ ?>
            <?php if(!empty($current_user->roles) && in_array('administrator', $current_user->roles)){ ?>
            <?php edit_post_link(); ?>
            <?php } ?>
            <?php } ?>
          </div>
          <div class="wt-content"><?php the_content(); ?></div>
          <?php endwhile; endif; ?>
        </div>
        <?php include('comment.php'); ?>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>