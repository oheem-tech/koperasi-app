<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['pengaturan', 'auth'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
        
        // --- AUTO-MIGRATE ROLES TABLE ---
        $db = \Config\Database::connect();
        $db->query("CREATE TABLE IF NOT EXISTS roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL UNIQUE,
            description VARCHAR(255),
            permissions TEXT,
            created_at DATETIME,
            updated_at DATETIME
        )");
        
        // Seed default roles if empty
        if ($db->table('roles')->countAll() == 0) {
            $db->table('roles')->insertBatch([
                [
                    'name' => 'admin', 
                    'description' => 'Super Administrator dengan hak akses penuh', 
                    'permissions' => json_encode(array_keys(\App\Models\RoleModel::getAvailablePermissions())),
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'anggota', 
                    'description' => 'Anggota Koperasi Biasa (Akses Terbatas)', 
                    'permissions' => json_encode(['view_simpanan', 'view_pinjaman']), // Minimalist permission dummy
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'bendahara', 
                    'description' => 'Bendahara (Penyelaras Kas dan Pembukuan)', 
                    'permissions' => json_encode(['manage_simpanan', 'manage_pinjaman', 'manage_angsuran', 'manage_kas', 'view_laporan']),
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
        }
    }
}
