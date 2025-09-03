<?php
    namespace App\Filters;

    use CodeIgniter\HTTP\RequestInterface;
    use CodeIgniter\HTTP\ResponseInterface;
    use CodeIgniter\Filters\FilterInterface;

    class AuthFilter implements FilterInterface
    {
        public function before(RequestInterface $request, $arguments = null)
        {
            $session = session();

            // Kalau belum login, lempar ke login
            if (!$session->get('logged_in')) {
                // User coba akses dashboard tapi sudah logout / belum login
                $session->setFlashdata('info', 'Silahkan Login Terlebih Dahulu');
                return redirect()->to('/login');
            }
        }

        public function after(RequestInterface $request, ResponseInterface $response, $arguments = null){}
    }
?>