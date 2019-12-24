<?php

$paged = $paged == 0?$paged+1:$paged;
$break_page = 20;
$sql = 'select * from wjdemos.runcode order by id desc limit '.($break_page*($paged-1)).', '.$break_page;
$list = $wpdb->get_results($sql);
$sql2 = 'select count(*) from wjdemos.runcode';
$max = $wpdb->get_var($sql2);
$max = ceil($max/$break_page);

get_header();
?>
<div class="container">
	<div class="row">
		<div class="col-md-8">
      <div class="wt-container">
      <div class="wt-nav-tabs">
        <h2 class="pull-left"><?php the_title(); ?></h2>
      </div>
      <div class="codeList">
        <?php foreach($list as $item){
          $item->code_name = trim($item->code_name);
          if(empty($item->code_name)){
            $item->code_name = '无标题';
          }
        ?>
        <div class="codeItem">
          <span class="codeItemDot"></span>
          <h3><a target="blank" pjax="exclude" href="/runCode/#/?code=<?php echo $item->code_guid; ?>"><?php echo $item->code_name; ?></a></h3>
          <a target="blank" pjax="exclude" href="/runCode/view/<?php echo $item->code_guid; ?>">预览</a>
          <span class="codeItemTime"><?php echo date('Y-m-d', $item->code_create_date); ?></span>
        </div>
        <?php } ?>
      </div>
      <div class="pagenavi"><?php pagenavi(7, $max); ?></div>
      </div>
    </div>
    <?php get_sidebar(); ?>
  </div>
</div>
<?php get_footer(); ?>