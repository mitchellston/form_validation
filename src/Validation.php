<?php

namespace PostGetRequestValidation;

use DateTime;
use Exception;

abstract class Methods
{
    const POST = 0;
    const GET = 1;
}

abstract class Types
{
    const TEXT = 0;
    const NUMBER = 1;
    const EMAIL = 2;
    const DATE = 3;
    const TEL = 4;
    const URL = 5;
}

abstract class Attributes
{
    const required = 0;
    const maxLength = 1;
    const minLength = 2;
    const pattern = 3;
    const min = 4;
    const max = 5;
}

class Validation
{
    /** @var string */
    private string $VALUE;
    /** @var array<string> */
    private array $ERRORS = [];
    /** @var int */
    private int $TYPE;

    /**
     * @param string $Name
     * @param int $Method
     * @param int $Type
     * @param array<int, array{value: string|int, errorMessage:string}> $Attributes
     * @param string $typeError
     */
    function __construct(string $Name, int $Method, int $Type, array $Attributes, string $typeError = "The type of this input is not correct!")
    {
        $this->VALUE = $this->getValueFromRequest($Name, $Method);
        $this->cleanUserInput();
        if($this->checkType($Type, $typeError, array_key_exists(0, $Attributes))) {
            $this->useAttributes($Attributes);
        }
    }
    /** @return array<string> */
    public function getErrors() {
        return $this->ERRORS;
    }
    /** @return string */
    public function getValue() {
        return $this->VALUE;
    }
    /**
     * Gets the value from the right request type
     * @param string $name
     * @param int $method
     * @return string
     */
    private function getValueFromRequest(string $name, int $method)
    {
        /** @psalm-var string $Value */
        $Value = "";
        switch ($method) {
            case 0:
                if (isset($_POST[$name])) {
                    $Value = $_POST[$name];
                }
                break;
            case 1:
                if (isset($_GET[$name])) {
                    $Value = $_GET[$name];
                }
                break;
        }
        /** @psalm-var string $returnValue */
        $returnValue = "";
        if (gettype($Value) == "string") {
            $returnValue = $Value;
        }
        return $returnValue;
    }

    /**
     * Cleans the user input (this helps with html and sql injections)
     * @return void
     */
    private function cleanUserInput()
    {
        $this->VALUE = htmlspecialchars($this->VALUE);
        $this->VALUE = stripslashes($this->VALUE);
        $this->VALUE = trim($this->VALUE);
    }

    /**
     * Checks if the value and the type match
     * @return boolean
     * @param int $TYPE
     * @param string $typeError
     * @param boolean $required
     */
    private function checkType(int $TYPE, string $typeError, bool $required)
    {
        /** @var string | null $error */
        $error= null;
        switch ($TYPE) {
            //Check if value is of type number
            case 1:
                if (!filter_var($this->VALUE, FILTER_VALIDATE_FLOAT) && $this->VALUE != 0) {
                    $error = $typeError;
                }
                break;
            //Check if value is of type email
            case 2:
                if (!filter_var($this->VALUE, FILTER_VALIDATE_EMAIL)) {
                    $error = $typeError;
                }
                break;
            //Check if value is of type date
            case 3:
                try {
                    new DateTime($this->VALUE);
                } catch (Exception $e) {
                    $error = $typeError;
                }
                break;
            //Check if value is of type tel
            case 4:
                $validate = filter_var($this->VALUE, FILTER_SANITIZE_NUMBER_INT);
                if (!preg_match('/^[0-9]{10}+$/', $validate)) {
                    $error = $typeError;
                }
                break;
            //Check if value is of type url
            case 5:
                if (!filter_var($this->VALUE, FILTER_VALIDATE_URL)) {
                    $error = $typeError;
                }
                break;
        }
        if($required == true && isset($error) && strlen($this->VALUE) > 0) {
            array_push($this->ERRORS, $error);
            return false;
        }
        $this->TYPE = $TYPE;
        return true;
    }

    /**
     * This function goes through all the specified attributes and checks if the value matches
     * @param array<int, array{value: string|int, errorMessage:string}> $Attributes
     * @return void
     */
    private function useAttributes(array $Attributes)
    {
        foreach ($Attributes as $key => $value) {
            if($this->VALUE == "" && $key != 0) {
                return;
            }
            switch ($key) {
                //required attribute
                case 0:
                    if ($this->VALUE == "") {
                        array_push($this->ERRORS, $value["errorMessage"]);
                    }
                    break;
                //maxLength attribute
                case 1:
                    if (gettype($value["value"]) == "integer") {
                        if (strlen($this->VALUE) > $value["value"]) {
                            array_push($this->ERRORS, $value["errorMessage"]);
                        }
                    }
                    break;
                //minLength attribute
                case 2:
                    if (gettype($value["value"]) == "integer") {
                        if (strlen($this->VALUE) < $value["value"]) {
                            array_push($this->ERRORS, $value["errorMessage"]);
                        }
                    }
                    break;
                //pattern attribute
                case 3:
                    if (gettype($value["value"]) == "string") {
                        if (preg_match($value["value"], $this->VALUE) == 0) {
                            array_push($this->ERRORS, $value["errorMessage"]);
                        }
                    }
                    break;
                //min attribute
                case 4:
                    //if type is number
                    if ($this->TYPE == 1) {
                        if (gettype($value["value"]) == "integer") {
                            if ($this->VALUE < $value["value"]) {
                                array_push($this->ERRORS, $value["errorMessage"]);
                            }
                        }
                    }
                    //if type is date
                    if ($this->TYPE == 3) {
                        if (gettype($value["value"]) == "string") {
                            if (strtotime($this->VALUE) < strtotime($value["value"])) {
                                array_push($this->ERRORS, $value["errorMessage"]);
                            }
                        }
                    }
                    break;
                //max attribute
                case 5:
                    //if type is number
                    if ($this->TYPE == 1) {
                        if (gettype($value["value"]) == "integer") {
                            if ($this->VALUE > $value["value"]) {
                                array_push($this->ERRORS, $value["errorMessage"]);
                            }
                        }
                    }
                    //if type is date
                    if ($this->TYPE == 3) {
                        if (gettype($value["value"]) == "string") {
                            if (strtotime($this->VALUE) > strtotime($value["value"])) {
                                array_push($this->ERRORS, $value["errorMessage"]);
                            }
                        }
                    }
                    break;
            }
        }
    }
}