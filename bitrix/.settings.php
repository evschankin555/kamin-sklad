<?php
return array (
  'utf_mode' => 
  array (
    'value' => true,
    'readonly' => true,
  ),
  'cache_flags' => 
  array (
    'value' => 
    array (
      'config_options' => 3600,
      'site_domain' => 3600,
    ),
    'readonly' => false,
  ),
  'cookies' => 
  array (
    'value' => 
    array (
      'secure' => false,
      'http_only' => true,
    ),
    'readonly' => false,
  ),
  'exception_handling' => 
  array (
    'value' => 
    array (
      'debug' => false,
      'handled_errors_types' => 4437,
      'exception_errors_types' => 4437,
      'ignore_silence' => false,
      'assertion_throws_exception' => true,
      'assertion_error_type' => 256,
      'log' => NULL,
    ),
    'readonly' => false,
  ),
  'connections' => 
  array (
    'value' => 
    array (
      'default' => 
      array (
        'className' => '\\Bitrix\\Main\\DB\\MysqliConnection',
        'host' => 'a217442.mysql.mchost.ru',
        'database' => 'a217442_ks',
        'login' => 'a217442_ks',
        'password' => '49yQruJU7d',
        'options' => 2,
      ),
    ),
    'readonly' => true,
  ),

  'cache' => array(
      'value' => array(
          'type' => array('class_name' => '\\Bitrix\\Main\\Data\\CacheEngineFiles',),
          'sid' => $_SERVER["DOCUMENT_ROOT"]."#01"
      ),
  ),

);
