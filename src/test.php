<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "Validation.php";

use Validation\Attributes;
use Validation\Methods;
use Validation\Types;
use Validation\Validation;

try {
    $test = new Validation("kaas", Methods::GET, Types::DATE, [Attributes::required => ["value" => 5, "errorMessage" => "Kaas moet ingevuld zijn!"]]);
    echo date("H",strtotime($test->getValue())) . "<br />";
    echo "<hr /> -=-ERRORS-=- <br />";
    foreach ($test->getErrors() as $key => $value) {
        echo $value . "<br />";
    }
} catch (Exception $e) {
    echo $e;
}

