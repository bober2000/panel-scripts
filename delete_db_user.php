<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require 'config.php';

$parser = new PEAR2\Console\CommandLine(array(
    'description' => 'Deletes MySQL user',
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
       'description' => 'database user name',
       'action'      => 'StoreString',
       'help_name'   => 'database_user_name'
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

   mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
   try {
        $conn = new mysqli($mysql_settings["client"]["host"], $mysql_settings["client"]["user"], $mysql_settings["client"]["password"]);
   } catch (Exception $ex) {
        echo "Service unavailable: ".$ex->getMessage()."\n";;
        exit(1);
   }
   $sql = "DROP USER `".$result->options["name"]."`@`".$mysql_settings["client"]["host"];
   try {
      if ($conn->query($sql) === TRUE) {
        echo "Successfully deleted database user: ".$result->options["name"]."`@`".$mysql_settings["client"]["host"]."\n";
        $conn->close();
        exit(0);
      }
      else{
        throw new Exception($conn->error);
      }
   } catch (Exception $ex) {
        echo "Error: ".$ex->getMessage()."\n";;
        $conn->close();
        exit(1);
   }
$conn->close();
}
catch (Exception $exc) {
   $parser->displayError($exc->getMessage());
}