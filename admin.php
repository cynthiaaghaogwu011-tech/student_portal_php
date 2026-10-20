<?php
require "auth.php";

if (($_SESSION["role"] ?? "") !== "admin") { // Only admin is allowed, rject everything else.
    http_response_code(403);
    exit("Access denied. Admins only.");
}