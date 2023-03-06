<?
  require('../db.php');
  $data = json_decode(file_get_contents('php://input'));
  $url = mysqli_real_escape_string($link, $data->{"url"});
  //$url = str_replace(":/", "://", $_GET['url']);
  //stream_set_blocking($src, true);
  $file = explode('/', $url);
  $file = $file[sizeof($file)-1];
  $type = mime_content_type("$assetsDir/$file");
  if($type != 'directory'){
    $apacheMap = file_get_contents('http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types');
    $map = explode("\n", $apacheMap);
    $out = [];
    $found = false;
    for($i = 0; $i<sizeof($map); ++$i){
      $els = explode("\t", $map[$i]);
      $ar = [];
      forEach($els as $el){
        if($el){
          array_push($ar, $el);
        }
      }
      if(sizeof($ar) == 2){
        $els2 = explode(' ', $ar[1]);
        forEach($els2 as $el2){
          if(!$found && $ar[0] == $type){
            $suffix = $el2;
            $found = true;
          }
        }
      }
    }
    if($found){
      header("Content-type: $type");
      echo file_get_contents("$assetsDir/$file");
    }
  }
?>
