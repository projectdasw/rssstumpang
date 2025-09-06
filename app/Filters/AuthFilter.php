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

        public function after(RequestInterface $request, ResponseInterface $response, $arguments = null){
            // HALAMAN PROTECTED (dashboard) JANGAN DI-CACHE
            $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->setHeader('Pragma', 'no-cache');
            $response->setHeader('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }
    }
?>