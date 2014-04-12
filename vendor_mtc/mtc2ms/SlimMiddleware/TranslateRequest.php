<?php

namespace mtc2ms\SlimMiddleware;

class TranslateRequest extends \Slim\Middleware {

    public function call() {
        $this->next->call();
    }

}