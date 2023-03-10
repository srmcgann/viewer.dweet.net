<?
  $db_user="user";
  $db_pass=explode("\n", file_get_contents('/home/cantelope/plorgpw'))[0];
  $db_host="localhost";
  $db="pshare";

  $baseAssetsURL = 'https://assets.dweet.net/misc';
  $viewerURL = 'https://viewer.dweet.net/';
  $assetsDir = '/var/www/html/whitehot_assets/misc';
  
  $link = mysqli_connect($db_host, $db_user, $db_pass, $db);

  function decToAlpha($val){
    $alphabet="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $ret="";
    while($val){
      $r=floor($val/62);
      $frac=$val/62-$r;
      $ind=(int)round($frac*62);
      $ret=$alphabet[$ind].$ret;
      $val=$r;
    }
    return $ret==""?"0":$ret;
  }

  function alphaToDec($val){
    $pow=0;
    $res=0;
    while($val!=""){
      $cur=$val[strlen($val)-1];
      $val=substr($val,0,strlen($val)-1);
      $mul=ord($cur)<58?$cur:ord($cur)-(ord($cur)>96?87:29);
      $res+=$mul*pow(62,$pow);
      $pow++;
    }
    return $res;
  }

  function getMimeTypeFromFileContent($content){
    return (new finfo(FILEINFO_MIME_TYPE))->buffer($content);
  }

  $meta = [];
  $sql = 'SELECT * FROM files WHERE id = ' . alphaToDec(str_replace('/', '', $_GET['asset']));
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    if($row['type'] == 'folder'){
      $meta['description'] = 'shared directory';
      $image = 'https://jsbot.cantelope.org/uploads/2jP7OJ.png';
    } else {
      if(strpos($row['type'], 'image') !== false){
        $meta['description'] = 'shared image';
        $image = "$baseAssetsURL/{$row['hash']}";
      } else {
        $meta['description'] = 'shared file';
        $image = 'https://jsbot.cantelope.org/uploads/2bceZU.png';
      }
    }
    if($row['type'] == 'generative'){
      $image = 'https://jsbot.cantelope.org/uploads/1ALBH1.png';
      $meta['title'] = str_replace('.zip', '', $row['name']) . " (generative)";
    } else {
      $meta['title'] = $row['name'];
    }
    $meta['image'] = $image;
    $meta['url'] = 'https://viewer.dweet.net/' . $_GET['asset'];
    $meta['twitter:card'] = $image;
  } else {
    $meta['title'] = 'pshare viewer';
    $meta['description'] = 'content tool';
    $meta['image'] = '';
    $meta['url'] = 'https://viewer.dweet.net';
    $meta['twitter:card'] = '';
  }
?>
