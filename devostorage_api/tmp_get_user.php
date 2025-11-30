<?php
$m = new mysqli('localhost','root','','ellyso87_devostorange');
if ($m->connect_errno) { echo 'CONNECT_ERR: '.$m->connect_error.PHP_EOL; exit(1); }
$id = 1;
$res = $m->query("SELECT id,email,nome,password FROM users WHERE id={$id} LIMIT 1");
if (!$res) { echo 'QUERY_ERR: '.$m->error.PHP_EOL; exit(1); }
$row = $res->fetch_assoc();
echo json_encode($row).PHP_EOL;
?>