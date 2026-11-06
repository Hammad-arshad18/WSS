<?php

declare(strict_types=1);

$id = (int) ($params['id'] ?? 0);
$resource = resource_find($id);

if ($resource === null || (!(int) $resource['is_published'] && !is_admin())) {
    abort(404, 'That document is not available.');
}

$user = current_user();
$allowedCategories = role_resource_categories((string) ($user['role'] ?? 'partner'));

if ($allowedCategories !== null && !in_array((string) $resource['category'], $allowedCategories, true)) {
    abort(404, 'That document is not available.');
}

$path = upload_path((string) $resource['stored_name']);

if ($path === null) {
    abort(404, 'That document could not be found.');
}

$allowedMimes = array_keys((array) config('security.allowed_uploads', []));
$mime = in_array((string) $resource['mime_type'], $allowedMimes, true)
    ? (string) $resource['mime_type']
    : 'application/octet-stream';

$original = (string) $resource['original_name'];
$fallback = preg_replace('/[^A-Za-z0-9._ -]/', '_', $original) ?: 'document';
$fallback = str_replace(['"', '\\'], '_', $fallback);

audit_record(
    auth_id(),
    'document.downloaded',
    'Downloaded resource #' . $id . ' (' . $original . ')'
);

if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $fallback . '"; filename*=UTF-8\'\'' . rawurlencode($original));
header('Content-Length: ' . (string) filesize($path));
header('Content-Transfer-Encoding: binary');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store, max-age=0');

readfile($path);
exit;
