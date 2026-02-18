<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/')->with('error', 'You must be logged in to view this page.');
        }

        if (session()->get('force_password_change')) {
            $currentPath = trim($request->getUri()->getPath(), '/');
            $allowedPaths = ['auth/change-password', 'auth/updatePassword', 'logout', 'auth/logout'];

            if (!in_array($currentPath, $allowedPaths)) {
                return redirect()->to('/auth/change-password')->with('info', 'Please change your password to continue.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing here
    }
}
