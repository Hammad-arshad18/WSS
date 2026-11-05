<?php

declare(strict_types=1);

csrf_verify();

$id = (int) ($params['id'] ?? 0);
$enquiry = enquiry_find($id);

if ($enquiry === null) {
    abort(404, 'That enquiry could not be found.');
}

enquiry_toggle_handled($id);
audit_record(auth_id(), 'enquiry.status_changed', 'Toggled enquiry #' . $id . ' status');

flash('success', 'Enquiry status updated.');
redirect('/admin/enquiries');
