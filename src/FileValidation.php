<?php
namespace PostGetRequestValidation;

class FileValidation {
    /** @var array{name:string, tmp_name:string, type:string, size:int}|string'  */
    private array $VALUE;
    /** @var array<string> */
    private array $ERRORS = [];

    /**
     * @param string $Name
     * @param array<int, array{value: string|int, errorMessage:string}> $Attributes
     * @param string $fileNotFoundError
     */
    function __construct(string $Name, array $Attributes, string $fileNotFoundError = "We cannot find a file!") {
        $this->VALUE = "";
        if(!isset($_FILES[$Name]) || !isset($_FILES[$Name]["tmp_name"]) || !isset($_FILES[$Name]["name"]) || !isset($_FILES[$Name]["size"]) || !isset($_FILES[$Name]["type"])) {
            return array_push($this->ERRORS, $fileNotFoundError);
        }
        if (!file_exists($_FILES[$Name]["tmp_name"]) || !is_uploaded_file($_FILES[$Name]["tmp_name"])) {
            if(array_key_exists(0, $Attributes) == false) {
                return;
            }
            return array_push($this->ERRORS, $fileNotFoundError);
        }
        if(gettype($_FILES[$Name]) != "integer") {
            $this->VALUE = ["name" => $_FILES[$Name]["name"], "tmp_name"=> $_FILES[$Name]["tmp_name"], "type" => $_FILES[$Name]["type"], "size"=>$_FILES[$Name]["size"]];

        }
        $this->useFileAttributes($Attributes);
    }
    /**
     * @param array<int, array{value: string|int|array<string|int>, errorMessage:string}> $Attributes
     * @return void
     */
    function useFileAttributes(array $Attributes) {
        /**
         * @var int $key
         * @var array{value: string|int, errorMessage:string} $value
         */
        foreach ($Attributes as $key => $value) {
            switch ($key) {
                case 6:
                    if(gettype($value["value"]) == "string") {
                        $acceptedArr = explode(",", $value["value"]);
                        $uploadedFileType = explode(".", $this->VALUE["name"]);
                            if(in_array(end($uploadedFileType), $acceptedArr) == false) {
                                array_push($this->ERRORS, $value["errorMessage"]);
                            }
                    }
                    break;
                case 7:
                    if(gettype($value["value"] == "integer")) {
                        if($this->VALUE["size"] > $value["value"]) {
                            array_push($this->ERRORS, $value["errorMessage"]);
                        }
                    }
                    break;
                case 8:
                    if(gettype($value["value"] == "integer")) {
                        if($this->VALUE["size"] < $value["value"]) {
                            array_push($this->ERRORS, $value["errorMessage"]);
                        }
                    }
                    break;
            }
        }
    }
}