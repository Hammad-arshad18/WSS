<?php

declare(strict_types=1);

/**
 * Private document uploads. Files are stored in storage/uploads and are only
 * served through the authenticated download page.
 */
function upload_store(array $file): array
{
    $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

    if ($error !== UPLOAD_ERR_OK) {
        throw new HttpError(422, match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The file is larger than the server allows.',
            UPLOAD_ERR_NO_FILE => 'Please choose a file to upload.',
            UPLOAD_ERR_PARTIAL => 'The upload did not complete. Please try again.',
            default => 'The file could not be uploaded.',
        });
    }

    $tmpPath = (string) ($file['tmp_name'] ?? '');
    $size = (int) ($file['size'] ?? 0);
    $maxBytes = (int) config('security.upload_max_bytes', 10 * 1024 * 1024);

    if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
        throw new HttpError(422, 'The upload could not be verified.');
    }

    if ($size <= 0 || $size > $maxBytes) {
        throw new HttpError(422, 'Files must be between 1 byte and ' . human_filesize($maxBytes) . '.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string) $finfo->file($tmpPath);
    $allowed = (array) config('security.allowed_uploads', []);

    if (!isset($allowed[$mime])) {
        throw new HttpError(422, 'Upload a PDF, JPG, PNG, DOCX or XLSX file.');
    }

    $extension = $allowed[$mime];

    if (!upload_signature_matches($tmpPath, $mime)) {
        throw new HttpError(422, 'The file contents do not match its type.');
    }

    $directory = storage_path('uploads');

    if (!is_dir($directory) && !@mkdir($directory, 0750, true) && !is_dir($directory)) {
        throw new HttpError(500, 'The storage directory is unavailable.');
    }

    $storedName = bin2hex(random_bytes(20)) . '.' . $extension;
    $destination = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $storedName;

    if (!move_uploaded_file($tmpPath, $destination)) {
        throw new HttpError(500, 'The file could not be saved. Please try again.');
    }

    @chmod($destination, 0640);

    return [
        'stored_name' => $storedName,
        'original_name' => safe_filename((string) ($file['name'] ?? 'document')),
        'mime_type' => $mime,
        'file_size' => $size,
    ];
}

function upload_delete(string $storedName): void
{
    $base = realpath(storage_path('uploads'));
    $path = realpath(storage_path('uploads') . DIRECTORY_SEPARATOR . basename($storedName));

    if ($base !== false && $path !== false && str_starts_with($path, $base . DIRECTORY_SEPARATOR) && is_file($path)) {
        @unlink($path);
    }
}

function upload_path(string $storedName): ?string
{
    $base = realpath(storage_path('uploads'));

    if ($base === false) {
        return null;
    }

    $path = realpath($base . DIRECTORY_SEPARATOR . basename($storedName));

    if ($path === false || !str_starts_with($path, $base . DIRECTORY_SEPARATOR) || !is_file($path)) {
        return null;
    }

    return $path;
}

function upload_signature_matches(string $path, string $mime): bool
{
    $handle = @fopen($path, 'rb');

    if ($handle === false) {
        return false;
    }

    $header = (string) fread($handle, 8);
    fclose($handle);

    return match ($mime) {
        'application/pdf' => str_starts_with($header, '%PDF-'),
        'image/jpeg' => str_starts_with($header, "\xFF\xD8\xFF"),
        'image/png' => str_starts_with($header, "\x89PNG\r\n\x1A\n"),
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => str_starts_with($header, 'PK'),
        default => false,
    };
}
