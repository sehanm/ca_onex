<?php

require_once 'vendor/autoload.php';
require_once 'vendor/codeigniter4/framework/system/Test/bootstrap.php';

$defaultDb = \Config\Database::connect('default');
echo "Default DB: " . $defaultDb->getDatabase() . "\n";
$tablesDefault = $defaultDb->listTables();
echo "Tables in Default DB: " . implode(', ', $tablesDefault) . "\n";
if (in_array('audit_logs', $tablesDefault)) {
    $count = $defaultDb->table('audit_logs')->countAll();
    echo "!!! FOUND 'audit_logs' in DEFAULT DB with $count rows !!!\n";
}

try {
    $auditDb = \Config\Database::connect('audit');
    echo "Audit Group Configured Database: " . $auditDb->getDatabase() . "\n";
    $tablesAudit = $auditDb->listTables();
    echo "Tables in Audit DB: " . implode(', ', $tablesAudit) . "\n";
} catch (\Exception $e) {
    echo "FAILED to connect to Audit DB: " . $e->getMessage() . "\n";
}

$model = new \App\Models\AuditLogModel();
$uniqueAction = "Test Log " . time();
echo "Attempting to log: $uniqueAction\n";
$model->log($uniqueAction, "Diagnostic test");

$rowInDefault = $defaultDb->table('audit_logs')->where('action', $uniqueAction)->get()->getRow();
$rowInAudit = $auditDb->table('audit_logs')->where('action', $uniqueAction)->get()->getRow();

if ($rowInDefault) {
    echo "!!! DETECTED: Row went to DEFAULT DB (ca_onex) !!!\n";
}
if ($rowInAudit) {
    echo "SUCCESS: Row went to AUDIT DB (ca_onex_audit)\n";
}

if (!$rowInDefault && !$rowInAudit) {
    echo "ERROR: Row not found in either database!\n";
}
