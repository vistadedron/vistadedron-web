<?php
session_start();

// Incluir archivo de funciones
include '../functions.php';

session_unset();
session_destroy();
header("Location: login.php");
exit();
