<?php
header("Access-Control-Allow-Origin: *");
//银行卡校验
function check_bankCard($card_number){
	$arr_no = str_split($card_number);
	$last_n = $arr_no[count($arr_no)-1];
	krsort($arr_no);
	$i = 1;
	$total = 0;
	foreach ($arr_no as $n){
		if($i%2==0){
			$ix = $n*2;
			if($ix>=10){
				$nx = 1 + ($ix % 10);
				$total += $nx;
			}else{
				$total += $ix;
			}
		}else{
			$total += $n;
		}
		$i++;
	}
	$total -= $last_n;
	$x = 10 - ($total % 10);
	if($x == $last_n){
		return 'true';
	}else{
		return 'false';
	}
}

function get_bank_area($bank_card_code){
	global $wpdb;
	if(check_bankCard($bank_card_code)){
		$short_code = substr($bank_card_code, 0, 6);
		$sql = 'select * from static_data.bank_card_area where bank_code = "'.$short_code.'"';
    $bank_obj = $wpdb->get_results($sql);
		if(empty($bank_obj)){
			$sql = 'select * from static_data.bank_card_area where bank_code = "'.substr($short_code, 0, 5).'"';
      $bank_obj = $wpdb->get_results($sql);
			if(empty($bank_obj)){
				$sql = 'select * from static_data.bank_card_area where bank_code = "'.substr($short_code, 0, 4).'"';
        $bank_obj = $wpdb->get_results($sql);
				if(empty($bank_obj)){
					$sql = 'select * from static_data.bank_card_area where bank_code = "'.substr($short_code, 0, 3).'"';
          $bank_obj = $wpdb->get_results($sql);
					if(empty($bank_obj)){
						$msg = array('code'=>2, 'data'=>'暂时无法识别');
					}
				}
			}
		}
		$msg = array('code'=>1, 'data'=>$bank_obj[0]);
	}else{
		$msg = array('code'=>0, 'data'=>'卡号不正确');
	}
	return $msg;
}

//API接口
if(isset($_GET['api'])){
	if(isset($_GET['bank_card_code'])){
    header('Content-type: application/json');
    header('Access-Control-Allow-Origin:*');
		$msg = get_bank_area($_GET['bank_card_code']);
		echo json_encode($msg);
		exit;
	}
}

if(isset($_GET['backcode'])){
	$res = get_bank_area($_GET['backcode']);
}

$sql = 'select * from static_data.bank_card_area limit 0, 50';
$bank_card_area = $wpdb->get_results($sql);

get_header();
?>

<div class="container">
  <div class="wt-container">
    <div class="breadcrumbs"><?php breadcrumbs(); ?></div>
    <h1 class="wt-single-title"><?php the_title(); ?></h1>
    <div class="wt-content">
      <p>我们为您提供银行账号归属地查询，银行卡号归属地查询，查银行卡号，中国农业银行、工商银行、建设银行、交通银行、招商银行、邮政储蓄账号属地查询，银行卡号查询，归属地查询等多项服务</p>
      <h3>发卡银行查询API接口</h3>
      <p>为了方便广大开发者开发，本站特意编写了这个API接口，发送正确的银行卡号，就可以查询到该银行卡的发卡银行、卡类型等信息。</p>
      <h3>查询接口</h3>
      <table class="table">
        <tr>
          <td width="100">请求方式</td>
          <td>GET</td>
        </tr>
        <tr>
          <td>接口地址</td>
          <td>http://www.wenjiangs.com/bank-issuing-bank?api</td>
        </tr>
        <tr>
          <td>请求参数</td>
          <td>bank_card_code</td>
        </tr>
        <tr>
          <td>返回格式</td>
          <td>JSON</td>
        </tr>
        <tr>
          <td>请求示例</td>
          <td>http://www.wenjiangs.com/bank-issuing-bank?api&bank_card_code=6228480588345121572</td>
        </tr>
      </table>
      <h3>返回数据</h3>
      <p>本接口返回JSON格式的数据，code 参数用于检测查询是否成功，data 参数是具体的数据。</p>
      <h3>查询成功</h3>
      <pre>{"code":1,"data":{"bank_code":"622848","bank_name":"\u519c\u4e1a\u94f6\u884c","bank_user_type":"\u91d1\u7a57\u901a\u5b9d\u5361(\u94f6\u8054\u5361)","bank_card_type":"\u501f\u8bb0\u5361"}}</pre>
      <p>使用 PHP 中的 json_decode 即可解析成 object 对象。</p>
      <pre>stdClass Object
    (
        [code] => 1
        [data] => stdClass Object
            (
                [bank_code] => 622848
                [bank_name] => 农业银行
                [bank_user_type] => 金穗通宝卡(银联卡)
                [bank_card_type] => 借记卡
            )

    )</pre>
      <h3>查询失败</h3>
      <pre>stdClass Object
    (
        [code] => 0
        [data] => 暂时无法识别
    )</pre>	<pre>stdClass Object
    (
        [code] => 0
        [data] => 卡号不正确
    )</pre>
      <h3>数据样例</h3>
      <table class="table">
        <tr>
          <th>起始卡号</th>
          <th>发卡银行</th>
          <th>卡类型</th>
          <th>使用类型</th>
        </tr>
        <?php foreach($bank_card_area as $bank_card_area_item){ ?>
        <tr>
          <td><?php echo $bank_card_area_item->bank_code; ?></td>
          <td><?php echo $bank_card_area_item->bank_name; ?></td>
          <td><?php echo $bank_card_area_item->bank_user_type; ?></td>
          <td><?php echo $bank_card_area_item->bank_card_type; ?></td>
        </tr>
        <?php } ?>
      </table>
      </div>
    </div>
  </div>
<?php get_footer(); ?>