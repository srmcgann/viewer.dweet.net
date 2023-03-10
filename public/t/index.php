<?
  require('../db.php');
  $fileID = mysqli_real_escape_string($link, alphaToDec($_GET['resource']));
  $file = []
  if(is_numeric($fileID)){
    $sql = 'SELECT * FROM files WHERE id = ' . $fileID;
    $res = mysqli_query($sql);
    if(mysqli_num_rows($res) == 1){
      $file = mysqli_fetch_assoc($res);
      if(+$file['private']){
        echo 'resource is private! :(';
        die();
      }
    }
  } else {
    echo 'error :(';
    die();
  }
  if(!sizeof($file)){
    echo '404 :(';
    die();
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <style>
      body, html{
        width: 100vw;
        height: 100vh;
        margin: 0;
        border: 0;
        background: #000;
        color: #fff;
        font-family: courier;
        overflow: hidden;
      }
      iframe{
        border: none;
        width: 100%;
        height: 100%;
        overflow: hidden;
        position: absolute;
        top: 0;
        left: 0;
      }
    </style>
  </head>
  <body>
    <iframe src="<?=$viewerURL$fileID?>"></iframe>
  </body>
</html>
