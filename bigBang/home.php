<html>
<a href="/">Home</a></br>
<?php
global $HOME;
$HOME   = '/bigBang/';
header("Content-type:text/html;charset=GB2312");

handleRequest();



function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function showSeason($varr){
    global $HOME;
    $result = '';
    foreach( $varr as $value){
        if($value!='.' ){
            $s = '<a href="'.$HOME.'home.php?S='.$value.'">'. $value. '</a>&nbsp&nbsp';
            $result= $result.$s;
        }
    }
    echo($result.'</br>');
}
function shoeVid($vid,$S){
    global $HOME;
    $result = '';
    foreach( $vid as $value){
        if($value!='source2' ){
            $s = '<a href="'.$HOME.'home.php?S='.$S.'&&V='.$value.'">'. $value. '</a></br>';
            $result= $result.$s;
        }
    }
    echo($result);
}
function playVideo($vid,$s){
    global $HOME;
   $vidP = 'videos/'.$s.'/'.$vid;
    $vidP2 = 'videos/'.$s.'/source2/'.$vid;
   $subP = 'sus/'.$s.'/'.gotSub($vid,$s);
    $re = '<video id="vid" width="320" height="240" controls>
    
  <source  src="'.$vidP .'" type="video/mp4" >
  <source src="'.$vidP2 .'" type="video/mp4">
  <track label="English" kind="subtitles" srclang="en" src="'.$subP.'" default>
</video>';
   echo($re);
}

function gotSub($vid,$s){
    preg_match('/[Ss][0-9]+[Ee][0-9]+/', $vid, $matches, PREG_OFFSET_CAPTURE);
    $matched = $matches[0][0];
    $sub = 'sus/'.$s;
    try {
        $suba = scandir($sub);
    }catch (Exception $e){
        return null;
    }

    foreach( $suba as $value){
        if($value!='.' && $value!='..'){
            preg_match('/[Ss][0-9]+[Ee][0-9]+/', $value, $matches2, PREG_OFFSET_CAPTURE);
            $mached2 = $matches2[0][0];
            if(strcasecmp($mached2,$matched)==0){
                return $value;
            }
        }
    }


}

function cmp($a, $b)
{
    $na = (int)str_replace('S','',$a);
    $nb = (int)str_replace('S','',$b);
    if ($na == $nb) {
        return 0;
    }
    return ($na < $nb) ? -1 : 1;
}
function handleRequest( ){
    global $HOME;
    $sea = 'videos';
    $ses = array_slice(scandir($sea),2);

    usort($ses,"cmp");
    $sr = null;
    $vr = null;
    if(isset($_REQUEST['S'])){
        $sr = $_REQUEST['S'];
    }

    if(isset($_REQUEST['V'])){
        $vr = $_REQUEST['V'];
    }
    if($sr==null && $vr==null){
        showSeason($ses);
    }
    if($vr==null&&$sr!=null){

        $vid = array_slice(scandir('videos/'.$sr),2);

        showSeason($ses);
        shoeVid($vid,$sr);

    }
    if($vr!=null&&$sr!=null){
        playVideo($vr,$sr);

    }





}




?>
<script>
    var video = document.getElementById('vid');
    video.addEventListener('mousedown',toggleStop);
    window.onkeyup = keyUp;
    var defaultT = 5; //10s
    var accRate = 1.2 ;//acc rate
    var intervalThresh = 0.7*1000;
    var LR = 0; //-1 left 1 right
    
    var accCum = 1;
    var Stime = 0;
    
    function reset(){
        accCum = 1;
    }
    
    function getIntervalOk(){
        var tnow =new Date().getTime();
        diff = tnow - Stime;
        Stime = tnow;
        if(diff<0){
            diff = 86400000 + diff;
                    }
        if(diff<intervalThresh){
            return true;
        }
        return false;
        
    }
    function needAcc(isL){
        if(isL){
            if(LR==-1&&getIntervalOk()){
                return true;
            }
            LR = -1;
        }else{
             if(LR==1&&getIntervalOk()){
                return true;
            }
            LR = 1;
        }
        reset();
        return false;
    }
    function left(){
       
        if(needAcc(false))
        {
             accCum*=accRate;
        }
        
          video.currentTime -= accCum * defaultT;
    }
    function right(){
         if(needAcc(true))
        {
               accCum*=accRate;
          
        }
         
          video.currentTime += accCum * defaultT;
        
        
    }
    function toggleStop(){
        if(video.paused){
            video.play();
        }else{
            video.pause();
        }
    }
    
    function keyUp(k){
        switch (k.keyCode) {
                case 37:
                left();
                break;
                case 39:
                right();
                break;
               case 32:
                toggleStop();
                break;
               
               }
    }
    
</script>





</html>
























