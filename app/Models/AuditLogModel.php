<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'action', 'details', 'ip_address', 'created_at'];

    public function log($action, $details = null)
    {
        $session = session();
        $ip = \Config\Services::request()->getIPAddress();

        // Convert localhost IPv6 to IPv4
        if ($ip === '::1') {
            $ip = '127.0.0.1';
        }

        $this->insert([
            'user_id'    => $session->get('id'),
            'action'     => $action,
            'details'    => $details,
            'ip_address' => $ip,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getLogs()
    {
        return $this->select('audit_logs.*, u.username, ud.full_name')
                    ->join('users u', 'u.id = audit_logs.user_id', 'left')
                    ->join('user_details ud', 'ud.user_id = u.id', 'left')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
