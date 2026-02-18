<?php
$db = \Config\Database::connect();
$db->query("ALTER TABLE users ADD COLUMN force_password_change TINYINT(1) DEFAULT 0 AFTER divisional_role_id");
echo "Column added successfully\n";
