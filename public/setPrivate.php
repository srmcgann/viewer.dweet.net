<?
  require('db.php');

  $data = json_decode(file_get_contents('php://input'));
  $user = mysqli_real_escape_string($link, $data->{"user"});
  $passhash = mysqli_real_escape_string($link, $data->{"passhash"});
  $fileID = mysqli_real_escape_string($link, $data->{"fileID"});
  $private = mysqli_real_escape_string($link, $data->{"private"});
  $success = false;
  if($user && $passhash){
    $sql = "SELECT * FROM users WHERE (LOWER(REPLACE(name, ' ', '')) = LOWER(REPLACE('$user', ' ', '')) OR LOWER(REPLACE(email, ' ', '')) = LOWER(REPLACE('$user', ' ', ''))) AND passhash = \"$passhash\"";
    $res = mysqli_query($link, $sql);
    if(!mysqli_num_rows($res)) die();
    $row = mysqli_fetch_assoc($res);
    $userID = $row['id'];
    $val = $private == '0' ? 0 : 1;
    $sql = 'UPDATE files SET private = "'.$val.'" WHERE id = ' . $fileID;
    mysqli_query($link, $sql);
    $success = true;
  }
  echo json_encode([$success, $private, $val]);
?>
