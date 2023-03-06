<?
  require('db.php');

  $data = json_decode(file_get_contents('php://input'));
  $user = mysqli_real_escape_string($link, $data->{"user"});
  $passhash = mysqli_real_escape_string($link, $data->{"passhash"});
  $fileID = mysqli_real_escape_string($link, $data->{"fileID"});
  $newName = mysqli_real_escape_string($link, $data->{"newName"});
  $success = false;
  if($user && $passhash){
    $sql = 'SELECT * FROM users WHERE (name LIKE "'.$user.'" OR email LIKE "'.$user.'") AND passhash = "'.$passhash.'"';
    $res = mysqli_query($link, $sql);
    if(!mysqli_num_rows($res)) die();
    $row = mysqli_fetch_assoc($res);
    $userID = $row['id'];
    $sql = 'SELECT * FROM files WHERE id = ' . $fileID;
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      $hash = $row['hash'];
      $sql = 'UPDATE files SET name = "'.$newName.'" WHERE userID = '.$userID.' AND id = ' . $fileID;    
      mysqli_query($link, $sql);
      if($row['type'] == 'folder'){
        $sql = 'SELECT * FROM files WHERE location LIKE "'.$row['location'].$row['name'].'%"';
        $res2 = mysqli_query($link, $sql);
        for($i = 0; $i < mysqli_num_rows($res2); ++$i){
          $row2 = mysqli_fetch_assoc($res2);
          $tgtID = $row2['id'];
          $newPath = str_replace($row['location'].$row['name'], $row['location'].$newName, $row2['location']);
          $sql = 'UPDATE files SET location = "'.$newPath.'" WHERE id = ' . $tgtID;
          mysqli_query($link, $sql);
        }
      }
      $success = true;
    }
  }
  echo json_encode([$success]);
?>
