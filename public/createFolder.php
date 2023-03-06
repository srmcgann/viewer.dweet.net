<?
  require('db.php');

  $data = json_decode(file_get_contents('php://input'));
  $user = mysqli_real_escape_string($link, $data->{"user"});
  $passhash = mysqli_real_escape_string($link, $data->{"passhash"});
  $folderName = mysqli_real_escape_string($link, $data->{"folderName"});
  $location = mysqli_real_escape_string($link, $data->{"currentLocation"});
  $success = false;
  if($user && $passhash){
    $sql = 'SELECT * FROM users WHERE (name LIKE "'.$user.'" OR email LIKE "'.$user.'") AND passhash = "'.$passhash.'"';
    $res = mysqli_query($link, $sql);
    if(!mysqli_num_rows($res)) die();
    $row = mysqli_fetch_assoc($res);
    $userID = $row['id'];

    $newFileName = ($hash = md5($passhash . $folderName . $location . $user));
    $newFilePath = $assetsDir . $newFileName;
    $collision = false;
    if(file_exists($newFilePath)){
      $sql = 'SELECT * FROM files WHERE folder = 1 AND name = "'.$folderName.'" AND location LIKE "'.$location.'"';
      $res = mysqli_query($link, $sql);
      for($i=0; $i<mysqli_num_rows($res); ++$i){
        $row = mysqli_fetch_assoc($res);
        $existing_userID = $row['userID'];
        $existing_location = $row['location'];
        if($existing_userID == $userID && $existing_location == $location) $collision = true;
      }
    }
    if(!$collision){
      $newFolder = [];
      chmod($newFilePath, 0755);
      $description = '';
      if(!$location) $location = '/';
      $sql = 'INSERT INTO files (name, type, userID, description, views, private, folder, hash, location) VALUES("'.$folderName.'", "folder", '.$userID.',"'.$description.'", 0, 1, 1, "'.$hash.'", "'.$location.'")';
      mysqli_query($link, $sql);
      $success = true;
      $sql = "SELECT * FROM files WHERE userID = $userID AND hash = \"$hash\"";
      $res2 = mysqli_query($link, $sql);
      if($filecount = mysqli_num_rows($res2)){
        $newFolder = mysqli_fetch_assoc($res2);
      }
    }
  }
  echo json_encode([$success, $collision, $newFolder]);
?>
