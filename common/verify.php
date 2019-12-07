<?php

// 是否为空
function isEmpty($val){
  if (empty($val)) return true; //是否已设定
  if ($val=='') return true; //是否为空
  return false;
}

// 纯数字
function isNumber($val){
  if(preg_match("^[0-9]+$", $val))
  return true;
  return false;
}

// 手机号码
function isPhone($val){
//eg: xxx-xxxxxxxx-xxx | xxxx-xxxxxxx-xxx ...
if(preg_match("^((0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$",$val))
  return true;
  return false;
}

// 邮政编号
function isPostcode($val){
  if(preg_match("^[0-9]{4,6}$",$val))
    return true;
  return false;
}

// 邮箱
function isEmail($val, $domain=""){
  if(!$domain){
  if( preg_match("/^[a-z0-9-_.]+@[\da-z][\.\w-]+\.[a-z]{2,4}$/i", $val) ){
    return true;
  }else{
    return false;
  }
  }else{
  if( preg_match("/^[a-z0-9-_.]+@".$domain."$/i", $val) ){
    return true;
  }else{
    return false;
  }
  }
}

// 昵称
function isName($val){
  if( preg_match("/^[\x80-\xffa-zA-Z0-9]{3,60}$/", $val) ){
    return true;
  }
  return false;
}

// 字符串长度
function isStrLength($val, $min, $max){
  $val = trim($val);
  if(preg_match("^[a-zA-Z0-9]{".$min.",".$max."}$", $val))
    return true;
  return false;
}

// 数字长度
function isNumLength($val, $min, $max){
  $val = trim($val);
  if(preg_match("^[0-9]{".$min.",".$max."}$",$val))
    return true;
  return false;
}

// 纯英文
function isEnglish($theelement){
  if(preg_match("[\x80-\xff].",$theelement))
    Return false;
  Return true;
}

// 中文
function isChinese($sInBuf){
  if (preg_match("/^[\x7f-\xff]+$/", $sInBuf))
  //兼容gb2312,utf-8
    return true;
  else
    return false;
}

// 日期 0000-00-00
function isDate($sDate){
  if( preg_match("^[0-9]{4}\-[][0-9]{2}\-[0-9]{2}$",$sDate) )
    Return true;
  else
    Return false;
}

// 日期 0000-00-00 00:00:00
function isTime($sTime){
  if(preg_match("^[0-9]{4}\-[][0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$", $sTime))
    Return true;
  else
    Return false;
}

// 金额
function isMoney($val){
  if(preg_match("^[0-9]{1,}$", $val))
    return true;
  if(preg_match("^[0-9]{1,}\.[0-9]{1,2}$", $val))
    return true;
  return false;
}

//去除字符串空格
function strTrim($str){
  return preg_replace("/\s/", "", $str);
}

//验证身份证(中国)
function idCard($str){
  $str = strTrim($str);
  if(preg_match("/^([0-9]{15}|[0-9]{17}[0-9a-z])$/i",$str)){
    return true;
  }else{
    return false;
  }
}

/*
 * 执行验证函数
 */
function verify($obj){
  foreach($obj as $v){
    $funcName = $v[0];
    unset($v[0]);
    if($funcName(explode(',', $v))){
      return $v;
    }
  }
}