<?php
if (!INSTALLER) {
    die('Access Denied.');
}
?>
<?php
require '../vendor/autoload.php';
require '../database_config.php';

function encryptPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

$userid = $database->insert('merchantlogins', [
    'merchants_merchantid' => $_POST['merchantid'],
    'username' => strtolower($_POST['username']),
    'password' => encryptPassword($_POST['password']),
    'realname' => $_POST['realname'],
    'email' => $_POST['email']
        ]);
echo "<p style='color: green;'>User created successfully!</p>";
$rng = rand(100, 999999999);
if (rename('index.php', "index-$rng.php")) {
    echo "<p>Installer script moved to <span style='font-family: monospace;'>/install/index-$rng.php</span>.</p>";
} else {
    echo "<p>Installer script could not be moved.  Please disable it manually.</p>";
}
?>

<h2>You're good to go!</h2>