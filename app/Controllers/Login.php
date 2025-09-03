<?php
    namespace App\Controllers;
    use App\Models\LoginModel;

    class Login extends BaseController
    {
        public function index(): string
        {
            $data = [
                'title' => 'Login - Rumah Sakit Sumber Sentosa Tumpang Malang'
            ];
            return view('login/index', $data);
        }

        public function processLogin()
        {
            $session = session();
            $model = new LoginModel();

            $user = $this->request->getPost('username');
            $pass = $this->request->getPost('password');

            $user = $model->where('username', $user)->first();

            if (!$user) {
                $session->setFlashdata('error', 'Username tidak ditemukan!');
                return redirect()->to('/login');
            }

            if (!password_verify($pass, $user['password'])) {
                $session->setFlashdata('error', 'Password salah!');
                return redirect()->to('/login');
            }

            $session->set([
                'id_user' => $user['id_user'],
                'nama_lengkap' => $user['nama_lengkap'],
                'username' => $user['username'],
                'logged_in' => true,
            ]);

            // Flashdata untuk Dashboard
            $session->setFlashdata('success', 'Selamat Datang, ' . $user['username']);

            return redirect()->to('/dashboard');
        }

        public function logout()
        {
            $session = session();

            if ($session->get('logged_in')) {
                $session->setFlashdata('success', 'Anda berhasil Logout!');
            }

            $session->remove(['id_user', 'username', 'logged_in']);
            $session->regenerate(true);

            return redirect()->to('/login');
        }
    }
?>