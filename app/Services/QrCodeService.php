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
     * Generate QR code and save to temporary file for email attachment
     * This is the recommended approach for email compatibility
     */
    public static function generateQrCodeFile($token, $size = 250)
    {
        $qrCode = self::generateQrCodeForEmail($token, $size);
        $tempPath = storage_path('app/temp/qr_' . Str::random(10) . '.png');

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        file_put_contents($tempPath, $qrCode);

        return $tempPath;
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
                'message' => $e->getMessage()
            ];
        }
    }
}
