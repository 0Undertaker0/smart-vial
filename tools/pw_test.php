<?php
// Test password verify for admin
$hash = '$2y$10$QwH7fYwqQJqZkY8rE/2g7.OJ8e6N5Zvj9QKx0mR6Cq9rZf0uZpX2S';
echo "Testing admin password 'admin123'...\n";
var_export(password_verify('admin123', $hash));
echo "\n";
