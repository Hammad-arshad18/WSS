<?php

declare(strict_types=1);

csrf_verify();

$id = (int) ($params['id'] ?? 0);
$resource = resource_find($id);

if ($resource === null) {
    abort(404, 'That document could not be found.');
}

resource_update($id, [
    'title' => $resource['title'],
    'description' => $resource['description'],
    'category' => $resource['category'],
    'is_published' => !(int) $resource['is_published'],
]);

audit_record(
    auth_id(),
    'document.publish_toggled',
    ((int) $resource['is_published'] ? 'Unpublished' : 'Published') . ' resource #' . $id
);

flash('success', (int) $resource['is_published'] ? 'Document moved to drafts.' : 'Document published to the portal.');
redirect('/admin/documents');
