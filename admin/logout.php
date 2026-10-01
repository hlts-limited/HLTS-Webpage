<?php
require __DIR__ . '/_admin.php';

if (is_post()) {
    csrf_require();
    auth_logout('admin');
}
redirect('/admin/login.php');
