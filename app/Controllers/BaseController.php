<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
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
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    protected function logAction($action, $details = null)
    {
        $auditModel = new \App\Models\AuditLogModel();
        $auditModel->log($action, $details);
    }

    protected function sendUserCreationEmail($emailAddress, $fullName, $username, $password)
    {
        $email = \Config\Services::email();
        $email->setTo($emailAddress);
        $email->setSubject('Account Created - CA ONEX');

        $message = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 10px;'>
                <h2 style='color: #800000;'>Welcome to CA ONEX, $fullName!</h2>
                <p>An account has been created for you in the CA ONEX system.</p>
                <div style='background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                    <p style='margin: 0;'><strong>Username:</strong> $username</p>
                    <p style='margin: 10px 0 0 0;'><strong>Password:</strong> $password</p>
                    <p style='margin: 15px 0 0 0; font-size: 0.9rem; color: #6b7280;'><em>Please change your password after logging in for security.</em></p>
                </div>
                <p>Please log in to your dashboard to get started.</p>
                <a href='" . base_url() . "' style='display: inline-block; background-color: #800000; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 10px;'>Go to Dashboard</a>
                <hr style='border: 0; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                <p style='color: #6b7280; font-size: 0.85rem;'>This is an automated message, please do not reply.</p>
                <p style='color: #6b7280; font-size: 0.85rem;'>2026&copy;CA OnEx System. All Rights Reserved!<br>Designed and Developed by ICT Division CA Sri Lanka</p>
            </div>
        ";

        $email->setMessage($message);
        return $email->send();
    }

    protected function sendOnboardingInitiateEmail($recipient, $ccs, $candidateData, $initiatorName)
    {
        $email = \Config\Services::email();
        $email->setTo($recipient);
        if (!empty($ccs)) {
            $email->setCC($ccs);
        }
        $email->setSubject('New Onboarding Request - ' . $candidateData['candidate_name']);

        $message = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 10px;'>
                <div style='text-align: center; margin-bottom: 20px;'>
                    <h2 style='color: #800000; margin: 0;'>Onboarding Initiation</h2>
                    <p style='color: #6b7280; font-size: 0.9rem;'>CA ONEX System</p>
                </div>
                
                <p>Hello,</p>
                <p>A new onboarding request has been initiated by <strong>$initiatorName</strong> and is pending your approval.</p>
                
                <div style='background-color: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #f3f4f6;'>
                    <h3 style='margin-top: 0; color: #111827; font-size: 1.1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px;'>Candidate Details</h3>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 8px 0; color: #6b7280; width: 40%;'>Candidate Name:</td>
                            <td style='padding: 8px 0; color: #111827; font-weight: 600;'>{$candidateData['candidate_name']}</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; color: #6b7280;'>Designation:</td>
                            <td style='padding: 8px 0; color: #111827;'>{$candidateData['designation']}</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; color: #6b7280;'>Department:</td>
                            <td style='padding: 8px 0; color: #111827;'>{$candidateData['department_name']}</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; color: #6b7280;'>Expected Joining:</td>
                            <td style='padding: 8px 0; color: #111827;'>{$candidateData['joining_date']}</td>
                        </tr>
                    </table>
                </div>
                
                <p>Please log in to the system to review and approve this request.</p>
                
                <div style='text-align: center; margin-top: 30px;'>
                    <a href='" . base_url('onboarding/pending') . "' style='display: inline-block; background-color: #800000; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold;'>View Request</a>
                </div>
                
                <hr style='border: 0; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                <p style='color: #9ca3af; font-size: 0.8rem; text-align: center;'>2026 &copy; CA OnEx System. All Rights Reserved!<br>Designed and Developed by ICT Division CA Sri Lanka</p>
            </div>
        ";

        $email->setMessage($message);
        return $email->send();
    }
}
