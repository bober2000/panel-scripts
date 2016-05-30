<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require 'config.php';

$parser = new PEAR2\Console\CommandLine(array(
    'description' => 'Grant privileges to MySQL user',
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
$parser->addOption(
   'db_name',
   array(
       'short_name'  => '-d',
       'long_name'   => '--dbname',
       'description' => 'database name',
       'action'      => 'StoreString',
       'help_name'   => 'database_name'
  )
);
$parser->addOption(
   'grant',
   array(
       'short_name'  => '-g',
       'long_name'   => '--grant',
       'description' => 'grant type',
       'action'      => 'StoreString',
       'help_name'   => 'grant_type'
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
   if (empty($result->options["name"]) || empty($result->options["db_name"])){
      echo "Check parameters, please\n";
      $parser->displayUsage();
   }
   mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
   try {
        $conn = new mysqli($mysql_settings["client"]["host"], $mysql_settings["client"]["user"], $mysql_settings["client"]["password"]);
   } catch (Exception $ex) {
        echo "Service unavailable: ".$ex->getMessage()."\n";;
        exit(1);
   }
   $sql = "GRANT ".$result->options["grant"]." ON ".$result->options["db_name"].".* TO `".$result->options["name"]."`@`".$mysql_settings["client"]["host"] ."`";
   try {
      if ($conn->query($sql) === TRUE) {
        echo "Successfully granted " . $result->options["grant"] ." grants on ".$result->options["db_name"]." db, to database user: `".$result->options["name"]."`@`".$mysql_settings["client"]["host"]."`\n";
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
