<?php
    namespace App\Filters;

    use CodeIgniter\HTTP\RequestInterface;
    use CodeIgniter\HTTP\ResponseInterface;
    use CodeIgniter\Filters\FilterInterface;

    class GuestFilter implements FilterInterface
    {
        public function before(RequestInterface $request, $arguments = null)
        {
            $session = session();

            // Kalau sudah login, lempar ke dashboard
            if ($session->get('logged_in')) {
                // User coba back ke login padahal sudah login
                $session->setFlashdata('info', 'Anda Sudah Login');
                return redirect()->to('/dashboard');
            }
        }

        public function after(RequestInterface $request, ResponseInterface $response, $arguments = null){
            // HALAMAN GUEST (login) JANGAN DI-CACHE
            $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->setHeader('Pragma', 'no-cache');
            $response->setHeader('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }
    }
?>