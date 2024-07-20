<?php

namespace App\Http\Controllers\Traits;

use GuzzleHttp\Exception\RequestException;

trait WhatsappTrait
{
    function sendWhatsapp($nohp, $subject, $teks, $tekstengah)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $endpoint = 'http://localhost:3000/send-message';

            // Remove common symbols
            $tujuan = str_replace(['-', ' ', '+'], '', $nohp);

            // If the tujuan starts with '62', replace it with '0'
            if (substr($tujuan, 0, 2) == '62') {
                $tujuan = '0' . substr($tujuan, 2);
            }

            $response = $client->request('POST', $endpoint, ['form_params' => [
                'number' => $tujuan,
                'message' => "*" . $subject . "*\n\n" . $teks . "\n" . $tekstengah,
            ]]);

            // return $response->getStatusCode();
            return true;
        } catch (RequestException $e) {
            return false;
        }
    }

    function sendWhatsappAttachment($nohp, $subject, $teks, $tekstengah, $media)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $endpoint = 'http://localhost:3000/send-message';

            // Remove common symbols
            $tujuan = str_replace(['-', ' ', '+'], '', $nohp);

            // If the tujuan starts with '62', replace it with '0'
            if (substr($tujuan, 0, 2) == '62') {
                $tujuan = '0' . substr($tujuan, 2);
            }

            $response = $client->request('POST', $endpoint, [
                'multipart' => [
                    [
                        'name'     => 'number',
                        'contents' => $tujuan
                    ],
                    [
                        'name'     => 'message',
                        'contents' => "*" . $subject . "*\n\n" . $teks . "\n" . $tekstengah
                    ],
                    [
                        'name'     => 'file_dikirim',
                        'contents' => fopen($media, 'r'),
                        'filename' => basename($media)
                    ],
                ]
            ]);

            // return $response->getStatusCode();
            return true;
        } catch (RequestException $e) {
            return false;
        }
    }
}
