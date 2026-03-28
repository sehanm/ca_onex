<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AuditLogModel;

class AuditFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during normal execution.
     * However, when an abnormal state is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script execution will end and that
     * Response will be sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // We log after the request is processed to ensure we have the context
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Skip if this request was already logged manually in the controller
        if (isset($GLOBALS['MANUAL_AUDIT_LOGGED']) && $GLOBALS['MANUAL_AUDIT_LOGGED']) {
            return;
        }

        $session = session();

        // Only log if user is logged in
        if (!$session->has('id')) {
            return;
        }

        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $excludedExts = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'otf', 'map'];

        // Avoid logging the audit log page itself, static files, or debug tools
        if (
            strpos($path, 'admin/audit-logs') !== false ||
            strpos($path, 'assets/') !== false ||
            strpos($path, 'debugbar') !== false ||
            in_array(strtolower($ext), $excludedExts)
        ) {
            return;
        }

        // We primarily want to log state-changing actions (POST, PUT, DELETE)
        // but the user asked for "ALL activities". So we'll log everything.
        // To avoid bloat, we might filter out simple GET requests for views,
        // but "ALL" means "ALL".

        $action = "Accessed: " . $path;
        if ($method !== 'GET') {
            $action = "Action: " . $method . " on " . $path;
        }

        $details = null;
        if ($method !== 'GET') {
            $postData = [];
            if ($request instanceof \CodeIgniter\HTTP\IncomingRequest) {
                $postData = $request->getPost();
            }
            // Mask sensitive data
            if (isset($postData['password']))
                $postData['password'] = '********';
            if (isset($postData['confirm_password']))
                $postData['confirm_password'] = '********';
            if (isset($postData['new_password']))
                $postData['new_password'] = '********';

            $details = json_encode($postData);
        }

        $auditModel = new AuditLogModel();

        // Use a try-catch to prevent application failure if audit DB is down
        try {
            $auditModel->log($action, $details);
        } catch (\Exception $e) {
            log_message('error', 'Failed to log audit: ' . $e->getMessage());
        }
    }
}
