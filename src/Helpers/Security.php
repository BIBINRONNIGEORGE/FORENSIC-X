<?php
/**
 * FORENSIC-X Application Security & Defensive Encoding Helpers
 * Section 16 Web Application Security Implementation
 */

class Security {
    public static function sendSecurityHeaders(): void {
        header("X-Frame-Options: DENY");
        header("X-Content-Type-Options: nosniff");
        header("X-XSS-Protection: 1; mode=block");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; connect-src 'self'; font-src 'self'; frame-ancestors 'none';");
    }

    public static function e(?string $value): string {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    public static function sanitizeFilename(string $filename): string {
        $filename = basename($filename);
        // Remove non-alphanumeric characters except dot, dash, underscore
        $filename = preg_replace('/[^a-zA-Z0-9\._-]/', '_', $filename);
        return substr($filename, 0, 150);
    }

    public static function validateUpload(array $file): array {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['valid' => false, 'error' => 'Invalid file upload parameters.'];
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['valid' => false, 'error' => 'Uploaded file exceeds the maximum allowed file size.'];
            case UPLOAD_ERR_NO_FILE:
                return ['valid' => false, 'error' => 'No file was uploaded.'];
            default:
                return ['valid' => false, 'error' => 'An unknown upload error occurred.'];
        }

        if ($file['size'] > MAX_UPLOAD_SIZE) {
            return ['valid' => false, 'error' => 'File size exceeds maximum permitted limit (' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB).'];
        }

        $origName = $file['name'];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
            return [
                'valid' => false,
                'error' => 'Disallowed file extension (.' . htmlspecialchars($ext) . '). Allowed: ' . implode(', ', ALLOWED_EXTENSIONS)
            ];
        }

        // Additional MIME check using Fileinfo
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']) ?: 'application/octet-stream';

        // Disallow PHP / HTML executable MIME types under any circumstances
        $dangerousMimes = [
            'application/x-php',
            'text/x-php',
            'application/x-httpd-php',
            'application/x-httpd-php-source',
            'text/html',
            'application/xhtml+xml',
            'application/javascript',
            'text/javascript'
        ];

        if (in_array(strtolower($mime), $dangerousMimes, true) && $ext !== 'txt' && $ext !== 'log') {
            return ['valid' => false, 'error' => 'Potential active executable content detected. File upload rejected.'];
        }

        return [
            'valid'         => true,
            'extension'     => $ext,
            'mime'          => $mime,
            'size'          => $file['size'],
            'orig_name'     => self::sanitizeFilename($origName),
            'storage_name'  => 'fx_evd_' . bin2hex(random_bytes(16)) . '.' . $ext
        ];
    }
}
