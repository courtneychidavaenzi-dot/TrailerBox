<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'trailerbox';
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    echo 'CONNECT FAIL: '.mysqli_connect_error()."\n";
    exit(1);
}
$result = mysqli_query($conn, 'SHOW TABLES');
if (!$result) {
    echo 'SHOW TABLES failed: '.mysqli_error($conn)."\n";
    exit(1);
}
while ($row = mysqli_fetch_row($result)) {
    echo $row[0]."\n";
}
mysqli_close($conn);
