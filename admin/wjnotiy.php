<?php
global $wpdb, $paged;
if(isset($_GET['paged'])){
	$paged = $_GET['paged'];
}else{
	$paged = 0;
}
$break_page = 20;
$this_page = $paged == 0 ? 1 : $paged;

$wjnotiy = $wpdb->get_results("select * from wp_messages order by send_time desc limit ".(($this_page-1)*$break_page).", ".$break_page);

$source = array(
  'web'=>'网页',
  'weixinsp'=>'微信小程序',
  'alipaysp'=>'支付宝小程序',
  'qqsp'=>'qq小程序',
  'app'=>'app',
  'baidusp'=>'百度小程序',
  'mobile'=>'手机版',
);

$type = array(
  'private'=>'私信',
  'collection'=>'收藏',
  'comment'=>'评论',
  'system'=>'系统消息',
);

?>

<style>
.pagenavi{
    text-align: center;
    margin: 0;
    padding: 15px 0;
}
.pagenavi li {
    display: inline-block;
    margin: 0 3px;
    font-size: 12px;
}
.pagenavi a {
    display: block;
    border-radius: 3px;
    padding: 3px 10px 1px;
    color: #666;
}
.pagenavi .active a {
    background: #337AB7;
    color: #FFF;
}
</style>
<div class="wrap">
	<h1 style="margin-bottom:10px;">通知消息</h1>
  <table class="wp-list-table widefat fixed striped posts">
		<thead>
		<tr>
			<td>ID</td>
			<td>发送者</td>
			<td>接收者</td>
			<td>标题</td>
			<td>内容</td>
			<td>类型</td>
			<td>来源</td>
			<td>状态</td>
			<td>发送时间</td>
			<td width="64">操作</td>
		</tr>
		</thead>
		<tbody>
    <?php foreach($wjnotiy as $nItem){ ?>
    <tr>
      <td><?php echo $nItem->id; ?></td>
      <td><?php $user = get_user_by('id', $nItem->send_id); echo $user->data->display_name; ?></td>
      <td><?php $user = get_user_by('id', $nItem->accept_id); echo $user->data->display_name; ?></td>
      <td><?php echo $nItem->title; ?></td>
      <td><?php echo $nItem->content; ?></td>
      <td><?php echo $type[$nItem->type]; ?></td>
      <td><?php echo $source[$nItem->source]; ?></td>
      <td><?php echo $nItem->status==1?'未读':'已读'; ?></td>
      <td><?php echo date('Y-m-d H:i:s', $nItem->send_time); ?></td>
      <td></td>
    </tr>
    <?php } ?>
    </tbody>
  </table>
</div>


