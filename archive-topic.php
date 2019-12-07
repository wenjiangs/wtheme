<?php
$terms = get_terms(array('taxonomy'=>"group", 'hide_empty' => false));
get_header();
?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <div class="phrase_nav">
        <a href="javascript:" class="active">话题</a>
        <a href="javascript:">小组</a>
      </div>
      <div class="wt-container">
        <div class="wt-nav-tabs">
          <h2 class="pull-left">
          <?php if(isset($wp_query->query['group'])){
          $pageTerm = get_term_by('slug', $wp_query->query['group'], 'group');
          echo $pageTerm->name;
          ?>
          <?php }else{ ?>
          最新话题
          <?php } ?>
          </h2>
        </div>
        <?php if(have_posts()):?>
        <?php while (have_posts()) : the_post();?>
        <?php get_template_part('loop', 'topic'); ?>
        <?php endwhile;?>
        <ul class="pagenavi">
          <?php pagenavi();?>
        </ul>
        <?php else:?>
        <div class="wp-info wp-info-default">
          <div class="wp-info-icon"><i class="fa fa-info"></i></div>
          <h3>这个页面没有内容！</h3>
          <p>这个页面的内容为空，可能是刚刚添加的分类或者新创建的标签页，管理员还没有添加内容，请过段时间再来访问。</p>
        </div>
        <?php endif; ?>
      </div>
      <div class="wt-container" style="display:none; padding-bottom:20px;">
        <?php
        foreach($terms as $terms_item){
          $follow_text = '关注';
          $follow_action = 'follow';
          $follow_count = collection_count($terms_item->term_id, 'docs');
          if(is_user_logged_in()){
            //查询用户是否喜欢过
            $user_is_follow = is_follow_item($current_user->ID, $terms_item->term_id, 'docs');
            if($user_is_follow > 0){
              $follow_text = '已关注';
              $follow_action = 'unfollow';
            }
          }
        ?>
          <div class="groupItem">
            <div class="groupItem-img">
              <a href="<?php echo get_term_link($terms_item->term_id); ?>">
              <img src="<?php echo '/wp-content/themes/wtheme/images/group-'.$terms_item->term_id.'.png'; ?>"/>
              </a>
            </div>
            <div class="groupItem-text">
              <a follow_action = "<?php echo $follow_action; ?>"
                item_id="<?php echo $user_item->ID; ?>"
                follow_count="<?php echo $follow_count; ?>"
                item_type="user"
                href="javascript:"
                class="follow_btn pull-right">
                <i class="fa fa-heart"></i> <span><?php echo $follow_text; ?></span> <?php echo $follow_count; ?>
              </a>
              <h3>
                <a href="<?php echo get_term_link($terms_item->term_id); ?>">
                <?php echo $terms_item->name; ?>
                </a>
              </h3>
              <div class="riMeta">
                <span class="wt-info-model"><i class="fa fa-file-text-o"></i> 话题 <?php echo $terms_item->count; ?></span>
                <span class="wt-info-model"><i class="fa fa-comment-o"></i> 回复 <?php echo get_group_reply_number($terms_item->term_id); ?></span>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
    <?php get_sidebar(); ?>
  </div>
</div>
<script>
$(function(){
  $('.phrase_nav a').click(function(){
    $('.phrase_nav a').removeClass('active');
    $(this).addClass('active');
    $('.col-md-8 .wt-container').hide();
    $('.col-md-8 .wt-container').eq($(this).index()).show();
  })
})
</script>
<?php get_footer(); ?>
