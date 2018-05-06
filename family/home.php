<html>
<a href="/">Home</a></br>
<?php
global $HOME;

$HOME   = '/family/';


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

<?php
$useragent=$_SERVER['HTTP_USER_AGENT'];

if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
?>
<script>


</script>

<?php
}
?>



</html>