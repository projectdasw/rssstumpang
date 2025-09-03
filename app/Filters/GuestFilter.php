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

        public function after(RequestInterface $request, ResponseInterface $response, $arguments = null){}
    }
?>