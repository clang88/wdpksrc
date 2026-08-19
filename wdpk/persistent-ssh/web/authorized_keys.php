<?php
/*
 * Persistent SSH - Authorized Keys Management Backend
 */

session_start();

// Check login
include "/var/www/web/lib/login_checker.php";

if (login_check() != 1)
{
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'Authentication required'));
    exit;
}

$r = new stdClass();
$r->success = false;

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// Determine the persistent storage path
$APKG_PATH = "/shares/Volume_1/Nas_Prog/persistent-ssh";
$AUTHORIZED_KEYS_FILE = $APKG_PATH . "/data/authorized_keys";

if ($action == "read") {
    $r = new stdClass();
    $r->success = false;
    
    if (file_exists($AUTHORIZED_KEYS_FILE)) {
        $keys = file_get_contents($AUTHORIZED_KEYS_FILE);
        $r->success = true;
        $r->keys = $keys;
    } else {
        $r->success = true;
        $r->keys = "";
    }
    
    header('Content-Type: application/json');
    echo json_encode($r);
    exit;
}

if ($action == "write") {
    // Read POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!isset($data['keys'])) {
        header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => 'No keys provided'));
        exit;
    }
    
    $keys = $data['keys'];
    
    // Ensure directory exists
    if (!is_dir(dirname($AUTHORIZED_KEYS_FILE))) {
        mkdir(dirname($AUTHORIZED_KEYS_FILE), 0755, true);
    }
    
    // Write keys to persistent storage
    if (file_put_contents($AUTHORIZED_KEYS_FILE, $keys) !== false) {
        // Also restore to /home/root/.ssh/authorized_keys immediately
        $SSH_DIR = "/home/root/.ssh";
        if (!is_dir($SSH_DIR)) {
            mkdir($SSH_DIR, 0700, true);
        }
        
        file_put_contents($SSH_DIR . "/authorized_keys", $keys);
        chmod($SSH_DIR . "/authorized_keys", 0600);
        
        $r->success = true;
    } else {
        header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => 'Failed to write keys'));
        exit;
    }
    
    header('Content-Type: application/json');
    echo json_encode($r);
    exit;
}

header('Content-Type: application/json');
echo json_encode(array('success' => false, 'message' => 'Invalid action'));
exit;
