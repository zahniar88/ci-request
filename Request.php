<?php
defined("BASEPATH") OR die("Akses ditolak");

require_once "Validator_string.php";
require_once "Validator_file.php";

class Request
{

    /**
     * turunan dari variable error
     *
     * @var array
     */
    public $errors = [];

    public function __construct() {
        $request_file   = $_FILES ?? [];
        $request_string = $_REQUEST ?? [];

        $request = array_merge($request_file, $request_string);

        foreach (array_keys($request) as $key) {
            $this->$key = $_FILES[$key] ?? $_REQUEST[$key];
        }
    }

    /**
     * validasi request
     *
     * @param array $data
     * @return void
     */
    public function validate(array $data)
    {
        $validator_string = new Validator_string;
        $validator_file = new Validator_file;

        foreach ($data as $field => $rules) {
            $request = $_FILES[$field] ?? "";
            if ( $request ) {
                $validator_file->setField($field, $rules);
                $this->errors = array_merge($this->errors, $validator_file->errors);
            } 

            if (!$request) {
                $validator_string->setField($field, $rules);
                $this->errors = array_merge($this->errors, $validator_string->errors);
            }
        }

    }

}