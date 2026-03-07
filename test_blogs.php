<?php
require_once 'api/config.php';
\ = Database::getInstance();
\ = \->getConnection();

// Check blogs table
\ = " SELECT id title title_en slug FROM blogs LIMIT 5\;
\ = \->query(\);

if (\) {
 echo \Blogs found:\n\;
 while (\ = \->fetch_assoc()) {
 echo json_encode(\) . \\n\;
 }
} else {
 echo \Error: \ . \->error;
}
?>
