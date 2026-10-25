<?php
/**
 * FORENSIC-X View Renderer and Presentation Helpers
 */

class View {
    public static function render(string $viewPath, array $data = [], string $layout = 'main'): void {
        extract($data);

        // Flash messages
        $flash = self::getFlash();

        // Render inner view
        ob_start();
        $fullViewFile = BASE_PATH . '/views/' . ltrim($viewPath, '/') . '.php';
        if (!file_exists($fullViewFile)) {
            die("View template not found: " . htmlspecialchars($viewPath));
        }
        require $fullViewFile;
        $content = ob_get_clean();

        // If no layout requested (e.g. for pure partial or print)
        if ($layout === 'none') {
            echo $content;
            return;
        }

        // Render surrounding layout
        $layoutFile = BASE_PATH . '/views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    public static function setFlash(string $type, string $message): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'] = [
            'type'    => $type, // 'success', 'danger', 'warning', 'info'
            'message' => $message
        ];
    }

    public static function getFlash(): ?array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    public static function formatBytes(int $bytes, int $precision = 2): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public static function severityBadge(string $severity): string {
        $colors = [
            'Critical' => 'badge-critical',
            'High'     => 'badge-high',
            'Medium'   => 'badge-medium',
            'Low'      => 'badge-low',
        ];
        $class = $colors[$severity] ?? 'badge-medium';
        return '<span class="badge ' . $class . '"><i class="dot"></i>' . htmlspecialchars($severity) . '</span>';
    }

    public static function statusBadge(string $status): string {
        $colors = [
            'Open'          => 'badge-open',
            'Investigating' => 'badge-investigating',
            'Contained'     => 'badge-contained',
            'Resolved'      => 'badge-resolved',
            'Closed'        => 'badge-closed',
            'Verified'      => 'badge-verified',
            'Mismatch'      => 'badge-mismatch',
            'Unavailable'   => 'badge-unavailable',
            'In-Analysis'   => 'badge-investigating',
            'Acquired'      => 'badge-open',
            'Sealed'        => 'badge-contained',
        ];
        $class = $colors[$status] ?? 'badge-neutral';
        return '<span class="badge ' . $class . '">' . htmlspecialchars($status) . '</span>';
    }

    public static function roleBadge(string $role): string {
        $colors = [
            'admin'        => 'badge-role-admin',
            'investigator' => 'badge-role-investigator',
            'analyst'      => 'badge-role-analyst',
            'reviewer'     => 'badge-role-reviewer',
        ];
        $class = $colors[$role] ?? 'badge-neutral';
        return '<span class="badge ' . $class . '">' . strtoupper(htmlspecialchars($role)) . '</span>';
    }
}
