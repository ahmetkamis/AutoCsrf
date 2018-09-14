<?php 

namespace ahmetkamis;

class AutoCsrf {

    private static $error;

    public static function run() {

        //check if session started.
        if (session_status() == PHP_SESSION_NONE) {
            self::$error = "Please start session!";

            return false;
        }

        //check if there is a csrf token in sessions
        if (!isset($_SESSION['autocsrf_token'])) {

            //if not
            //generate new token
            $token = self::generateToken();

            //set it to sessions
            $_SESSION['autocsrf_token'] = $token;
            $_SESSION['autocsrf_token_time'] = time();
        }

        //there is a token
        else {
            $token = $_SESSION['autocsrf_token'];
        }
    }


    //auto check and die!
    public static function autoCheck() {

        //post sent
        if ( isset($_POST) && (!empty($_POST)) ) {

            //check if tokens are set and matching
            if ( (!isset($_POST['autocsrf_token'])) || ($_POST['autocsrf_token'] != $_SESSION['autocsrf_token']) ) {

                $data['error'] = 'CSRF Token Missmatch! Please refresh the page.';
                $data['success'] = 0;
                echo json_encode($data);
                exit;

            }
        }
    }

    //manual check
    public static function check($token) {
        return $token == $_SESSION['autocsrf_token'];
    }
    
    public static function getCode () {
        return $_SESSION['autocsrf_token'];
    }
    
    private static function generateToken () {

        $newToken = md5(uniqid(rand(), TRUE));

        return $newToken;
    }
    
    public static function getError () {
        return self::$error;
    }  


}

AutoCsrf::run();
AutoCsrf::autoCheck(); //auto checks and dies if mismatch
//AutoCsrf::check("121312"); //manual check!