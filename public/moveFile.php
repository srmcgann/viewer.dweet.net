<?
  require('db.php');

  $data = json_decode(file_get_contents('php://input'));
  $user = mysqli_real_escape_string($link, $data->{"user"});
  $passhash = mysqli_real_escape_string($link, $data->{"passhash"});
  $src = mysqli_real_escape_string($link, $data->{"src"});
  $dest = mysqli_real_escape_string($link, $data->{"dest"});
  $success = false;
  if($user && $passhash){
    $sql = "SELECT * FROM users WHERE (LOWER(REPLACE(name, ' ', '')) = LOWER(REPLACE('$user', ' ', '')) OR LOWER(REPLACE(email, ' ', '')) = LOWER(REPLACE('$user', ' ', ''))) AND passhash = \"$passhash\"";
    $res = mysqli_query($link, $sql);
    if(!mysqli_num_rows($res)) die();
    $row = mysqli_fetch_assoc($res);
    $userID = $row['id'];

    $sql = 'SELECT * FROM files WHERE id = ' . $src;
    $res = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($res);
    if($row['type'] == 'folder'){
      $sql = 'SELECT * FROM files WHERE location LIKE "'.$row['location'].$row['name'].'%"';
      $res2 = mysqli_query($link, $sql);
      $sql_ = 'SELECT * FROM files WHERE id = ' . $dest;
      $res_ = mysqli_query($link, $sql_);
      $row_ = mysqli_fetch_assoc($res_);
      $newLocation = $row_['location'] . $row_['name']. '/' . $row['name'];
      for($i = 0; $i < mysqli_num_rows($res2); ++$i){
        $row2 = mysqli_fetch_assoc($res2);
        $tgtID = $row2['id'];
        $newPath = str_replace($row['location'].$row['name'], $newLocation, $row2['location']);
        $sql = 'UPDATE files SET location = "'.$newPath.'" WHERE id = ' . $tgtID;
        mysqli_query($link, $sql);
      }
    }


    $sql = 'SELECT * FROM files WHERE id = ' . $dest;
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      $newLocation = $row['location'] . $row['name'] . '/';
      if($row['type'] == 'folder'){
        $sql = 'UPDATE files SET location = "' . $newLocation . '" WHERE id = ' . $src;
        mysqli_query($link, $sql);
      }

      $success = true;
    }
  }
  echo json_encode([$success,$sql,$newLocation]);
?>
