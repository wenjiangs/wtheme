<?php
$genderArr = array('男','女','其他','保密');
//更新用户个人档案
if(isset($_POST['submit_profile'])){
	update_user_meta($current_user->ID, 'description', $_POST['description']);
	update_user_meta($current_user->ID, 'mobile_phone', $_POST['mobile_phone']);
	update_user_meta($current_user->ID, 'gender', $genderArr[$_POST['gender']]);
	update_user_meta($current_user->ID, 'location', $_POST['location'].' '.$_POST['location2']);
  $user_id = wp_update_user(array(
		'ID' => $current_user->ID,
		'user_url' => $_POST['user_url'],
		'display_name' => $_POST['nickname'],
		'user_email' => $_POST['user_email'],
	));
	if($user_id){
		header('location:/setting');
	}
}
get_header();
?>
<div class="container" id="app">
<div class="row">
<?php get_sidebar('user'); ?>
<div class="col-md-9">
<div class="wt-container">
<h1 class="wt-setting-tit">基本资料</h1>
<?php if(isset($err)){ ?>
	<div class="alert alert-danger setting-alert"><?php echo $err; ?></div>
<?php } ?>
<?php if(isset($suc)){ ?>
	<div class="alert alert-success setting-alert"><?php echo $suc; ?></div>
<?php } ?>
<form action="" class="profile_form" method="post">
<table class="form-table" width="100%">
  <tr>
	<th width="150">账号</th>
	<td><?php echo $current_user->user_login;?></td>
  </tr>
  <tr>
	<th>昵称</th>
	<td><input type="text" class="form-control" style="width:40%" name="nickname" value="<?php echo $current_user->data->display_name;?>"/></td>
  </tr>
  <tr>
	<th>性别</th>
	<td>
  <el-radio-group v-model="gender" change="changeGender">
    <el-radio :label="index" v-for="(item, index) in genderArr">{{item}}</el-radio>
  </el-radio-group>
  <input type="hidden" name="gender" v-model="gender"/>
  </td>
  </tr>
  <tr>
	<th>居住地</th>
	<td>
    <el-cascader
      :options="options"
      v-model="viewLocation"
      separator=" "
      @change="handleChange">
    </el-cascader>
    <input type="hidden" name="location" v-model="location"/>
    <textarea class="form-control" placeholder="详细地址"
    style="height:5em; margin-top:10px;" name="location2" v-model="location2"></textarea>
  </td>
  </tr>
  <tr>
	<th>个人网站</th>
	<td><input type="text" class="form-control" name="user_url" value="<?php echo $current_user->data->user_url;?>"/></td>
  </tr>
  <tr>
	<th>电子邮箱</th>
	<td>
	<input type="text" style="width:70%; margin-right:15px;" class="form-control pull-left" name="user_email" value="<?php echo $current_user->data->user_email;?>"/>
	<button class="btn btn-success" type="button">验证邮箱</button>
	</td>
  </tr>
  <tr>
	<th>个性主页</th>
	<td><a target="blank" href="<?php echo get_author_posts_url($current_user->ID);?>"><?php echo get_author_posts_url($current_user->ID);?></a><p><i class="fa fa-info-circle"></i> 暂时不支持修改！</p></td>
  </tr>
  <tr>
	<th>手机号码</th>
	<td>
  <p class="pull-right">仅用于账号管理用途</p>
  <input type="text" style="width:50%; margin-right:15px;" class="form-control pull-left" name="mobile_phone" value="<?php echo get_user_meta($current_user->ID, 'mobile_phone', true); ?>"/>
  <button class="btn btn-success" type="button">验证手机</button>
  </td>
  </tr>
  <tr>
	<th>简介</th>
	<td><textarea class="form-control" style="height:10em;" name="description"><?php echo get_user_meta($current_user->ID, 'description', true);?></textarea></td>
  </tr>
  <tr>
	<th>注册时间</th>
	<td style="color:#999"><?php echo $current_user->user_registered;?></td>
  </tr>
  <tr>
	<td></td>
	<td><button type="sbumit" native-type="submit"
  name="submit_profile" class="btn btn-primary submitBtn">保存资料</button></td>
	</tr>
</table>
</form>
</div>
</div>
</div>
</div>
<script>
var app = new Vue({
  el: '#app',
  data: {
    selectedOptions: [],
    options: options,
    genderArr: ['男','女','其他','保密'],
    gender: <?php
    $gender = get_user_meta($current_user->ID, 'gender', true);
    if($gender){
      echo array_search($gender, $genderArr);
    }else{
      echo 0;
    }
    ?>,
    location: '<?php
    $gender = get_user_meta($current_user->ID, 'location', true);
    if($gender){
      $gender = explode(' ', $gender);
      echo $gender[0].' '.$gender[1].' '.$gender[2];
      unset($gender[0],$gender[1],$gender[2]);
      $location2 = implode(' ', $gender);
    }
    ?>',
    location2: '<?php if($location2) echo $location2; ?>',
    viewLocation: [],
  },
  methods: {
    handleChange(res) {
      this.location = res.join(' ');
    },
    changeGender(res){
      this.gender = res;
    }
  },
  mounted(){
    this.viewLocation = this.location.split(' ');
    console.log(this.viewLocation);
  },
})
</script>
<?php get_footer();?>