<?php
function get_level($code){
    $code_arr = chunk_split($code, 2, ',');
    $code_arr = explode(',', $code_arr);
    if($code_arr[1].$code_arr[2].$code_arr[3].$code_arr[4].$code_arr[5] == 0){
        return array(2, $code_arr[0].'%00000000', $code_arr[0].'0000000000');
    }else if($code_arr[2].$code_arr[3].$code_arr[4].$code_arr[5] == 0){
        return array(3, $code_arr[0].$code_arr[1].'%000000',
        $code_arr[0].$code_arr[1].'%000000');
    }else if($code_arr[3].$code_arr[4].$code_arr[5] == 0){
        return array(4, $code_arr[0].$code_arr[1].$code_arr[2].'%000',
        $code_arr[0].$code_arr[1].$code_arr[2].'000000');
    }else{
        return array(5, $code_arr[0].$code_arr[1].$code_arr[2].$code_arr[3].$code_arr[4][0].'%',
        $code_arr[0].$code_arr[1].$code_arr[2].$code_arr[3].$code_arr[4][0].'000');
    }
}
if(isset($_GET['code'])){
    $level = get_level($_GET['code']);
    $sql = 'select * from static_data.area where area_code like "'.$level[1].'" and area_code <> "'.$level[2].'"';
}else{
    $sql = 'select * from static_data.area where area_code like "%0000000000"';
}
$area = $wpdb->get_results($sql);

if(isset($_GET['api'])){
  header('Content-type: application/json');
  header('Access-Control-Allow-Origin:*');
  echo json_encode($area);
  exit;
}

get_header();
?>
<div class="container">
  <div class="wt-container">
    <div class="breadcrumbs"><?php breadcrumbs(); ?></div>
    <h1 class="wt-single-title"><?php the_title(); ?></h1>
    <div class="wt-content">
    <p>2017年统计用区划代码和城乡划分代码</p>
    <p>统计用区划代码和城乡划分代码发布说明：</p>
        一、编制依据
    <p>2008年7月，国务院批复同意国家统计局与民政部、住建部、公安部、财政部、国土部、农业部共同制定的《关于统计上划分城乡的规定》（国函〔2008〕60号），自2008年8月1日实施，正式奠定了统计上划分城乡的理论依据和方法基础。随后，国家统计局印发《统计用区划代码和城乡划分代码编制规则》（国统字〔2009〕91号）。</p>
        二、区划范围
    <p>统计用区划代码和城乡划分代码的区划范围，是国家统计局开展统计调查的区划范围。未包括我国台湾省、香港特别行政区、澳门特别行政区。</p>
        三、发布内容
    <p>12位统计用区划代码和3位城乡分类代码。</p>
        四、适用领域
    <p>《国务院关于统计上划分城乡规定的批复》（国函〔2008〕60号）明确指出：“本规定作为统计上划分城乡的依据，不改变现有的行政区划、隶属关系、管理权限和机构编制，以及土地规划、城乡规划等有关规定”。各级各部门在使用统计用区划代码和城乡划分代码时，请务必结合实际情况。</p>
        五、补充编制开发区统计汇总识别码情况
    <p>为满足统计调查工作组织和数据汇总的需要，国家统计局对一些符合条件的开发区编制了统计汇总识别码。统计汇总识别码在统计用区划代码的县级码段上编制，其码段为71～80。</p>
    <p>编制统计汇总识别码的开发区应同时满足以下四个条件：一是国家和省人民政府正式批准成立的开发区；二是开发区的管理等同于县级人民政府，行使县级人民政府的管理职能，即管理开发区内的社会公共事务；三是开发区至少管理一个乡级单位；四是开发区管委会成立并运作两年及以上。</p>
    <ul class="xzqhUl">
        <?php foreach($area as $area_item){ ?>
        <?php if(isset($level) && $level[0] == 5){ ?>
        <li><?php echo $area_item->area_code; ?></li>
        <li><?php echo $area_item->area_name; ?></li>
        <?php }else{ ?>
        <li><a href="?code=<?php echo $area_item->area_code; ?>"><?php echo $area_item->area_code; ?></a></li>
        <li><a href="?code=<?php echo $area_item->area_code; ?>"><?php echo $area_item->area_name; ?></a></li>
        <?php } ?>
        <?php } ?>
    </ul>
    <h2>API 地址</h2>
    <pre>http://www.wenjiangs.com/division?api</pre>
    <p>默认查询所有的省份，如果传递 code 参数并设置为正确的省份行政区域划分代码，就返回市</p>
    <pre>http://www.wenjiangs.com/division?api&code=110000000000</pre>
    <p>所有数据来源于国家统计局（http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2017/index.html），如有侵权，请联系删除。</p>
  </div>
</div>
</div>
<?php get_footer(); ?>