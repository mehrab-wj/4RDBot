<?php
class _4rd {
  public $api_url = "http://4rd.ir/api/v1";
  public $api_key = "1535583601:23dfbte6b22x9702858de682fxe2e3e7fc52bbf1";
  function request($datas=[],$api_key = "default") {
    if ($api_key != 'default') { $api_key = $api_key; }
    else { $api_key = $this->api_key; }
    $url = $this->api_url;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query(['token'=>$api_key] + $datas));
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
  }

}

 ?>
