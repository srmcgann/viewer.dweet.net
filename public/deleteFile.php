<?
  require('db.php');

  $data = json_decode(file_get_contents('php://input'));
  $user = mysqli_real_escape_string($link, $data->{"user"});
  $passhash = mysqli_real_escape_string($link, $data->{"passhash"});
  $fileID = mysqli_real_escape_string($link, $data->{"fileID"});
  $success = false;
  if($user && $passhash){
    $sql = "SELECT * FROM users WHERE (LOWER(REPLACE(name, ' ', '')) = LOWER(REPLACE('$user', ' ', '')) OR LOWER(REPLACE(email, ' ', '')) = LOWER(REPLACE('$user', ' ', ''))) AND passhash = \"$passhash\"";
    $res = mysqli_query($link, $sql);
    if(!mysqli_num_rows($res)) die();
    $row = mysqli_fetch_assoc($res);
    $userID = $row['id'];
    $sql = 'SELECT * FROM files WHERE id = ' . $fileID;
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      if($row['type'] == 'folder'){
        $sql = 'DELETE FROM files WHERE location like "'.$row['location'].$row['name'].'%"';
        mysqli_query($link, $sql);
      }
      $hash = $row['hash'];
      $sql = 'DELETE FROM files WHERE userID = '.$userID.' AND id = ' . $fileID;    
      mysqli_query($link, $sql);
      unlink($assetsDir.'/'.$hash);
      $success = true;
    }
  }
  echo json_encode([$success]);
?>
