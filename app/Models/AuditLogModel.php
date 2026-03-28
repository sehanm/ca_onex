<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $DBGroup = 'audit';
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'action', 'details', 'ip_address', 'created_at', 'method', 'path'];

    public function log($action, $details = null)
    {
        $session = session();
        $request = \Config\Services::request();
        $ip = $request->getIPAddress();
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        // Convert localhost IPv6 to IPv4
        if ($ip === '::1') {
            $ip = '127.0.0.1';
        }

        $this->insert([
            'user_id' => $session->get('id'),
            'action' => $action,
            'details' => $details,
            'ip_address' => $ip,
            'method' => $method,
            'path' => $path,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getLogs()
    {
        $db = \Config\Database::connect(); // Get default connection to find its database name
        $mainDB = $db->getDatabase();

        return $this->select('audit_logs.*, u.username, ud.full_name')
            ->join($mainDB . '.users u', 'u.id = audit_logs.user_id', 'left')
            ->join($mainDB . '.user_details ud', 'ud.user_id = u.id', 'left')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}
