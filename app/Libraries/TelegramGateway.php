<?php

namespace App\Libraries;

use App\Models\PengaturanModel;

class TelegramGateway
{
    protected $pengaturanModel;
    protected $token;

    public function __construct()
    {
        $this->pengaturanModel = new PengaturanModel();
        $tokenRow = $this->pengaturanModel->where('pengaturan_key', 'telegram_token')->first();
        $this->token = $tokenRow ? $tokenRow['pengaturan_value'] : '';
    }

    /**
     * Send message using Telegram Bot API
     */
    public function sendMessage($chat_id, $message)
    {
        if (empty($this->token)) {
            log_message('error', 'Telegram Gateway Token is empty.');
            return false;
        }

        if (empty($chat_id)) {
            return false;
        }

        $url = "https://api.telegram.org/bot" . $this->token . "/sendMessage";
        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML' // Allows basic HTML formatting like <b>bold</b>, <i>italic</i>
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5, // Set timeout to avoid blocking requests
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            log_message('error', 'Telegram Gateway cURL Error #: ' . $err);
            return false;
        } else {
            return json_decode($response, true);
        }
    }
}

