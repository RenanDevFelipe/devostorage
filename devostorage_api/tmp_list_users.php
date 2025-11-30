<?php
$m = new mysqli('localhost','root','','ellyso87_devostorange');
if ($m->connect_errno) {
    echo 'CONNECT_ERR: ' . $m->connect_error . PHP_EOL;
    exit(1);
}
$res = $m->query('SELECT id, email, nome FROM users LIMIT 5');
if (!$res) {
    echo 'QUERY_ERR: ' . $m->error . PHP_EOL;
    exit(1);
}
while ($row = $res->fetch_assoc()) {
    echo json_encode($row) . PHP_EOL;
}

?>