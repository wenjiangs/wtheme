<?php
global $wpdb;

$paged = $_GET['paged'];
if(empty($paged) || $paged == 0){
	$paged = 1;
}
$break_page = 20;

$results = $wpdb->get_results("select * from wp_users, wp_accusation where wp_users.ID = wp_accusation.user_id order by accusation_date desc limit ".(($paged-1)*$break_page).", ".$break_page);

//统计
$count_data = $wpdb->get_var('select count(*) from wp_accusation');
$max_page = ceil($count_data/$break_page);

?>
<div class="wrap">
<h1 class="wrap-h1" style="margin-bottom:10px; ">内容举报</h1>
  <table class="wp-list-table widefat fixed striped posts">
  <thead>
    <tr>
      <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"></td>
      <th width="24">ID</th>
      <th width="80">用户名</th>
      <th>举报地址</th>
      <th width="80">举报类型</th>
      <th>举报内容</th>
      <th width="160">举报时间</th>
    </tr>
  </thead>
  <tbody>
    <?php if(empty($results)){ ?>
    <tr>
    <td colspan="7">暂无数据</td>
    </tr>
    <?php }else{ ?>
    <?php foreach($results as $rs){ ?>
    <tr>
      <th class="check-column"><input id="cb-select-all-1" type="checkbox"></th>
      <th><?php echo $rs->id; ?></th>
      <th><?php echo $rs->display_name; ?></th>
      <th><?php echo $rs->accusation_path; ?></th>
      <th><?php echo $rs->accusation_type; ?></th>
      <th><?php echo $rs->accusation_content; ?></th>
      <th><?php echo $rs->accusation_date; ?></th>
    </tr>
    <?php } ?>
    <?php } ?>
  </tbody>
  </table>
  <?php if(!empty($results)){ ?>
  <ul class="pagenavi">
		<?php pagenavi(8, $max_page);?>
	</ul>
  <?php } ?>
</div>