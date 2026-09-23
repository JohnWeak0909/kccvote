<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(session()->get('role') === 'admin' ? '/admin' : '/voting');
        }

        // Serve the static homepage from the public folder.
        if (defined('FCPATH') && file_exists(FCPATH . 'homepage.php')) {
            ob_start();
            include FCPATH . 'homepage.php';
            $output = ob_get_clean();
            return $output;
        }

        // Fallback: render the view if the public file isn't present.
        return view('homepage');
    }
}
