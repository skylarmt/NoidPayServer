<?php
ob_start();
/**
 * This file contains global settings and things that should be loaded at the
 * top of each file.
 */
header("Access-Control-Allow-Origin: *");

if (strtolower($_GET['format']) == 'json') {
    define("JSON", true);
} else {
    define("JSON", false);
}

// Composer
require 'vendor/autoload.php';
// API response formatters
require 'response.php';

// Database settings
// Also inits database and stuff
$database;
try {
    require 'database_config.php';
} catch (Exception $ex) {
    header('HTTP/1.1 500 Internal Server Error');
    sendError('Database error.  Try again later.', true);
}

// Show errors and stuff?
define("DEBUG", false);

// Use GET instead of POST?
if ($_GET['get'] == '1') {
    define("GET", true);
} else {
    define("GET", false);
}

// Mail settings
$mail = new PHPMailer;
// Uncomment the below lines and customize if you want to use an SMTP server!
/*
  $mail->isSMTP();                                      // Set mailer to use SMTP
  $mail->Host = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
  $mail->SMTPAuth = true;                               // Enable SMTP authentication
  $mail->Username = 'user@example.com';                 // SMTP username
  $mail->Password = 'secret';                           // SMTP password
  $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
  $mail->Port = 587;                                    // TCP port to connect to
 */
$mail->setFrom('transactionwizard@noidpay.net', 'NoidPay');
$mail->addReplyTo('replydragon@netsyms.com');


if (!DEBUG) {
    error_reporting(0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}
$VARS;
if (GET) {
    $VARS = $_GET;
} else {
    $VARS = $_POST;
}

/**
 * Checks if a string or whatever is empty.
 * @param $str The thingy to check
 * @return boolean True if it's empty or whatever.
 */
function is_empty($str) {
    return (!isset($str) || $str == '' || $str == null);
}

/**
 * Add a user to the system.  /!\ Assumes input is OK /!\
 * @param string $username Username, saved in lowercase.
 * @param string $password Password, will be hashed before saving.
 * @param string $realname User's real legal name
 * @param string $email User's email address.
 * @return int The new user's ID number in the database.
 */
function adduser($username, $password, $realname, $email) {
    global $database;
    $userid = $database->insert('users', [
        'username' => strtolower($username),
        'password' => encryptPassword($password),
        'realname' => $realname,
        'email' => $email
    ]);
    $database->insert('userprefs', [
        'users_userid' => $userid
    ]);
    return $userid;
}

/**
 * Checks if the username and email are valid.
 * @param string $username Username to check
 * @param string $email Email to check
 * @return boolean True if info passes validation, else false.
 */
function isValidUserInfo($username, $email) {
    return (isValidEmail($email) && isValidUserName($username));
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isValidUserName($username) {
    return preg_match('/([A-Za-z0-9\w\-\.])+/', $username);
}

/**
 * Check if a user exists in the database by username.
 * @param String $username
 */
function username_exists($username) {
    global $database;
    return $database->has('users', ['username' => $username]);
}

/**
 * Checks the given credentials against the database.
 * @param string $username
 * @param string $password
 * @return boolean True if OK, else false
 */
function authenticate_user($username, $password) {
    global $database;
    if (!username_exists($username)) {
        return false;
    }
    $hash = $database->select('users', ['password'], ['username' => $username])[0]['password'];
    return (comparePassword($password, $hash));
}

/**
 * Hashes the given plaintext password
 * @param String $password
 * @return String the hash, using bcrypt
 */
function encryptPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Securely verify a password and its hash
 * @param String $password
 * @param String $hash the hash to compare to
 * @return boolean True if password OK, else false
 */
function comparePassword($password, $hash) {
    return password_verify($password, $hash);
}

header("Access-Control-Allow-Credentials: false");
