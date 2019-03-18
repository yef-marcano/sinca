<?php
    session_destroy();
    session_unset();
    header("Location: ./frm_login.php");

