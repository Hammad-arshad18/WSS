<?php

declare(strict_types=1);

csrf_verify();

$id = (int) ($params['id'] ?? 0);
$resource = resource_find($id);

if ($resource === null) {
    abort(404, 'That document could not be found.');
}

resource_delete($id);
upload_delete((string) $resource['stored_name']);

audit_record(auth_id(), 'document.deleted', 'Deleted resource #' . $id . ' (' . $resource['title'] . ')');

flash('success', 'Document deleted.');
redirect('/admin/documents');
