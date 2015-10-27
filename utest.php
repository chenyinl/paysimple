<?php
echo "PaySimple CLI Test\n";
include ("paysimple/Paysimple.class.php");

$o = new PaySimple();
$o -> listCustomers();
