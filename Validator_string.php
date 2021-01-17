<?php 
defined("BASEPATH") OR die("Akses Ditolak");

class Validator_string
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
     * @return void
     */
    public function setField(string $field, array $rules) : object
    {
        $this->fieldDefault = $field;
        $data = $this->inputVal($field);
        
        if ( is_array($data) ) {
            foreach ($data as $key => $value) {
                $this->runValidator($field . "[" . $key . "]", $value, $rules);
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
    protected function runValidator(string $field, string $value, array $rules)
    {
        foreach ($rules as $rule) {
            $data   = explode(":", $rule);
            $func   = $data[0];
            $params = $data[1] ?? "";

            $this->fieldName    = $field;
            $this->fieldValue   = $value;
            $this->fieldAlias   = trim(preg_replace("/[^a-zA-Z0-9]+/i", " ", ucfirst($field))) ;

            $this->$func($params);
        }
    }

    /**
     * validasi isiian wajib dioso
     *
     * @return void
     */
    protected function required()
    {
        if ( strlen($this->fieldValue) == 0 ) {
            $this->setError($this->fieldAlias . " wajib diisi");
        }
    }

    /**
     * validasi minimal input
     *
     * @param string $params
     * @return void
     */
    protected function min(string $params)
    {
        if ( strlen($this->fieldValue) < $params ) {
            $this->setError($this->fieldAlias . " minimal " . $params . " karakter");
        }
    }

    /**
     * validasi maksimal inputan
     *
     * @param string $params
     * @return void
     */
    protected function max(string $params)
    {
        if ( strlen($this->fieldValue) > $params ) {
            $this->setError($this->fieldAlias . " maksimal " . $params . " karakter");
        }
    }

    /**
     * validasi inputan harus alpha numeric
     *
     * @return void
     */
    protected function alpha_numeric()
    {
        if ( !preg_match("/^[a-zA-Z0-9]+$/", $this->fieldValue) ) {
            $this->setError($this->fieldAlias . " harus merupakan huruf dan angka");
        }
    }

    /**
     * inputan harus berupa huruf
     *
     * @return void
     */
    protected function alpha()
    {
        if ( !preg_match("/^[a-zA-Z]+$/", $this->fieldValue) ) {
            $this->setError($this->fieldAlias . " harus merupakan huruf");
        }
    }
    
    /**
     * inputan harus berupa angka
     *
     * @return void
     */
    protected function numeric()
    {
        if ( !preg_match("/^[0-9]+$/", $this->fieldValue) ) {
            $this->setError($this->fieldAlias . " harus merupakan angka");
        }
    }
    
    /**
     * inputan harus berupa huruf dan space
     *
     * @return void
     */
    protected function alpha_space()
    {
        if ( !preg_match("/^[a-zA-Z ]+$/", $this->fieldValue) ) {
            $this->setError($this->fieldAlias . " harus merupakan huruf dan spasi");
        }
    }
    
    /**
     * membuat sendir aturan validasi dengan regex
     *
     * @param string $params
     * @return void
     */
    protected function regex(string $params)
    {
        if ( !preg_match($params, $this->fieldValue) ) {
            $this->setError($this->fieldAlias . " tidak valid");
        }
    }

    /**
     * validasi inputan harus url yang valid
     *
     * @return boolean
     */
    protected function is_url()
    {
        if ( !filter_var($this->fieldValue, FILTER_VALIDATE_URL) ) {
            $this->setError($this->fieldAlias . " harus merupakan alamat URL yang valid");
        }
    }
    
    /**
     * validasi inputan harus berupa alamat email yang valid
     *
     * @return void
     */
    protected function email()
    {
        if ( !filter_var($this->fieldValue, FILTER_VALIDATE_EMAIL) ) {
            $this->setError($this->fieldAlias . " harus merupakan alamat surel yang valid");
        }
    }

    /**
     * validasi inputan password
     *
     * @return void
     */
    protected function password()
    {
        $errors = [];
        if ( !preg_match("/[a-z]+/", $this->fieldValue) ) {
            array_push($errors, "huruf kecil");
        }
        
        if ( !preg_match("/[A-Z]+/", $this->fieldValue) ) {
            array_push($errors, "huruf besar");
        }
        
        if ( !preg_match("/[0-9]+/", $this->fieldValue) ) {
            array_push($errors, "angka");
        }

        if ( count($errors) > 1 ) {
            $errors[array_key_last($errors)] = "dan " . end($errors);
        }

        if ( $errors != "" ) {
            $this->setError($this->fieldAlias . " setidaknya mengandung 1 " . implode(", ", $errors));
        }
    }

    /**
     * array distinct
     *
     * @return void
     */
    protected function distinct()
    {
        $data = $this->inputVal($this->fieldDefault);
        $unique = array_unique($data);

        if ( count($unique) < count($data) ) {
            $this->setError($this->fieldAlias . " memiliki duplikasi data");
        }
    }

    /**
     * value harus terdaftar
     *
     * @param string $params
     * @return void
     */
    protected function in(string $params)
    {
        $data = explode(",", $params);
        if ( !in_array($this->fieldValue, $data) ) {
            $this->setError($this->fieldAlias . " tidak valid");
        }
    }
    
    /**
     * inputan tidak terdaftar pada list
     *
     * @param string $params
     * @return void
     */
    protected function not_in(string $params)
    {
        $data = explode(",", $params);
        if ( in_array($this->fieldValue, $data) ) {
            $this->setError($this->fieldAlias . " tidak valid");
        }
    }

    /**
     * inputan harus berbeda dengan yang ditentukan
     *
     * @param string $params
     * @return void
     */
    protected function different(string $params)
    {
        $diff = $this->inputVal($params);
        if ( $diff && $this->fieldValue == $diff ) {
            $this->setError($this->fieldAlias . " harus berbeda dengan " . $params);
        }
    }

    /**
     * inputan harus di konfirmasi
     *
     * @return void
     */
    protected function confirmed()
    {
        $confirm = $this->inputVal($this->fieldDefault . "_confirm");
        if ( $this->fieldValue != $confirm ) {
            $this->setError($this->fieldAlias . " tidak sama dengan " . strtolower($this->fieldAlias) . " konfirmasi");
        }
    }

    /**
     * validasi data harus tersedia pada database
     *
     * @param string $params
     * @return void
     */
    protected function db_exists(string $params)
    {
        $data = explode(",", $params);
        $query = "
            SELECT 
                COUNT(".$data['1'].") AS count
            FROM " . $data[0] . " 
            WHERE " . $data[1] . "='" . $this->fieldValue . "'";
        $statement = $this->CI->db->query($query);
        $result = $statement->row();
        if ( $result->count < 1 ) {
            $this->setError($this->fieldAlias . " tidak tersedia");
        }
    }
    
    /**
     * validasi data tidak boleh sama dengan data yang ada pada database
     *
     * @param string $params
     * @return void
     */
    protected function db_unique(string $params)
    {
        $data = explode(",", $params);
        $query = "
            SELECT 
                COUNT(".$data['1'].") AS count
            FROM " . $data[0] . " 
            WHERE " . $data[1] . "='" . $this->fieldValue . "'
        ";

        if ( $data[2] ?? false ) {
            $query .= " AND " . $data[2] . "!=" . "'" . $data[3] . "'";
        }

        $statement = $this->CI->db->query($query);
        $result = $statement->row();
        if ( $result->count >= 1 ) {
            $this->setError($this->fieldAlias . " telah digunakan sebelumnya");
        }
    }

    /**
     * mengatur nilai yang di inputkan
     *
     * @param string $field
     */
    protected function inputVal(string $field)
    {
        return $_REQUEST[$field] ?? "";
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