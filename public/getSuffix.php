<?
  require('db.php');
  $apacheMap = file_get_contents('http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types');
  //echo $apacheMap . "\n";
  $map = explode("\n", $apacheMap);
  $out = [];
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
        $out[] = [$el2, $ar[0]];
      }
    }
  }
  echo json_encode($out) . "\n";
  //echo json_encode([$success, $suffix, $type]);
?>

