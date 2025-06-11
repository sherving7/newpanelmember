<?php
class api{
        
public function balance($smart,$api,$api_url,$api_key){
    if($smart == 1){
	$url = $api_url."?key=".$api_key."&action=balance";
	$result = curl($url);
	if($result !== 'false'){
	$res = json_decode($result,1);
	if(!isset($res['error'])){
	return number_format($res['balance'],0,'','');
	}else{
	return 'false';
	}
	}else{
	return 'false';
	}
	}
}
//---------------------------------------//
public function status($smart,$api,$api_url,$api_key,$id){
    if($smart == 1){
	$url = $api_url."?key=".$api_key."&action=status&order=".$id;
	$result = curl($url);
	if($result !== 'false'){
	$res = json_decode($result,1);
	if(isset($res['order'])){
	return $res;
	}else{
	return ['status'=>'false'];
	}
    }else{
	return 'false';
	}
	}
}
//---------------------------------------//
public function add_order($smart,$api,$api_url,$api_key,$id,$link,$quantity){
    if($smart == 1){
	$url = $api_url."?key=".$api_key."&action=add&service=".$id."&link=".$link."&quantity=".$quantity;
	$result = curl($url);
	if($result !== 'false'){
	$res = json_decode($result,1);
	if($res['status'] == 'success'){
	return ['status'=>'OK','code'=>$res['order']];
	}else{
	return $res;
	}
    }else{
	return 'false';
	}
}
}
//---------------------------------------//
public function services($smart,$api,$api_url,$api_key){
    if($smart == 1){
	$url = $api_url."?key=".$api_key."&action=services";
	$result = curl($url);
	if($result !== 'false'){
	$res = json_decode($result,1);
	return $res;
    }else{
	return 'false';
	}
}
}
}
?>
