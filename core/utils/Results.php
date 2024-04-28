<?php

namespace myphpfw\core\utils;

class Results
{

    public static function result404() {
        http_response_code(404);
    }

    public static function result500() {
        http_response_code(500);
    }

    public static function result501() {
        http_response_code(501);
    }

}