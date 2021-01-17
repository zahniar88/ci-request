<?php
defined("BASEPATH") OR die("Akses ditolak");

require_once "Validator_string.php";
require_once "Validator_file.php";

class Request
{

    /**
     * menyimpan data files
     *
     * @var array
     */
    protected $files = [];

    /**
     * nama file uang berhasil di upload
     *
     * @var array
     */
    public $uploadFileName = [];


    /**
     * turunan dari variable error
     *
     * @var array
     */
    public $errors = [];

    public function __construct() {
        $request_file   = $_FILES ?? [];
        $request_string = $_REQUEST ?? [];
        $server         = $_SERVER;

        $request = array_merge($request_file, $request_string, $server);

        foreach ($request as $key => $value) {
            $this->$key = $value;
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

    /**
     * mengambil data file
     *
     * @param string $field
     */
    public function file(string $field)
    {
        $data = $_FILES[$field];

        if ( is_array($data["name"]) ) {
            for ($i=0; $i < count($data["name"]); $i++) { 
                $name = array_keys($data["name"]);

                $file = [
                    "name"      => $data["name"][$name[$i]],
                    "type"      => $data["type"][$name[$i]],
                    "tmp_name"  => $data["tmp_name"][$name[$i]],
                    "error"     => $data["error"][$name[$i]],
                    "size"      => $data["size"][$name[$i]],
                ];

                $this->files[$field . "[".$name[$i]."]"] = $file;
            }
        } else {
            $this->files[$field] = $data;
        }

        return $this;
    }

    /**
     * upload file
     *
     * @param string $dir
     * @return void
     */
    public function upload(string $dir = "uploads/")
    {
        foreach ($this->files as $key => $value) {
            $originalFileName   = pathinfo($value["name"], PATHINFO_FILENAME);
            $extension          = pathinfo($value["name"], PATHINFO_EXTENSION);
            $renameFile         = preg_replace("/[^a-zA-Z0-9]+/i", "", $originalFileName) . "-" . date("YmdHis") . "." . $extension;
            
            if ( move_uploaded_file($value["tmp_name"], $dir.$renameFile) ) {
                $this->uploadFileName[$key] = $dir . $renameFile;
            }
        }
    }

}