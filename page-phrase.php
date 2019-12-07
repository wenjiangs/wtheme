<?php
$breakpage = 15;
$paged = $paged == 0?$paged+1:$paged;
$type = 0;

$typeArr = array('你好污', '毒鸡汤', '心灵鸡汤', '笑话');

$custom_title = '';

$sql = 'select * from wjdemos.phrase where id > 0 ';
$sql_count = 'select count(*) from wjdemos.phrase where id > 0 ';

if(isset($_GET['type']) && $_GET['type']>0){
  $type = $_GET['type'];
  $sql .= ' and type = '.$_GET['type'];
  $sql_count .= ' and type = '.$_GET['type'];
  if(!empty($typeArr[$_GET['type']])){
    $custom_title = $typeArr[$_GET['type']-1].' - ';
  }
}

$sql .= ' order by id desc limit '.(($paged-1)*$breakpage).', '.$breakpage;
$phrase = $wpdb->get_results($sql);
$phrase_count = $wpdb->get_var($sql_count);
$max_page = ceil($phrase_count/$breakpage);
get_header();
?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <div class="phrase_nav">
        <a href="<?php echo get_permalink($post->ID); ?>" <?php if($type==0) echo 'class="active"';?>>全部</a>
        <?php foreach($typeArr as $key => $item){ ?>
        <a href="<?php echo get_permalink($post->ID); ?>?type=<?php echo $key+1; ?>" <?php if($type==($key+1)) echo 'class="active"';?>>
          <?php echo $item; ?></a>
        <?php } ?>
      </div>
      <div class="phrase_list">
        <?php foreach($phrase as $phrase_item){ ?>
        <div class="phrase_item">
          <div class="wt-content"><?php echo wpautop($phrase_item->content); ?></div>
          <div class="phrase_meta">
            <span class="wt-info-model"><i class="fa fa-thumbs-o-up"></i> 顶</span>
            <span class="wt-info-model"><i class="fa fa-thumbs-o-down"></i> 踩</span>
            <span class="wt-info-model"><i class="fa fa-comment-o"></i> 评论</span>
          </div>
        </div>
        <?php } ?>
      </div>
      <ul class="pagenavi">
        <?php pagenavi(7, $max_page);?>
      </ul>
    </div>
    <?php get_sidebar(); ?>
  </div>
</div>

<?php get_footer(); ?>