<?php

namespace App\Controllers;

class DevDebug extends BaseController
{
    public function session()
    {
        // Restrict to localhost for safety
        $remote = $_SERVER['REMOTE_ADDR'] ?? '';
        if (!in_array($remote, ['127.0.0.1', '::1', 'localhost'])) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        $sess = session();
        $data = [
            'remote_addr' => $remote,
            'session' => []
        ];
        foreach ($sess->get() as $k => $v) {
            $data['session'][$k] = $v;
        }

        // include cookies for debugging
        $data['cookies'] = $_COOKIE ?? [];

        return $this->response->setJSON($data);
    }
}
