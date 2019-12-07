<?php global $current_user; ?>
<div class="col-md-4">
  <div class="myIntegral">
    <div class="wt-container">
			<div class="mod-tit">
        <h3>当前积分</h3>
			</div>
      <div class="mod-con">
        <div class="curIntegral"><?php echo getUserIntegral($current_user->ID); ?></div>
        <div class="curIntegralText">当前可用积分，查看<a href="">积分明细</a></div>
      </div>
    </div>
  </div>
  <div class="integralRole">
  <div class="wt-container">
			<div class="mod-tit">
        <h3>积分规则</h3>
			</div>
      <div class="mod-con">
        <p>1、第一次签到获得1积分，每次签到增加1积分；</p>
        <p>2、连续签到7天及以上固定获取10积分，每日签到最高可获取10积分；</p>
        <p>3、签到累计不受自然月及假期的影响；</p>
        <p>4、若发现作弊行为，一经发现将清零账户所有积分；</p>
        <p>5、连续签到间断后，重新计算连续签到时间；</p>
        <p>6、积分永久有效；</p>
      </div>
  </div>
</div>