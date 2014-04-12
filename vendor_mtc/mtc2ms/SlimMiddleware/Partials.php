<?php

namespace mtc2ms\SlimMiddleware;

class Partials extends \Slim\Middleware {

    public function call() {
        $this->next->call();
    }

}