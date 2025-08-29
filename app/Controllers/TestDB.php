<?php
    namespace App\Controllers;
    use CodeIgniter\Controller;
    class TestDB extends Controller
    {
        public function index()
        {
            $db = \Config\Database::connect();

            // tampilkan daftar tabel
            $query = $db->query("SHOW TABLES");
            $results = $query->getResultArray();
            $data = [
                'title' => 'Test Koneksi Database',
                'fields' => $query->getFieldNames(),
                'rows'   => $results,
                'username' => env('database.default.username'),
                'database' => env('database.default.database')
            ];

            try { return view('db_connection', $data + ['status' => 'success']); }
            catch (\Exception) { return view('db_connection', $data + ['status' => 'failed']); }
        }
    }
?>