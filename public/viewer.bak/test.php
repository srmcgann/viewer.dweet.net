<?
  require('../db.php');
  $content = file_get_contents('/var/www/html/whitehot_assets/misc/bfe7d688ad6a313ccf5583af7f21f2d7');
  echo getMimeTypeFromFileContent($content) . "\n";
?>
