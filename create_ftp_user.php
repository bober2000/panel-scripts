<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require 'config.php';

$parser = new PEAR2\Console\CommandLine(array(
    'description' => 'Creates new FTP user',
    'version'     => '0.0.1', // the version of your program
));
$parser->addOption(
   'interactive',
   array(
      'short_name'  => '-i',
      'long_name'   => '--interactive',
      'description' => 'Run interactively',
      'action'      => 'StoreTrue'
  )
);
$parser->addOption(
   'name',
   array(
       'short_name'  => '-n',
       'long_name'   => '--name',
       'description' => 'ftp user name',
       'action'      => 'StoreString',
       'help_name'   => 'ftp_user_name'
  )
);
$parser->addOption(
   'password',
   array(
       'short_name'  => '-p',
       'long_name'   => '--password',
       'description' => 'database user name password',
       'action'      => 'Password',
       'help_name'   => 'ftp_user_name_password'
  )
);
$parser->addOption(
   'owner',
   array(
       'short_name'  => '-o',
       'long_name'   => '--owner',
       'description' => 'system user name',
       'action'      => 'StoreString',
       'help_name'   => 'system_user_name'
  )
);
$parser->addOption(
   'directory',
   array(
       'short_name'  => '-d',
       'long_name'   => '--directory',
       'description' => 'ftp user home directory',
       'action'      => 'StoreString',
       'help_name'   => 'dir_name'
  )
);

try {
   $result = $parser->parse();
   if ($result->options["interactive"]){
      //TODO Запуск в интерактивном режиме
   }
   if (empty(array_filter($result->options))){
      $parser->displayUsage();
   }
   if ($result->options["password"]){
      $user_password = $result->options["password"];
   } else {
     $user_password = random_str(8);
   }
   try {
     $cmd = escapeshellcmd('id -u ' . $result->options["owner"]);
     $uid = exec($cmd . " 2>&1",$aResult, $return_val);
     if ($return_val<>0)
        throw new \Exception ("Can't get user uid");
   } catch (Exception $ex) {
        echo 'Error: ',  $ex->getMessage(), "\n";
        exit(1);
   }
   try {
     $cmd = escapeshellcmd('id -g ' . $result->options["owner"]);
     $gid = exec($cmd . " 2>&1",$aResult, $return_val);
     if ($return_val<>0)
        throw new \Exception ("Can't get user gid");
   } catch (Exception $ex) {
        echo 'Error: ',  $ex->getMessage(), "\n";
        exit(1);
   }
   $temp = tmpfile();
   $tmp_pwd = $user_password . "\n" . $user_password . "\n";
   $metaDatas = stream_get_meta_data($temp);
   $tmpFilename = $metaDatas['uri'];
   try {
     if (fwrite($temp,$tmp_pwd ) === FALSE) {
        throw new \Exception ("Can't write temporary file");
    }
   } catch (Exception $ex) {
        echo 'Error: ',  $ex->getMessage(), "\n";
        fclose($temp);
        unset($tmp_pwd);
        unset($user_password);
        exit(1);
   }
   try {
     $cmd = escapeshellcmd('pure-pw useradd ' . $result->options["name"] .' -u ' . $aResult[0] . ' -g ' . $aResult[1] . ' -d ' . $result->options["directory"] . ' -m ');
     $cmd = $cmd . "< " . $tmpFilename;
     unset($aResult);
     exec($cmd . " 2>&1",$aResult, $return_val);
     if ($return_val<>0)
        throw new \Exception ("Can't run pure-pw useradd command");
     echo "Successfully created ftp user: " . $result->options["name"] . " with password " . $user_password . "\n";
     exit(0);
   } catch (Exception $ex) {
        echo 'Error: ',  $ex->getMessage(), "\n";
        fclose($temp);
        unset($tmp_pwd);
        unset($user_password);
        exit(1);
   }
}
catch (Exception $exc) {
   $parser->displayError($exc->getMessage());
}