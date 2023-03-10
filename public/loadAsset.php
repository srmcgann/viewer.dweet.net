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
    $row = mysqli_fetch_assoc($res);
    if($row['type'] == 'folder'){
      $location = $row['location'] . $row['name'] . '/';
      $sql = 'SELECT * FROM files WHERE location = "'.$location.'" AND userID = ' . $row['userID'] . ' AND private = 0 AND type <> "folder"';
      $res2 = mysqli_query($link, $sql);
      for($i=0; $i<mysqli_num_rows($res2); ++$i){
        $file = mysqli_fetch_assoc($res2);
        $files[] = $file;
      }
    } else {
      $files[] = $row;
    }
    $userID = $files[0]['userID'];
    $sql = 'SELECT name FROM users WHERE id = ' . $userID;
    $res = mysqli_query($link, $sql);
    $owner = mysqli_fetch_assoc($res)['name'];
  } else {
    $success = false;
  }
  echo json_encode([$success, $files, $owner, $filecount, $slug, $assetID, $sql]);
?>
