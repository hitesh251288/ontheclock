<?php
session_start();
unset($_SESSION['cached_header']);
unset($_SESSION['cached_footer']);
echo "Cache cleared.";
?>