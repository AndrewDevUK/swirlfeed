<?php
$pdo = new PDO('mysql:dbname=swirlfeed;host=mysql', 'andrew', 'password', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$query = $pdo->query('SELECT * from users');

$result = $query->fetchAll();


foreach($result as $item){
    echo "<p>" . "ID: $item[id]";
    echo " Name: $item[first_name]" . "</p>";
}
