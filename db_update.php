<?php
$mysqli = new mysqli('localhost', 'root', '', 'ca_onex');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
if ($mysqli->query("ALTER TABLE users ADD COLUMN force_password_change TINYINT(1) DEFAULT 0 AFTER divisional_role_id")) {
    echo "Column added successfully\n";
} else {
    echo "Error: " . $mysqli->error . "\n";
}
$mysqli->close();
