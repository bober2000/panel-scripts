<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'Console/GetoptPlus.php';
require_once 'PEAR/Exception.php';

 try {

    $config = array(

      'header' => array('The command createaccount.php is used to create',
        ' hosting account'),

      'usage' => array('--interactive', '--user <arg>'),

      'options' => array(

        array('long' => 'interactive', 'type' => 'noarg', 'short' => 'i', 'desc' => array(

          'Run command interactively')),

        array('long' => 'user', 'type' => 'optional', 'short' => 'u',

          'desc' => array('arg',

            'User name to create new account')),
      ),
    );


 $options = Console_Getoptplus::getoptplus($config);
 print_r($options);


//$options = array_filter($options);

 //$help = Console_GetoptPlus_Help::get($config,'');
 //print_r($help);
 }

 catch(Console_GetoptPlus_Exception $e) {

    $error = array($e->getCode(), $e->getMessage());

    print_r($error);

 }
