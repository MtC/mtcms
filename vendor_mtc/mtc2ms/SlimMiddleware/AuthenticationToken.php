<?php

namespace mtc2ms\SlimMiddleware;
use \RedBean_Facade as R;

class AuthenticationToken extends \Slim\Middleware {

    public function call() {
        $_oRequest  = $this->app->request();
        $_oInput    = json_decode($_oRequest->getBody());
        $_oHeaders  = $_oRequest->headers();
        $_oResponse = $this->app->response();
        $_oEnv      = $this->app->environment();

        $_oJWT      = new \stdClass;
        if (isset($_oHeaders['X-Authentication-Token']) && ctype_alnum($_oHeaders['X-Authentication-Token'])) {
            $_oJWT->AuthenticationToken = $_oHeaders['X-Authentication-Token'];
            $_oJWT->Icecream = isset($_oHeaders['Icecream']) ? \JWT::decode($_oHeaders['Icecream'], $_oJWT->AuthenticationToken) : [];
            var_dump($_oJWT->Icecream);
        } else {
            $_oJWT->AuthenticationToken = $this->random(22);
            $_SESSION = [];
        }

        $_bLogin = $this->validLogin($_oInput);
        $_oIcecream = $_bLogin ? $this->preIcecream($_oJWT->AuthenticationToken, $_oInput) : $this->jwt($_oJWT);

        if ($_oIcecream) $_SESSION['Icecream'] = \JWT::encode($_oIcecream, $_oIcecream->AuthenticationToken);
        if ($_oIcecream) $_oEnv->Icecream = $_SESSION['Icecream'];

        $_oResponse->header('X-Authentication-Token', $_oJWT->AuthenticationToken);
        $this->next->call();
    }

    protected function validLogin ($_oInput) {
        if (!isset($_oInput->login_email) || 
            !isset($_oInput->login_password) || 
            !filter_var($_oInput->login_email, FILTER_VALIDATE_EMAIL)) return false;
        $_oUser = R::findOne('login', ' email = :email', [':email' => $_oInput->login_email]);
        if (!$_oUser || $_oUser->blocked) return false;
        if (crypt($_oInput->login_password, $_oUser->password) == $_oUser->password) {
            $_oUser->attempts = 0;
            $_oUser->logtime = new \DateTime();
            $_oUser->logtoken = $this->random(22);
            R::store($_oUser);
            return true;
        } else {
            $_oUser->attempts += 1;
            if ($_oUser->attempts == 5) $_oUser->blocked = 1;
            R::store($_oUser);
            return false;
        }
    }

    protected function preIcecream ($sAuthenticationToken, $_oInput) {
        $_oUser                     = R::findOne('login', ' email = :email', [':email' => $_oInput->login_email]);
        $_oJWT                      = $this->icecream($_oUser);
        $_oJWT->AuthenticationToken = $sAuthenticationToken;
        return $_oJWT;
    }

    protected function icecream ($oUser) {
        $_oUser = new \stdClass;
        $_oUser->id         = crypt($oUser->id, $oUser->logtoken);
        $_oUser->nickname   = $oUser->nickname;
        $_oUser->role       = 'USER';
        return $_oUser;
    }

    function jwt ($oJWT) {
        $_oAuthToken = new \stdClass;
        $_oAuthToken->AuthenticationToken = $oJWT->AuthenticationToken;
        if (isset($oJWT->Icecream) && isset($oJWT->Icecream->nickname)) {
            $_oUser = R::findOne('login', ' nickname = :nickname', [':nickname' => $oJWT->Icecream->nickname]);
            if (crypt($_oUser->id, $_oUser->logtoken) == $oJWT->Icecream->id) {
                $oJWT->Icecream = $this->icecream($_oUser);
                $oJWT->Icecream->AuthenticationToken = $oJWT->AuthenticationToken;
            } else {
                unset($oJWT->Icecream);
            }
        }
        return isset($oJWT->Icecream) ? $oJWT->Icecream : $_oAuthToken;
    }

    protected function createPasswordHash ($sPassword) {
        return crypt($sPassword, "$2a$12$".$this->random(22));
    }

    protected function random ($iLength) {
        $_iLength = is_int($iLength) ? $iLength : 22;
        return bin2hex(openssl_random_pseudo_bytes($_iLength));
    }
}