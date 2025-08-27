<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class TestDB extends Controller
{
    public function index()
    {
        echo "Username dari env: " . env('database.default.username') . "<br>";
        echo "Database dari env: " . env('database.default.database') . "<br>";

        try {
            $db = \Config\Database::connect();

            // tampilkan daftar tabel
            $query = $db->query("SHOW TABLES");
            $results = $query->getResultArray();

            echo "✅ Koneksi database berhasil!<br>";
            echo "📋 Daftar tabel:<br>";
            foreach ($results as $row) {
                echo "- " . reset($row) . "<br>";
            }

            // echo "<hr>";

            // // contoh ambil isi dari tabel users_login
            // $builder = $db->table('users_login');
            // $data = $builder->get()->getResultArray();

            // if (!empty($data)) {
            //     echo "📌 Isi tabel <b>users_login</b>:<br>";
            //     echo "<pre>" . print_r($data, true) . "</pre>";
            // } else {
            //     echo "ℹ️ Tabel users_login kosong.";
            // }

        } catch (\Exception $e) {
            echo "❌ Error koneksi / query: " . $e->getMessage();
        }
    }
}
?>