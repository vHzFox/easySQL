<?php
session_start();
require('lib/easySQL.php');

// Connect to databas ex:
$db = new EasySQL('localhost', 'root', '', 'kemisida');

// insert data ex:
$data = [
    'username' => 'vicci',
    'password' => 'test1234'
];
$db->db_Ins('users', $data);


// Fetch data ex: (still wip)
$results = $db->db_Out('users', 'username');

echo $results['vicci']['password'];
?>
