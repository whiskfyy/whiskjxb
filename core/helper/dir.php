<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
function dir_remove($dir) {
   if(substr($path, -1, 1) != "/") {
       $path .= "/";
   }
   $normal_files = glob($path . "*");
   $hidden_files = glob($path . "\.?*");
   $all_files = array_merge($normal_files, $hidden_files);

   foreach ($all_files as $file) {
       if(preg_match("/(\.|\.\.)$/", $file)) {
           continue;
       }
       if(is_file($file) === TRUE) {
           @unlink($file);
       } elseif (is_dir($file) === TRUE) {
           dir_remove($file);
       }
   }
   if(is_dir($path) === TRUE) {
       rmdir($path);
   }
}
?>