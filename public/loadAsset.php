<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $slug = mysqli_real_escape_string($link, $data->{"slug"});
  $assetID = alphaToDec($slug);
  $files = [];
  $success = false;
  $filecount = 0;
  $sql = "SELECT * FROM files WHERE id = $assetID AND private = 0";
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $success = true;
    $files[] = mysqli_fetch_assoc($res);
  } else {
    $success = false;
  }
  echo json_encode([$success, $files, $filecount, $slug, $assetID, $sql]);
?>
