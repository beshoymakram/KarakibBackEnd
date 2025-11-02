<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate QR code token for an order/request
     */
    public static function generateToken($model)
    {
        $data = [
            'id' => $model->id,
            'number' => $model->order_number ?? $model->request_number,
            'user_id' => $model->user_id,
            'type' => class_basename($model), // 'Order' or 'Request'
            'nonce' => Str::random(16)
        ];

        return Crypt::encryptString(json_encode($data));
    }

    /**
     * Generate QR code image as base64
     */
    public static function generateQrCode($token, $size = 300)
    {
        return base64_encode(
            QrCode::format('png')
                ->size($size)
                ->margin(2)
                ->errorCorrection('H')
                ->generate($token)
        );
    }

    /**
     * Get QR code as data URL for embedding
     */
    public static function getQrCodeDataUrl($token, $size = 300)
    {
        $qrImage = self::generateQrCode($token, $size);
        return 'data:image/png;base64,' . $qrImage;
    }

    /**
     * Generate QR code as raw PNG for email attachment
     */
    public static function generateQrCodeForEmail($token, $size = 250)
    {
        return QrCode::format('png')
            ->size($size)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($token);
    }

    /**
     * Verify and decode QR code token
     */
    public static function verifyToken($token)
    {
        try {
            $decrypted = Crypt::decryptString($token);
            $data = json_decode($decrypted, true);

            return [
                'valid' => true,
                'data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => $e
            ];
        }
    }
}
