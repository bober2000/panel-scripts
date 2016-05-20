<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require 'config.php';

$parser = new PEAR2\Console\CommandLine(array(
    'description' => 'List users MySQL databases',
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
       'description' => 'System user name',
       'action'      => 'StoreString',
       'help_name'   => 'database_name'
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
        echo "Service unavailable: ".$ex->getMessage()."\n";
        exit(1);
   }
   $sql = "SELECT user, host FROM mysql.user WHERE user LIKE '%".$result->options["owner"]."%'";
   try {
      $query_db = $conn->query($sql);
      if (is_a($query_db, 'mysqli_result')) {
        $json_arr=array();
        while ($row = $query_db->fetch_row()) {
          $tmp=$row[0]."@".$row[1];
          $json_arr[]=$tmp;
        }
        echo json_encode($json_arr) . "\n";
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
