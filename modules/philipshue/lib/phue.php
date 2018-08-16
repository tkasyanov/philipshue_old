<?php
class PHue {


   // private $bridgeip;
    var $info = array();

    function __construct($ip = NULL, $username=NULL){
        if ($ip != NULL) $this->ip = $ip;
        $this->apiurl = "http://$ip/api/$username";
    }

    function loadInfo(){
        $json_info = $this->getInfo();
        $current_info = json_decode($json_info,true);
        return $current_info;
    }
    function loadLights(){
        $json_info = $this->getInfo();
        $current_info = json_decode($json_info,true);
        return $current_info["lights"];
    }
    function loadSensors(){
        $json_info = $this->getInfo();
        $current_info = json_decode($json_info,true);
        return $current_info["sensors"];
    }




    function loadConfig(){
        $json_info = $this->getInfo();
        $current_info = json_decode($json_info,true);
        return $current_info['config'];
    }


    function sendCmd($content_js,$method){
        $context = array('http'=>array(
            'method'=>$method,
            'header'=>'Content-type: application/x-www-form-urlencoded',
            'content'=>$content_js
        )
        );
        return @file("$this->apiurl",false,stream_context_create($context))[0];
    }
    function newUser(){
        return json_decode($this->sendCmd(json_encode (array('devicetype'=>'Phue')),"POST"))[0];
    }

    function getInfo(){
        return file_get_contents("$this->apiurl");
    }

}