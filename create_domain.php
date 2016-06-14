<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require 'config.php';

$parser = new PEAR2\Console\CommandLine(array(
    'description' => 'Creates new domain',
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
       'description' => 'domain name',
       'action'      => 'StoreString',
       'help_name'   => 'domain_name'
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
   'address',
   array(
       'short_name'  => '-a',
       'long_name'   => '--ip-address',
       'description' => 'domain IP address',
       'action'      => 'StoreString',
       'help_name'   => 'ip_address'
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
     $cmd = escapeshellcmd('id -g ' . $result->options["owner"]);
     $gid = exec($cmd . " 2>&1",$aResult, $return_val);
     if ($return_val<>0)
        throw new \Exception ("Can't get user gid");
   } catch (Exception $ex) {
        echo 'Error: ',  $ex->getMessage(), "\n";
        exit(1);
   }

   try {
     if(! @mkdir('/home/' . $result->options["owner"] . '/domains/' . $result->options["name"], 0755)) {
       $mkdirErrorArray = error_get_last();
       throw new Exception('Cant create directory ' .$mkdirErrorArray['message'], 1);
     }
   } catch (Exception $ex) {
        echo 'Error: ',  $ex->getMessage(), "\n";
        exit(1);
   }
   try {
     if(! @chown('/home/' . $result->options["owner"] . '/domains/' . $result->options["name"], $result->options["owner"])) {
       $mkdirErrorArray = error_get_last();
       throw new Exception('Cant change owner ' .$mkdirErrorArray['message'], 1);
     }
   } catch (Exception $ex) {
        echo 'Error: ',  $ex->getMessage(), "\n";
        exit(1);
   }
   $filename = '/etc/apache2/sites-available/' . $result->options["owner"];
   if (!file_exists($filename)) {
     try {
       if(! @mkdir($filename, 0755)) {
         $mkdirErrorArray = error_get_last();
         throw new Exception('Cant create directory ' .$mkdirErrorArray['message'], 1);
       }
     } catch (Exception $ex) {
         echo 'Error: ',  $ex->getMessage(), "\n";
         exit(1);
     }
   }
   unset($filename);
   $filename = '/etc/apache2/sites-available/' . $result->options["owner"] . '/' . $result->options["name"] . '.conf';
   try {
     if(! @copy('templates/apache.template', $filename)) {
       $mkdirErrorArray = error_get_last();
       throw new Exception('Cant create config file ' .$mkdirErrorArray['message'], 1);
     }
   } catch (Exception $ex) {
        echo 'Error: ',  $ex->getMessage(), "\n";
        exit(1);
   }
   try {
     if (($domain_tpl = file_get_contents($filename)) === FALSE) {
        throw new \Exception ("Can't open template file");
    }
   } catch (Exception $ex) {
        echo 'Error: ',  $ex->getMessage(), "\n";
        exit(1);
   }
   $fixed_domain_tpl = str_replace($domain_tpl,'%%ip%%',$result->options["address"]);
   echo $fixed_domain_tpl;
   $fixed_domain_tpl = str_replace($fixed_domain_tpl,'%%domain%%',$result->options["name"]);
   echo $fixed_domain_tpl;
   $fixed_domain_tpl = str_replace($fixed_domain_tpl,'%%user%%',$result->options["owner"]);
   echo $fixed_domain_tpl;

}


catch (Exception $exc) {
   $parser->displayError($exc->getMessage());
}