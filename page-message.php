<?php
global $current_user;
get_currentuserinfo();
$chat = get_chat_list($current_user->ID);

$acceptArray = array(
  'user_id' => 0,
);
if(isset($_GET['user']) && is_numeric($_GET['user'])){
  $accept = get_user_by('id', $_GET['user']);
  if($accept){
    $acceptArray = array(
      'title' => '',
      'content' => '',
      'type' => 'private',
      'source' => '',
      'status' => 2,
      'noRead' => 0,
      'user_id' => $_GET['user'],
      'display_name' => $accept->data->display_name,
      'user_avatar' => preg_replace( '/<img(.+?)src=[\'"](.+?)[\'"](.+?)>/im', "$2", get_avatar($_GET['user']) ),
      'send_time_stamp' => '',
      'send_date' => '',
      'send_time' => '',
    );
  }
}

get_header();
?>

<div class="container" id="app">
  <div class="wt-container" style="overflow:visible">
    <div class="msg">
      <div class="msgLeft">
        <div class="msgSearch">
          <i class="fa fa-search"></i>
          <input type="text" class="form-control" placeholder="搜索消息记录"/>
        </div>
        <div class="msgList">
          <div class="msgListItem"
            v-for="(item, index) in chat_list"
            :key="index"
            @click="selectUser(item)"
          >
            <div class="mliAvatar">
              <div class="mliaBadge" v-if="item.noRead>0">{{item.noRead}}</div>
              <img :src="item.user_avatar">
            </div>
            <div class="mliTxt">
              <span>{{item.send_time}}</span>
              <h3>{{item.display_name}}</h3>
              <p>{{item.content}}</p>
            </div>
          </div>
        </div>
      </div>
      <div class="msgRight" v-if="accept.user_id>0">
        <div class="mrTit">{{accept.display_name}}</div>
        <div class="mrCon" id="mrCon">
          <div v-for="(item, index) in msg_list" :key="index">
          <div class="mrItem" v-if="item.user_id>0" :class="{mrItemRight:item.user_id==user_id}">
            <div class="mrAvatar">
              <img :src="item.user_avatar">
            </div>
            <div class="mrText">{{item.content}}</div>
          </div>
          <div class="mrTime" v-if="item.user_id==0"><span>{{item.time}}</span></div>
          </div>
        </div>
        <div class="mrInput">
          <div class="mriMain">
            <textarea v-model="msgContent" class="form-control"></textarea>
          </div>
          <div class="mriBtn">
            <button type="button" class="btn btn-default">关闭</button>
            <button type="button" @click="sendMessage" class="btn btn-primary">发送</button>
          </div>
        </div>
      </div>
      <div class="msgRight" v-if="accept.user_id==0">
        <div class="msgEmpty">
          <i class="fa fa-comment-o"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var app = new Vue({
  el: '#app',
  data: {
    accept: JSON.parse('<?php echo json_encode($acceptArray); ?>'),
    user_id: <?php echo $current_user->ID; ?>,
    msg_list: [],
    chat_list: [],
    msgContent: '',
  },
  methods: {
    wjPost(model, action, cb){
      $.ajax({
        method: 'post',
        url: '/wjson/',
        data:{
          model: model,
          action: JSON.stringify(action)
        },
        success: (res)=>{
          cb(res);
        }
      })
    },
    getChatList(){
      this.wjPost('getChatList', {user_id: this.user_id}, (res)=>{
        this.chat_list = res.data;
        <?php if($accept){ ?>
        console.log(this.accept);
        let isFound = false;
        this.chat_list.map((item, index)=>{
          if(this.accept.user_id==item.user_id){
            isFound = true;
            this.accept = item;
            this.getMessages();
          }
        })
        if(!isFound){
          this.chat_list.push(this.accept);
        }
        <?php } ?>
      })
    },
    getMessages(){
      this.wjPost('getMessages', {
        user_id: this.user_id,
        accept_id: this.accept.user_id,
      }, (res)=>{
        this.msg_list = res.data;
        this.scrollBottom();
      })
    },
    selectUser(e){
      if(!(this.accept.user_id==e.user_id)){
        this.accept = e;
        this.getMessages();
      }
    },
    sendMessage(){
      if(this.msgContent=='') return;
      this.wjPost('sendMessage', {
        user_id: this.user_id,
        send_id: this.user_id,
        accept_id: this.accept.user_id,
        title: '',
        content: this.msgContent,
        source: 'web',
      }, (res)=>{
        if(res.success){
          this.msgContent = '';
          this.msg_list.push(res.data);
          this.scrollBottom();
        }
      })
    },
    scrollBottom(){
      this.$nextTick(()=>{
        var ele = document.getElementById('mrCon');
        ele.scrollTop = ele.scrollHeight;
      })
    },
  },
  mounted(){
    this.getChatList();
  }
})
</script>

<?php get_footer(); ?>