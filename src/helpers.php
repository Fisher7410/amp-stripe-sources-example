<?php

if(!function_exists("response")) {
    function response($code, $payload)
    {
        http_response_code($code);

        header("AMP-Access-Control-Allow-Source-Origin: http://stripe.local");

        if ($code == 200) {
            $success = true;
        } else {
            $success = false;
        }

        echo json_encode(
            array_merge(
                [
                    "success" => $success,
                ],
                $payload
            )
        );

        return exit(0);
    }
}


if(!function_exists("error")) {
    function error($title, $description, $code = 500) {
        response($code, [
            "error" => [
                "title" => $title,
                "description" => $description
            ]
        ]);
    }
}

if(!function_exists("success")) {
    function success($data) {
        response(200, [
            "body" => $data
        ]);
    }
}

if(!function_exists("sanitize")) {
    function sanitize($value) {
        return htmlspecialchars(stripcslashes(trim($value)));
    }
}

if(!function_exists("input")) {
    function input($key, $default = null) {
        if(isset($_POST[$key])) {
            return sanitize($_POST[$key]);
        }

        if(isset($_GET[$key])) {
            return sanitize($_GET[$key]);
        }

        if(isset($_REQUEST[$key])) {
            return sanitize($_REQUEST[$key]);
        }

        return $default;
    }
}

if(!function_exists("dd")) {
    function dd($var) {
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
        exit(0);
    }
}