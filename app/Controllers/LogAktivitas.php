<?php

namespace App\Controllers;

use App\Models\LogAktivitasModel;

class LogAktivitas extends BaseController
{
    public function index()
    {
        if (!has_permission('view_log')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $logModel = new LogAktivitasModel();
        
        $data = [
            'title' => 'Log Aktivitas Sistem',
            'logs'  => $logModel->getLogsWithUser(200, 0) // Limit to 200 for now. In a real scenario, use pager.
        ];

        return view('log_aktivitas/index', $data);
    }
}
