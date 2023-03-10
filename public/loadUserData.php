<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $user = mysqli_real_escape_string($link, $data->{"user"});
  $passhash = mysqli_real_escape_string($link, $data->{"passhash"});
  $files = [];
  $success = false;
  $filecount = 0;
  if($user && $passhash){
    $sql = "SELECT * FROM users WHERE passhash = \"$passhash\"";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $success = true;
      $row = mysqli_fetch_assoc($res);
      $userID = $row['id'];
      $currentLocation = $row['currentLocation'];
      $sql = "SELECT * FROM files WHERE userID = $userID AND location LIKE \"$currentLocation\"";
      $res2 = mysqli_query($link, $sql);
      if($filecount = mysqli_num_rows($res2)){
        for($i=0; $i<$filecount; ++$i){
          $files[] = mysqli_fetch_assoc($res2);
        }
      } else {
        $error = "user has no files!";
      }
    } else {
      $error = "user not found!";
    }
  } else {
    $error = "username or passhash not provided!";
  }
  echo json_encode([$success, $files, $filecount, $error, $currentLocation]);
?>
