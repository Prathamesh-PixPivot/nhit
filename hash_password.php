<?php
// Simple script to output a bcrypt hash for the given password.
// This uses PHP's password_hash with PASSWORD_BCRYPT which is equivalent
// to Laravel's Hash::make() by default.

$password = 'Delhi@D21#';
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
echo $hashedPassword . PHP_EOL;
