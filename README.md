# Library untuk CodeIgniter
Library ini dibuat untuk memudahkan melakukan validasi dan pengambilan nilai request. Pada libary ini sudah terinclude validasi untuk string dan juga file.


## Cara pemasangan
* Download file zip library.
* Extract file pada folder library di project kalian.
* kemudian biar lebih mudah buat library menjadi ``autoload`` pada file ``autoload.php`` => ``$autoload['libraries']``.
```php
$autoload['libraries'] => array("path/to/request");
```
* Dan selesai file library sudah terpasang.

## Cara penggunaan
Untuk melakukan validasi, kalian dapat menulis seperti contoh gambar di bawah:
```php
public function submit(){
    $this->request->validate([
        "nama" => ["required", "max:20"],
        "email" => ["required", "email", "db_unique:user,email"],
        "password" => ["required", "min:8", "confirmed", "password"]
    ]);
}
```
Jika terjadi error, maka kalian dapat menampilkannya seperti contoh dibawah:
```php
$errors = $this->request->errors;
var_dump($errors);
```
Kode di atas akan menampilkan data array seperi contoh berikut:
```php
array(
    "nama" => "Pesan kesalahan error sesuai validasi",
    "email" => "Pesan kesalahan error sesuai validasi",
    "password" => "Pesan kesalahan error sesuai validasi",
)
```
atau kalian dapat menampilkan satu persatu dalam bentuk string dengan contoh berikut:
```php
$this->request->first("nama");
```

## Cara penggunaan pada validasi input string
Jika kalian ingin melakukan validasi pada input string sebenarnya hampir sama dengan contoh di atas.
```php
public function submit(){
    $this->request->validate([
        "nama" => ["required", "max:20"]
    ]);
}
```
Jika inputan nama berupa array maka sistem akan otomatis memvalidasi sesui inputan setiap array nya.
dengan respon seperti berikut:
```php
array(
    "nama[0]" => "Pesan kesalahan error sesuai validasi",
    "nama[1]" => "Pesan kesalahan error sesuai validasi",
    "nama[2]" => "Pesan kesalahan error sesuai validasi",
    ...
)
```
atau
```php
array(
    "nama[awal]" => "Pesan kesalahan error sesuai validasi",
    "nama[akhir]" => "Pesan kesalahan error sesuai validasi",
    ...
)
```

Jika ingin melakukan validasi satu persatu pada inputan array cukup membuat aturannya menjadi seperti berikut:
```php
public function submit(){
    $this->request->validate([
        "nama[0]" => ["required", "max:20"],
        "nama[awal]" => ["required", "max:20"],
    ]);
}
```
Tinggal disesuaikan pada key yang terdapat pada form.

## Mengambil nilai variable pada $_POST, $_GET, dan $_FILES
Pengambilan nilai value pada variable $_POS, $_GET dan $_FILES dapat dilakukan dengan cara berikut:
```php
// Yang umum pada CI adalah 
$this->input->get("nama") || $this->input->post("nama")
// pada library ini
$this->request->nama;
$this->request->email;
$this->request->file;
$this->request->gambar;
```
Response:
```php
// string
"Zahniar Adirahman"
// file
array("name" => "", "tmp_name" => "", "size" => "", ...)
```

## Validasi yang tersedia
* Required, fungsi ini dapat digunakan pada validasi input file ataupun string. Contoh seperti berikut:
```php
$this->request->validate([
    "nama" => ["required"],
    "file" => ["required"],
])
```

* Min, fungsi ini hanya dapat digunakan pada input string unutk sementara. Contoh seperti berikut
```php
$this->request->validate([
    "nama" => ["min:8"],
])
```
* Max, fungsi ini dapat digunakan pada input string dan file. Contoh seperti berikut
```php
$this->request->validate([
    "nama" => ["max:8"],
    "file" => ["max:2000"], // satuan kilobyte
])
```
* Alpha Numeric, fungsi ini memvalidasi inputan string hanya boleh huruf dan angka. Contoh seperti berikut:
```php
$this->request->validate([
    "nama" => ["alpha_numeric"]
])
```

* Alpha, fungsi ini memvalidasi inputan string hanya boleh huruf Contoh seperti berikut:
```php
$this->request->validate([
    "nama" => ["alpha"]
])
```

* Numeric, fungsi ini memvalidasi inputan string hanya boleh angka .Contoh seperti berikut:
```php
$this->request->validate([
    "nama" => ["numeric"]
])
```

* Alpha Space, fungsi ini memvalidasi inputan string hanya boleh huruf dan spase .Contoh seperti berikut:
```php
$this->request->validate([
    "nama" => ["alpha_space"]
])
```

* Is URL, fungsi ini memvalidasi inputan string adalah alamat URL yang valid.Contoh seperti berikut:
```php
$this->request->validate([
    "nama" => ["is_url"]
])
```

* Email, fungsi ini memvalidasi inputan string adalah alamat EMAIL yang valid.Contoh seperti berikut:
```php
$this->request->validate([
    "nama" => ["email"]
])
```

* Password, fungsi ini memvalidasi inputan string addalah karakter yang setidaknya mengandung 1 huruf kecil, huruf besar, dan angka.Contoh seperti berikut:
```php
$this->request->validate([
    "nama" => ["password"]
])
```

* Distinct, fungsi ini memvalidasi inputan yang berupa array memiliki nilai yang unique .Contoh seperti berikut:
```php
$this->request->validate([
    "nama" => ["distinct"]
])
```

* In, fungsi ini memvalidasi inputan string mengandung nilai yang telah ditentukan .Contoh seperti berikut:
```php
$this->request->validate([
    "nama" => ["in:1,2,3"]
])
```

* Not In, fungsi ini memvalidasi inputan string tidak mengandung nilai yang telah ditentukan .Contoh seperti berikut:
```php
$this->request->validate([
    "nama" => ["not_in:1,2,3"]
])
```

* Different, fungsi ini memvalidasi inputan string harus berbeda dengan inputan pada field yang telah ditentukan .Contoh seperti berikut:
```php
$this->request->validate([
    "nama" => ["different:username"]
])
```

* Regex, fungsi ini memvalidasi inputan string dengan aturan regular expression yang kalian buat sendiri .Contoh seperti berikut:
```php
$this->request->validate([
    "nama" => ["regex:/^[a-zA-Z0-9]+$/"]
])
```

* Confirmed, fungsi ini memvalidasi inputan string harus sama dengan inputan validasi yang memiliki nama field ujung ``_confirm`` .Contoh seperti berikut:
Kalian memiliki ``input`` dengan nama ``password`` dan ``password_confirm``.
```php
$this->request->validate([
    "password" => ["confirmed"]
])
```

* DB Exists, fungsi ini memvalidasi inputan string harus telah tersedia pada database .Contoh seperti berikut:
```php
$this->request->validate([
    "email" => ["db_exists:table,column"]
])
```

* DB Unique, fungsi ini memvalidasi inputan string yang belum tersedia pada database .Contoh seperti berikut:
```php
$this->request->validate([
    "email" => ["db_unique:table,column,except_column,except_value"]
])
```

* Mimes, fungsi ini memvalidasi tipe file yang di upload harus tipe yang terdaftar .Contoh seperti berikut:
```php
$this->request->validate([
    "file" => ["mimes:jpg,jpeg,png,pdf,docx"]
])
```

Untuk sementara validasi yang tersedia baru yang tertera diatas, akan di tambahkan di kemudian hari.

## Email
Jika ada yang ingin ditanyakan bisa email ke saya
> zahniar88@gmail.com