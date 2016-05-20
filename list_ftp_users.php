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
   'owner',
   array(
       'short_name'  => '-o',
       'long_name'   => '--owner',
       'description' => 'system user name',
       'action'      => 'StoreString',
       'help_name'   => 'system_user_name'
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
     if (($handle = fopen($pure_pwdfile,'r')) === FALSE) {
       throw new \Exception ("Can't open pure-ftpd passwords file");
    }
   } catch (Exception $ex) {
       echo 'Error: ',  $ex->getMessage(), "\n";
       exit(1);
   }
   $json_arr=array();
   while (($data = fgetcsv($handle, 0, ":")) !== FALSE) {
      if ($data[2] == $uid){
        $json_arr[] = $data[0];
      }
    }
    echo json_encode($json_arr) . "\n";
    exit(0);
}
catch (Exception $exc) {
   $parser->displayError($exc->getMessage());
}