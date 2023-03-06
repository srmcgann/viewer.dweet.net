<?
  require('db.php');

  $user = mysqli_real_escape_string($link, $_POST['user']);
  $passhash = mysqli_real_escape_string($link, $_POST['passhash']);

  //$user = 'cantelope';
  //$passhash = '$2y$10$Rmne0vHey.ywn7QU/WqUGun9SXUZR6RHjssez4.dvJdAF2ZsB6SA2';

  if($user && $passhash){
    $sql = "SELECT * FROM users WHERE (LOWER(REPLACE(name, ' ', '')) = LOWER(REPLACE('$user', ' ', '')) OR LOWER(REPLACE(email, ' ', '')) = LOWER(REPLACE('$user', ' ', ''))) AND passhash = \"$passhash\"";
    $res = mysqli_query($link, $sql);
    if(!mysqli_num_rows($res)) die();
  } else {
    die();
  }

putenv('TMPDIR=/tmp');
$tmpFilePath = $_FILES['file']['tmp_name'];
$success = false;
if ($tmpFilePath != ""){
  $type = mime_content_type($tmpFilePath);
  $filename = strtoupper($_FILES['file']['name']);
  $suffix = '.' . substr($filename, strlen($filename)-3);
  if(1){//$suffix == '.MP3' || $suffix == '.WAV' || $suffix == '.OGG'){
    $newFileName = ($hash = md5($_FILES['file']['name'] . hash_file('md5', $tmpFilePath)));// . $suffix;
    $newFilePath = "$assetsDir/$newFileName";
    $location = mysqli_real_escape_string($link, $_POST['location']);
    $userID = mysqli_real_escape_string($link, $_POST['userID']);
    $collision = false;
    if(file_exists($newFilePath)){
      $sql = 'SELECT * FROM files WHERE hash = "'.$hash.'"';
      $res = mysqli_query($link, $sql);
      for($i=0; $i<mysqli_num_rows($res); ++$i){
        $row = mysqli_fetch_assoc($res);
        $existing_userID = $row['userID'];
        $existing_location = $row['location'];
        if($existing_userID == $userID && $existing_location == $location) $collision = true;
      }
    }
    if(!$collision){
      rename($tmpFilePath, $newFilePath);
      chmod($newFilePath, 0755);
      $name = mysqli_real_escape_string($link, $_POST['name']);
      $description = mysqli_real_escape_string($link, $_POST['description']);
      if(!$location) $location = '/';
      $sql = 'INSERT INTO files (name, type, userID, description, views, private, folder, hash, location) VALUES("'.$name.'", "'.$type.'", '.$userID.',"'.$description.'", 0, 1, 0, "'.$hash.'", "'.$location.'")';
      mysqli_query($link, $sql);
      $success = true;
    }
  }
  echo json_encode([$success, $collision, $tmpFilePath, $newFilePath]);
}
?>
