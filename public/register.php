<?
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $user = mysqli_real_escape_string($link, $data->{"user"}); 
  $password = mysqli_real_escape_string($link, $data->{"password"});
  $email = mysqli_real_escape_string($link, $data->{"email"});
  $error = "no (or unknown error)";
  $success = false;
  if($user && $password){
    $sql = "SELECT * FROM users WHERE LOWER(REPLACE(name, ' ', '')) = LOWER(REPLACE('$user', ' ', '')) OR LOWER(REPLACE(email, ' ', '')) = LOWER(REPLACE('$user', ' ', ''))";
    $res = mysqli_query($link, $sql);
    if(!mysqli_num_rows($res)){
      $passhash = password_hash($password, PASSWORD_DEFAULT);
      $sql = 'INSERT INTO users (name, currentLocation, avatar, admin, passhash, email) VALUES("'.$user.'", "/", "", 0, "'.$passhash.'", "'.$email.'")';
      mysqli_query($link, $sql);
      $sql1 = "SELECT * FROM users WHERE LOWER(REPLACE(name, ' ', '')) = LOWER(REPLACE('$user', ' ', '')) OR LOWER(REPLACE(email, ' ', '')) = LOWER(REPLACE('$user', ' ', ''))";
      $res = mysqli_query($link, $sql1);
      if(mysqli_num_rows($res)){
        $row = mysqli_fetch_assoc($res);
        $success = true;
        $user = [
          'name'     => $row['name'],
          'email'    => $row['email'],
          'passhash' => $row['passhash'],
          'id'       => $row['id'],
          'avatar'   => $row['avatar'],
          'admin'    => $row['admin']
        ];
      } else {
        $error = "error adding user... [check sql maybe :'( ]";
      }
    } else {
      $error = "user already exists!";
    }
  }else{
    $error = "user name or password not provided!";
  }
  echo json_encode([$success, $user, $error, $sql]);
?>
