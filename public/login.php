<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $user = mysqli_real_escape_string($link, $data->{"user"}); 
  $password = mysqli_real_escape_string($link, $data->{"password"});
  $location = mysqli_real_escape_string($link, $data->{"location"});
  if($user && $password){
    $sql = "SELECT * FROM users WHERE LOWER(REPLACE(name, ' ', '')) = LOWER(REPLACE('$user', ' ', '')) OR LOWER(REPLACE(email, ' ', '')) = LOWER(REPLACE('$user', ' ', ''))";
    $res = mysqli_query($link, $sql);
    $success=false;
    $error="no (or unknown) error";
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      $userID = $row['id'];
     $lp = explode('/', $location);
     if(sizeof($lp)){
       $ar = [];
       for($i=1;$i<sizeof($lp);++$i){
         if($lp[$i]) $ar[]=$lp[$i];
       }
       if(sizeof($ar)){
         $lp_ = str_replace('/', '', $ar[0]);
         if($userID == alphaToDec($lp_)){
           $location = '';
           for($i=1;$i<sizeof($ar);++$i){
             $location .= $ar[$i].'/';
           }
         }
       }
     }

      $passhash = $row['passhash'];
      if(password_verify($password, $passhash)){
        $success = true;
        $user = [
          'name'            => $row['name'],
          'currentLocation' => $row['currentLocation'],
          'email'           => $row['email'],
          'basicIcons'      => (0+$row['basicIcons'])?'true':'false',
          'snapToGrid'      => (0+$row['snapToGrid'])?'true':'false',
          'passhash'        => $row['passhash'],
          'id'              => $row['id'],
          'avatar'          => $row['avatar'],
          'admin'           => $row['admin']
        ];
      $sql = 'UPDATE users SET currentLocation = "'.$location.'" WHERE id = ' . $userID;
      mysqli_query($link, $sql);
      } else {
        $error = "incorrect password!";
      }
    } else {
      $error = "user not found!";
    }
  }else{
    $error = "user name or password not provided!";
  }
  echo json_encode([$success, $user, $error]);
?>
