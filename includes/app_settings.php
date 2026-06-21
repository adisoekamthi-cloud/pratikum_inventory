<?php
function app_settings_root_path(string $path = ''): string
{
    $root = dirname(__DIR__);
    return $path === '' ? $root : $root . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
}

function app_settings_defaults(): array
{
    return [
        'app_name' => 'Sakura Mobilindo',
        'company_logo' => 'assets/img/gambar.png',
        'favicon' => 'assets/img/gambar.png',
        'sidebar_header_image' => 'assets/img/AdminLTELogo.png',
    ];
}

function app_settings_file_path(): string
{
    return app_settings_root_path('config/app_settings.json');
}

function app_settings_load(): array
{
    $settings = app_settings_defaults();
    $file = app_settings_file_path();

    if (is_file($file)) {
        $stored = json_decode((string) file_get_contents($file), true);
        if (is_array($stored)) {
            $settings = array_merge($settings, $stored);
        }
    }

    return $settings;
}

function app_settings_save(array $settings): bool
{
    $settings = array_merge(app_settings_defaults(), $settings);
    $dir = dirname(app_settings_file_path());

    if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
        return false;
    }

    return file_put_contents(app_settings_file_path(), json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
}

function app_settings_asset_url(array $settings, string $key, string $prefix = ''): string
{
    $defaults = app_settings_defaults();
    $path = $settings[$key] ?? $defaults[$key] ?? '';

    return $prefix . ltrim($path, '/\\');
}

function app_settings_handle_upload(string $field, string $key, array &$settings, array &$errors, string $label): void
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return;
    }

    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "$label gagal diupload.";
        return;
    }

    $maxSize = 2 * 1024 * 1024;
    if ($_FILES[$field]['size'] > $maxSize) {
        $errors[] = "$label maksimal 2MB.";
        return;
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/x-icon' => 'ico',
        'image/vnd.microsoft.icon' => 'ico',
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $_FILES[$field]['tmp_name']) : '';
    if ($finfo) {
        finfo_close($finfo);
    }

    if (!isset($allowedTypes[$mime])) {
        $errors[] = "$label harus berupa JPG, PNG, GIF, WebP, atau ICO.";
        return;
    }

    $uploadDir = app_settings_root_path('assets/img/settings');
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
        $errors[] = "Folder upload tidak bisa dibuat.";
        return;
    }

    $fileName = $key . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $allowedTypes[$mime];
    $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $targetPath)) {
        $errors[] = "$label gagal disimpan.";
        return;
    }

    $oldPath = $settings[$key] ?? '';
    $settings[$key] = 'assets/img/settings/' . $fileName;

    if (str_starts_with($oldPath, 'assets/img/settings/')) {
        $oldFullPath = app_settings_root_path($oldPath);
        if (is_file($oldFullPath)) {
            unlink($oldFullPath);
        }
    }
}

function app_settings_handle_submit(array &$settings): array
{
    $message = '';
    $messageType = '';

    if (!isset($_POST['update_app_settings'])) {
        return [$message, $messageType];
    }

    $errors = [];
    $appName = trim($_POST['app_name'] ?? '');
    if ($appName === '') {
        $errors[] = 'Nama aplikasi wajib diisi.';
    } elseif (strlen($appName) > 80) {
        $errors[] = 'Nama aplikasi maksimal 80 karakter.';
    } else {
        $settings['app_name'] = $appName;
    }

    app_settings_handle_upload('company_logo', 'company_logo', $settings, $errors, 'Logo perusahaan');
    app_settings_handle_upload('favicon', 'favicon', $settings, $errors, 'Icon tab browser');
    app_settings_handle_upload('sidebar_header_image', 'sidebar_header_image', $settings, $errors, 'Gambar header sidebar');

    if ($errors) {
        return [implode('<br>', array_map('htmlspecialchars', $errors)), 'danger'];
    }

    if (!app_settings_save($settings)) {
        return ['Setting gagal disimpan. Pastikan folder config bisa ditulis.', 'danger'];
    }

    return ['Setting aplikasi berhasil disimpan.', 'success'];
}
