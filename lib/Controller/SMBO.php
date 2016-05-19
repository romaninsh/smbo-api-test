<?php
class Controller_SMBO extends AbstractController {
    function call($ctl,$command,$args=array()){
        $ch = curl_init();
        $url=$this->api->getConfig('api_url', $url='https://sortmybooksonline.com/api/json');
        $url.='/'.$ctl;
        $url.='/'.$command;

        if(!isset($args['hash']))$args['hash']=$this->api->recall('hash');
        if(!isset($args['system_id']))$args['system_id']=$this->api->recall('system_id');

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, count($args));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);


        $res=curl_exec($ch);
        curl_close($ch);
        if(is_null(json_decode($res))){
            $res='URL: '.$url.'<br/>'.print_r($args,true).'<br/><hr/>'.$res;

        }else{
            //if($args['system_id'])-$this->api->memorize('system_id',$args['system-id']);
        }
        return $res;
    }
    function get($ctl){
        $data=json_decode($res=$this->call($ctl,'list'),true);
        $data = $data['response'];
        if(!$data){
            $this->owner->add('View_Error')->set($res);
            return array('Error');
        }
        $ass=array();
        foreach($data as $row){
            $ass[$row['id']]=@$row['name']?:$row['legal_name'];
        }
        return $ass;
    }
}
