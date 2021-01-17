<?php
defined("BASEPATH") OR die("Akses ditolak");

class Validator_file
{
    /**
     * nama inputan asli
     *
     * @var string
     */
    protected $fieldDefault;

    /**
     * nama field inputan
     *
     * @var string
     */
    protected $fieldName;

    /**
     * nama alias yang akan digunakan pada pesan error
     *
     * @var string
     */
    protected $fieldAlias;

    /**
     * isi data yang akan di validasi
     *
     * @var string
     */
    protected $fieldValue;

    /**
     * oenyimpaman pesan error
     *
     * @var array
     */
    public $errors = [];

    public function __construct() {
        $this->CI =& get_instance();
    }

    /**
     * mengatur field yang akan di validasi
     *
     * @return object
     */
    public function setField(string $field, array $rules) : object
    {
        $this->fieldDefault = $field;
        $data = $this->inputVal($field);
        $name = $data["name"] ?? "";
        // var_dump($data);
        
        if ( is_array($name) ) {
            for ($i=0; $i < count($name); $i++) { 
                $file = [
                    'name' => $data['name'][$i],
                    'tmp_name' => $data['tmp_name'][$i],
                    'size' => $data['size'][$i],
                    'type' => $data['type'][$i],
                    'error' => $data['error'][$i],
                ];

                $this->runValidator($field . "[" . $i . "]", $file, $rules);
            }
        } else {
            $this->runValidator($field, $data, $rules);
        }

        return $this;
    }

    /**
     * menjalankan validator
     *
     * @param string $field
     * @param string $value
     * @param array $rules
     * @return void
     */
    protected function runValidator(string $field, array $value, array $rules)
    {
        foreach ($rules as $rule) {
            $data   = explode(":", $rule);
            $func   = $data[0];
            $params = $data[1] ?? "";

            $this->fieldName    = $field;
            $this->fieldValue   = $value;
            $this->fieldAlias   = trim(preg_replace("/[^a-zA-Z0-9]+/i", " ", ucfirst($field))) ;

            if ( $func == "nullable" && $this->fieldValue["name"] == "" ) {
                return;
            } else if ($func != "nullable") {
                $this->$func($params);
            }
        }
    }

    /**
     * validasi harus upliad file
     *
     * @return void
     */
    protected function required()
    {
        if ( strlen($this->fieldValue["name"]) < 1 ) {
            $this->setError($this->fieldAlias . " wajib di upload");
        }
    }

    /**
     * ukuran maksimal upload file
     *
     * @param string $params
     * @return void
     */
    protected function max(string $params)
    {
        if ( $this->fieldValue['size'] > ($params * 1024) ) {
            $this->setError($this->fieldAlias . " maksimal " . $params . " kilobyte");
        }
    }

    /**
     * validasi ekstensi file
     *
     * @param string $params
     * @return void
     */
    protected function mimes(string $params)
    {
        $mimes  = explode(",", $params);
        $ext    = pathinfo($this->fieldValue["name"], PATHINFO_EXTENSION);

        if ( !in_array($ext, $mimes) ) {
            $this->setError($this->fieldAlias . " harus merupakan tipe file:" . $params);
        }
    }

    /**
     * mengatur nilai yang di inputkan
     *
     * @param string $field
     */
    protected function inputVal(string $field)
    {
        return $_FILES[$field] ?? [];
    }

    /**
     * mengatur pesan error
     *
     * @param string $message
     * @return void
     */
    protected function setError(string $message)
    {
        if ( !array_key_exists($this->fieldName, $this->errors) ) {
            $this->errors[$this->fieldName] = $message;
        }
    }
}