<?php
get_header();

$goodsMeta = get_goods_meta($post->ID, false);
//print_r($goodsMeta);
// 店铺地址
$merchant_address = get_option('merchant_address');
if($merchant_address){
  $merchant_address = str_replace(array('[', ']'), array('(', ')'), $merchant_address);
  $sql = 'select * from wp_city_list where city_code in '.$merchant_address;
  $merchant_address = $wpdb->get_results($sql);
}

// 销售区域
$sales_area = get_option('sales_area');
echo "<script>var sales_area = '".$sales_area."'; </script>";

// 查询运费模板
$freight_id = get_post_meta($post->ID, 'freight_id', true);
if($freight_id){
  $sql = 'select * from wp_shop_freight where id = '.$freight_id;
  $sql2 = 'select * from wp_shop_freight_meta where freight_id = '.$freight_id;
  $freight = $wpdb->get_results($sql);
  $freight_meta = $wpdb->get_results($sql2);
  $freight = $freight[0];
  $freight->meta = $freight_meta;
  echo "<script>var freight = '".json_encode($freight)."'; </script>";
}

// 查询省
$province = get_province();
echo "<script>var province = '".json_encode($province)."'; </script>";
?>

<div class="container" id="gSingle">
  <div class="gsBox wt-container">
    <div class="row">
      <div class="col-md-6">
        <div class="gsImg">
          <div class="gsThumb">
            <?php foreach($goodsMeta['thumb'] as $key => $thumb){ ?>
            <span <?php if($key==0) echo 'class="active"';?>><img src="<?php echo $thumb; ?>"/></span>
            <?php } ?>
          </div>
          <div class="gsAlbum"><img src="<?php echo $goodsMeta['thumb'][0]; ?>"/></div>
        </div>
        <ul class="gsEquity">
          <li>100%人工质检</li>
          <li>全国包邮低价保障</li>
          <li>7天无理由退货</li>
        </ul>
        <ul class="gsEquity gsEDo">
          <li><a href="#"><i class="fa fa-heart"></i> 收藏</a></li>
          <li><a href="#"><i class="fa fa-share-alt"></i> 分享</a></li>
          <li><a href="#"><i class="fa fa-warning"></i> 举报</a></li>
        </ul>
      </div>
      <div class="col-md-6">
        <div class="gsRight">
          <h2 class="gsTitle"><?php echo $post->post_title; ?></h2>
          <div class="gsExcerpt"><?php echo $post->post_excerpt; ?></div>
          <div class="gsPrices">
            <div class="prices">
              <dl class="gsItemDl">
                <dt>参考价</dt>
                <dd><span class="market-prices"><i>￥</i><?php echo number_format($goodsMeta['market_prices'], 2); ?></span></dd>
              </dl>
              <dl class="gsItemDl gsSalePrices">
                <dt>价格</dt>
                <dd><span class="sale-prices"><i>￥</i><?php echo number_format($goodsMeta['sale_prices'], 2); ?></span></dd>
              </dl>
            </div>
          </div>
          <dl class="gsItemDl gsDelivery">
            <dt>配送</dt>
            <dd><?php
            echo $merchant_address[0]->city_name;
            if(!($merchant_address[1]->city_name == '市辖区')){
              echo $merchant_address[1]->city_name;
            }
            echo $merchant_address[2]->city_name;
            ?> 到
              <span class="gsSelectAr">{{thisSelectCity.city_name}} <i class="fa fa-angle-down"></i></span>
              <strong v-if="inStock">有货</strong>
              <strong v-else class="noStock">无货</strong>
              快递：{{freightPrices}}元
            </dd>
          </dl>
          <div class="selectCity">
            <div class="selectCityBox">
              <div class="scbHead">
                请选择要配送的省份
                <i class="fa fa-times pull-right closeSC"></i>
              </div>
              <ul>
                <li v-for="item in province" @click="selectCity(item)">{{item.city_name}}</li>
              </ul>
            </div>
          </div>
          <?php foreach($goodsMeta['sale_attr'] as $item){ ?>
          <dl class="gsItemDl gsDlAttr">
            <dt><?php echo $item['attr_name']; ?></dt>
            <dd>
              <?php foreach($item['attr_value'] as $itemVal){ ?>
              <span><?php echo $itemVal['value']?></span>
              <?php } ?>
            </dd>
          </dl>
          <?php } ?>
          <dl class="gsItemDl gsDlNum">
            <dt>数量</dt>
            <dd>
              <div class="gsPayNum">
                <span class="gsPayNumBtn" @click="gsPayNumReduce">-</span><input type="text" v-model="payNum"/><span class="gsPayNumBtn" @click="gsPayNumPlus">+</span>
              </div>
            </dd>
          </dl>
          <dl class="gsItemDl gsBtn">
            <dd>
              <button type="button" :disabled="!inStock" class="btn btn-pay-empty">立刻购买</button>
              <button type="button" :disabled="!inStock" class="btn btn-pay"><i class="fa fa-shopping-cart"></i> 加入购物车</button>
            </dd>
          </dl>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
var gSingle = new Vue({
  el: "#gSingle",
  data: {
    province: JSON.parse(province),
    freight: JSON.parse(freight),
    payNum: 1,
    freightPrices: <?php echo $freight->meta[0]->first_charge; ?>,
    thisSelectCity: {city_name:'请选择'},
    inStock: true,
  },
  methods:{
    selectCity(city){
      if(typeof city.city_code == 'undefined'){
        this.calcFreight(this.freight.meta[0]);
        return;
      }
      
      this.thisSelectCity = city;
      
      // 查询是否在销售区域
      if(!(sales_area.indexOf(this.thisSelectCity.city_code) != -1)){
        this.inStock = false;
      }else{
        this.inStock = true;
      }
      
      var isFond = false;
      for(let i=1; i<this.freight.meta.length; i++){
        var region = this.freight.meta[i].region;
        if(region.indexOf(this.thisSelectCity.city_code) != -1){
          this.calcFreight(this.freight.meta[i]);
          isFond = true;
          break;
        }
      }
      if(!isFond){
        this.calcFreight(this.freight.meta[0]);
      }
      console.log(this.freightPrices, isFond);
      $('.selectCity').hide();
    },
    calcFreight(region){
      this.freightPrices = region.first_charge;
      var caoNum = this.payNum - region.first_count;
      if(caoNum>0){
        this.freightPrices = this.freightPrices*1 + Math.ceil(caoNum/region.second_count) * region.second_charge;
      }
    },
    gsPayNumReduce(){
      this.payNum--;
      if(this.payNum<1){
        this.payNum = 1;
      }
      this.selectCity(this.thisSelectCity);
    },
    gsPayNumPlus(){
      this.payNum++;
      this.selectCity(this.thisSelectCity);
    }
  },
  mounted(){
    $('.gsThumb span').mouseover(function(){
      $('.gsThumb span').removeClass('active');
      $(this).addClass('active');
      $('.gsAlbum img').attr('src', $(this).find('img').attr('src'))
    })
    $('.gsSelectAr').click(function(){
      $('.selectCity').show();
    })
    $('.closeSC').click(function(){
      $('.selectCity').hide();
    })
  }
})
</script>
<?php get_footer(); ?>