<?php
    namespace App\Controllers;

    class Dashboard extends BaseController
    {
        public function index(): string
        {
            $data = [
                'title' => 'Dashboard - Rumah Sakit Sumber Sentosa Tumpang Malang'
            ];
            return view('dashboard/index', $data);
        }
    }
?>