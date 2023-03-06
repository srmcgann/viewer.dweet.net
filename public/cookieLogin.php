<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $user = mysqli_real_escape_string($link, $data->{"user"}); 
  $passhash = mysqli_real_escape_string($link, $data->{"passhash"});
  $error = "no (or unknown) error";
  $location = mysqli_real_escape_string($link, $data->{"location"});
  if($user && $passhash){
  $sql = "SELECT * FROM users WHERE (LOWER(REPLACE(name, ' ', '')) = LOWER(REPLACE('$user', ' ', '')) OR LOWER(REPLACE(email, ' ', '')) = LOWER(REPLACE('$user', ' ', ''))) AND passhash = \"$passhash\"";
   $res = mysqli_query($link, $sql);
   $success=false;
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
         $location = '/';
         $lp_ = str_replace('/', '', $ar[0]);
         if($userID == alphaToDec($lp_)){
           for($i=1;$i<sizeof($ar);++$i){
             $location .= $ar[$i].'/';
           }
         }
       } 
     }
     $success = true;
     $user = [
       'name'            => $row['name'],
       'email'           => $row['email'],
       'passhash'        => $row['passhash'],
       'basicIcons'      => (0+$row['basicIcons'])?'true':'false',
       'snapToGrid'      => (0+$row['snapToGrid'])?'true':'false',
       'currentLocation' => $location,
       'id'              => $row['id'],
       'avatar'          => $row['avatar'],
       'admin'           => $row['admin']
     ];
     $sql = 'UPDATE users SET currentLocation = "'.$location.'" WHERE id = ' . $userID;
     mysqli_query($link, $sql);
   } else {
     $error = "user not found!";
   }
  }else{
    $error = "user name or passhash not provided!";
  }
  echo json_encode([$success, $user, $error, $location, $ar]);
?>
