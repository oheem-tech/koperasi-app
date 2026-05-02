<?php

namespace App\Libraries;

use App\Models\PengaturanModel;

class WaGateway
{
    protected $pengaturanModel;
    protected $token;

    public function __construct()
    {
        $this->pengaturanModel = new PengaturanModel();
        $tokenRow = $this->pengaturanModel->where('pengaturan_key', 'wa_token')->first();
        $this->token = $tokenRow ? $tokenRow['pengaturan_value'] : '';
    }

    /**
     * Parse phone number to international format (62...)
     */
    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) === '62') {
            // Already correct
        } else {
            $phone = '62' . $phone; // Assuming missing prefix
        }
        return $phone;
    }

    /**
     * Send message using Fonnte API
     */
    public function sendMessage($phone, $message)
    {
        // Fitur WA Gateway eksklusif untuk pengguna Lisensi PRO
        if (!function_exists('is_premium')) {
            helper('auth'); // Asumsi is_premium ada di auth_helper atau helper lain yang sesuai, jika tidak akan mengandalkan helper default yang sudah diload.
        }
        if (function_exists('is_premium') && !is_premium()) {
            log_message('error', 'WA Gateway dibatalkan: Aplikasi belum diupgrade ke versi PRO.');
            return false;
        }

        if (empty($this->token)) {
            log_message('error', 'WA Gateway Token is empty.');
            return false;
        }

        if (empty($phone)) {
            return false;
        }

        $formattedPhone = $this->formatPhoneNumber($phone);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5, // Set timeout to avoid blocking requests for too long
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $formattedPhone,
                'message' => $message,
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $this->token
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            log_message('error', 'WA Gateway cURL Error #: ' . $err);
            return false;
        } else {
            return json_decode($response, true);
        }
    }
}
