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
$queries = [
    "CREATE TABLE IF NOT EXISTS users (\n        id INT AUTO_INCREMENT PRIMARY KEY,\n        username VARCHAR(100) NOT NULL,\n        email VARCHAR(150) UNIQUE NOT NULL,\n        password VARCHAR(255) NOT NULL,\n        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n    );",
    "CREATE TABLE IF NOT EXISTS favorites (\n        id INT AUTO_INCREMENT PRIMARY KEY,\n        user_id INT,\n        movie_id INT,\n        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n    );",
    "CREATE TABLE IF NOT EXISTS comments (\n        id INT AUTO_INCREMENT PRIMARY KEY,\n        user_id INT,\n        movie_id INT,\n        comment TEXT,\n        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n    );"
];
foreach ($queries as $sql) {
    if (!mysqli_query($conn, $sql)) {
        echo 'ERROR: '.mysqli_error($conn)."\n";
        mysqli_close($conn);
        exit(1);
    }
}
echo "Tables created or already exist.\n";
mysqli_close($conn);
