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

try {
   $result = $parser->parse();
   if ($result->options["interactive"]){
      //TODO Запуск в интерактивном режиме
   }
   if (empty(array_filter($result->options))){
      $parser->displayUsage();
   }
   try {
     $cmd = escapeshellcmd('pure-pw userdel ' . $result->options["name"] . ' -m ');
     exec($cmd . " 2>&1",$aResult, $return_val);
     if ($return_val<>0)
        throw new \Exception ("Can't run pure-pw userdel command");
     echo "Successfully deleted ftp user: " . $result->options["name"] . "\n";
     exit(0);
   } catch (Exception $ex) {
        echo 'Error: ',  $ex->getMessage(), "\n";
        exit(1);
   }
}
catch (Exception $exc) {
   $parser->displayError($exc->getMessage());
}