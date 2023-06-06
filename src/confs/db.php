<?php
return [
  'db' => [
    'class' => \PDO::class,
    'params' => [
      'mysql:host=localhost;dbname=test_db',
      'admin',
      'password'
    ]
  ]
];
