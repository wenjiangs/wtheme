<?php
$alreadySignDay = get_user_meta($current_user->ID, 'alreadySignDay', true);

// 昨天是否签到
if(!is_yesUserSign($current_user->ID)){
  // 今天是否签到
  if(!is_userSign($current_user->ID)){
    $alreadySignDay = 0;
  }else{
    $alreadySignDay = 1;
  }
}

get_header();
?>

<div class="container" id="app">
  <div class="row">
    <div class="col-md-8">
      <div class="tickImage"><img src="wp-content/themes/wtheme/images/tick.jpg"/></div>
      <div class="tickBox">
        <div class="tickHead">
          <h3>签到领积分</h3>
          <p>断签后从第1天重新计算</p>
        </div>
        <div class="tickWrap">
          <?php for($i=1; $i<=6; $i++){ ?>
          <dl class="tickItem" :class="{active:alreadySignDay>=<?php echo $i; ?>}">
            <dt>第 <?php echo $i; ?> 天</dt><dd>+ <?php echo $i; ?></dd>
          </dl>
          <?php } ?>
          <dl class="tickItem" :class="{active:alreadySignDay>6}">
            <dt>7天及以上</dt><dd>+ 10</dd>
          </dl>
        </div>
        <div class="tickBtn">
          <button class="btn btn-success btn-lg" :disabled="isSign==1" @click="submitSign" v-html="btnText"></button>
        </div>
      </div>
      <div class="tickBox">
        <div class="tickHead">
          <h3>积分兑好礼</h3>
          <p>积分大兑换好礼任你选</p>
        </div>
      </div>
    </div>
    <?php get_sidebar('tick'); ?>
  </div>
</div>
<script>
var app = new Vue({
  el: '#app',
  data: {
    user_id: <?php echo $current_user->ID; ?>,
    alreadySignDay: <?php echo $alreadySignDay?$alreadySignDay:0; ?>,
    btnText: '<?php echo is_userSign($current_user->ID)?'已签到':'今日签到'; ?>',
    isSubmiting: false,
    isSign: <?php echo is_userSign($current_user->ID); ?>,
  },
  methods: {
    submitSign(){
      if(this.isSubmiting) return;
      this.btnText = '<i class="el-icon-loading"></i> 签到中';
      this.isSubmiting = true;
      $.ajax({
        method: 'post',
        url: '/wjson/',
        data:{
          model: 'userSign',
          action: {
            user_id: this.user_id
          }
        },
        success:(res)=>{
          this.isSubmiting = false;
          if(res.success){
            this.btnText = '已签到';
            this.alreadySignDay++;
            this.isSign = true;
          }else{
            this.btnText = '今日签到';
            this.$confirm(res.message, '', {
              confirmButtonText: '确定',
              showCancelButton: false,
              center: true
            })
          }
        }
      })
    }
  },
  mounted(){
    
  }
})
</script>
<?php get_footer(); ?>