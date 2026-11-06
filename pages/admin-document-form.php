<?php

declare(strict_types=1);

$user = current_user();
$categories = (array) content('resource_categories', []);
$resource = null;
$errors = [];
$title = '';
$description = '';
$category = '';

$validateResource = static function (string $title, string $description, string $category) use ($categories): array {
    return validate(
        ['title' => $title, 'description' => $description, 'category' => $category],
        [
            'title' => 'required|min:2|max:180',
            'description' => 'max:500',
            'category' => 'required|in:' . implode(',', $categories),
        ]
    );
};

if (is_post() && $action === 'store_document') {
    csrf_verify();

    $title = input_string('title');
    $description = input_string('description');
    $category = input_string('category');
    $published = ($_POST['is_published'] ?? '0') === '1';

    $errors = $validateResource($title, $description, $category);

    if ($errors === []) {
        try {
            $file = upload_store($_FILES['document'] ?? []);
        } catch (HttpError $e) {
            $errors['document'] = $e->getMessage();
            flash('error', $e->getMessage());
        }
    }

    if ($errors !== []) {
        flash_errors($errors);
        flash_input([
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'is_published' => $published ? '1' : '0',
        ]);

        if (!isset($errors['document'])) {
            flash('error', 'Please review the document details.');
        }

        redirect('/admin/documents/create');
    }

    try {
        $id = resource_create([
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'stored_name' => $file['stored_name'],
            'original_name' => $file['original_name'],
            'mime_type' => $file['mime_type'],
            'file_size' => $file['file_size'],
            'is_published' => $published,
        ], auth_id());
    } catch (Throwable $e) {
        upload_delete($file['stored_name']);
        throw $e;
    }

    audit_record(auth_id(), 'document.created', 'Uploaded resource #' . $id . ' (' . $title . ')');

    flash('success', 'Document uploaded and saved as ' . ($published ? 'published' : 'a draft') . '.');
    redirect('/admin/documents');
}

if (is_post() && $action === 'update_document') {
    csrf_verify();

    $id = (int) ($params['id'] ?? 0);
    $existing = resource_find($id);

    if ($existing === null) {
        abort(404, 'That document could not be found.');
    }

    $title = input_string('title');
    $description = input_string('description');
    $category = input_string('category');
    $published = ($_POST['is_published'] ?? '0') === '1';

    $errors = $validateResource($title, $description, $category);

    if ($errors !== []) {
        flash_errors($errors);
        flash_input([
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'is_published' => $published ? '1' : '0',
        ]);
        flash('error', 'Please review the document details.');
        redirect('/admin/documents/' . $id . '/edit');
    }

    resource_update($id, [
        'title' => $title,
        'description' => $description,
        'category' => $category,
        'is_published' => $published,
    ]);

    $newFile = null;

    if (isset($_FILES['document']) && (int) ($_FILES['document']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        try {
            $newFile = upload_store($_FILES['document']);
            resource_replace_file($id, $newFile);
            upload_delete((string) $existing['stored_name']);
        } catch (HttpError $e) {
            flash_errors(['document' => $e->getMessage()]);
            flash('error', $e->getMessage());
            redirect('/admin/documents/' . $id . '/edit');
        } catch (Throwable $e) {
            if ($newFile !== null) {
                upload_delete($newFile['stored_name']);
            }
            throw $e;
        }
    }

    audit_record(
        auth_id(),
        'document.updated',
        'Updated resource #' . $id . ' (' . $title . ')' . ($newFile !== null ? ' with a new file' : '')
    );

    flash('success', 'Document details saved.');
    redirect('/admin/documents');
}

if (isset($params['id'])) {
    $resource = resource_find((int) $params['id']);

    if ($resource === null) {
        abort(404, 'That document could not be found.');
    }
}

$isEdit = $resource !== null;
$actionUrl = $isEdit ? '/admin/documents/' . (int) $resource['id'] : '/admin/documents';
$publishedValue = old('is_published', $isEdit ? (string) (int) $resource['is_published'] : '1') === '1';

$title = $isEdit ? 'Edit document' : 'Upload document';
$metaDescription = $isEdit ? 'Update a portal document.' : 'Publish a portal document.';
$bodyClass = 'page-panel';

require includes_path('panel-header.php');
?>
<a class="back-link back-link--panel" href="/admin/documents"><?= icon('arrow') ?> Back to documents</a>

<div class="panel-head">
    <div>
        <p class="eyebrow eyebrow--dark"><?= $isEdit ? 'Edit resource' : 'New resource' ?></p>
        <h1 class="panel-title"><?= $isEdit ? 'Edit document.' : 'Upload a document.' ?></h1>
        <p class="panel-subtitle"><?= $isEdit ? 'Update the details or replace the file.' : 'Add a file to the client resource library.' ?></p>
    </div>
</div>

<div class="form-layout">
    <div class="form-layout__main">
        <form class="panel-card form" method="post" action="<?= e($actionUrl) ?>" enctype="multipart/form-data" novalidate>
            <?= csrf_field() ?>

            <div class="panel-card__head">
                <div>
                    <p class="eyebrow eyebrow--dark">Details</p>
                    <h2>Document information</h2>
                </div>
            </div>

            <div class="field<?= form_error('title') !== '' ? ' has-error' : '' ?>">
                <label for="title">Document title</label>
                <input id="title" name="title" type="text" value="<?= e(old('title', $resource['title'] ?? '')) ?>" required maxlength="180" placeholder="e.g. 2026 Service Overview">
                <?php if (form_error('title') !== ''): ?><p class="field__error"><?= e(form_error('title')) ?></p><?php endif; ?>
            </div>

            <div class="field<?= form_error('category') !== '' ? ' has-error' : '' ?>">
                <label for="category">Category</label>
                <div class="select-wrap">
                    <select id="category" name="category" required>
                        <option value="">Choose a category</option>
                        <?php foreach ($categories as $categoryOption): ?>
                            <option value="<?= e($categoryOption) ?>"<?= selected(old('category', $resource['category'] ?? ''), $categoryOption) ?>><?= e($categoryOption) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= icon('chevron') ?>
                </div>
                <?php if (form_error('category') !== ''): ?><p class="field__error"><?= e(form_error('category')) ?></p><?php endif; ?>
            </div>

            <div class="field<?= form_error('description') !== '' ? ' has-error' : '' ?>">
                <label for="description">Short description</label>
                <textarea id="description" name="description" rows="4" maxlength="500" placeholder="A short note to help account holders understand what this document contains."><?= e(old('description', $resource['description'] ?? '')) ?></textarea>
                <?php if (form_error('description') !== ''): ?><p class="field__error"><?= e(form_error('description')) ?></p><?php endif; ?>
            </div>

            <div class="form-divider"></div>

            <div class="panel-card__head">
                <div>
                    <p class="eyebrow eyebrow--dark">File</p>
                    <h2><?= $isEdit ? 'Replace the file' : 'Attach the file' ?></h2>
                </div>
                <?php if ($isEdit): ?><span class="badge badge--soft">Optional</span><?php endif; ?>
            </div>

            <?php if ($isEdit): ?>
                <div class="current-file">
                    <span class="doc-icon doc-icon--<?= e(file_kind($resource['mime_type'])) ?>"><?= icon(file_kind($resource['mime_type']) === 'sheet' ? 'sheet' : 'file') ?></span>
                    <span class="current-file__body">
                        <strong><?= e($resource['original_name']) ?></strong>
                        <small><?= e(human_filesize($resource['file_size'])) ?> · Uploaded <?= e(format_date($resource['created_at'])) ?></small>
                    </span>
                    <span class="badge badge--muted">Current file</span>
                </div>
                <p class="field__help">Uploading a new file replaces the current one. Leave empty to keep it.</p>
            <?php endif; ?>

            <div class="field<?= form_error('document') !== '' ? ' has-error' : '' ?>">
                <label for="document"><?= $isEdit ? 'New file' : 'File' ?></label>
                <label class="file-drop" for="document" data-file-drop>
                    <span class="file-drop__icon"><?= icon('download') ?></span>
                    <span class="file-drop__body">
                        <strong data-file-name><?= $isEdit ? 'Choose a replacement file' : 'Choose a file or drag it here' ?></strong>
                        <small>PDF, JPG, PNG, DOCX or XLSX · up to <?= e(human_filesize(config('security.upload_max_bytes'))) ?></small>
                    </span>
                    <input id="document" name="document" type="file" accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx" <?= $isEdit ? '' : 'required' ?> data-file-input>
                </label>
                <?php if (form_error('document') !== ''): ?><p class="field__error"><?= e(form_error('document')) ?></p><?php endif; ?>
            </div>

            <div class="toggle-field">
                <label class="switch">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1"<?= checked($publishedValue) ?>>
                    <span class="switch__track" aria-hidden="true"></span>
                    <span class="switch__body">
                        <strong>Publish to portal</strong>
                        <small>When off, the document is saved as a draft and is not visible in the client portal.</small>
                    </span>
                </label>
            </div>

            <div class="form__divider"></div>

            <div class="form__actions">
                <button class="btn btn--dark" type="submit"><?= $isEdit ? 'Save document' : 'Upload document' ?> <?= icon('check') ?></button>
                <a class="btn btn--ghost" href="/admin/documents">Cancel</a>
            </div>
        </form>
    </div>

    <aside class="form-layout__side">
        <?php if ($isEdit): ?>
            <section class="panel-card">
                <div class="panel-card__head">
                    <div>
                        <p class="eyebrow eyebrow--dark">Record</p>
                        <h2>File details</h2>
                    </div>
                </div>
                <dl class="meta-list">
                    <div><dt>Category</dt><dd><?= e($resource['category']) ?></dd></div>
                    <div><dt>Size</dt><dd><?= e(human_filesize($resource['file_size'])) ?></dd></div>
                    <div><dt>Uploaded</dt><dd><?= e(format_date($resource['created_at'])) ?></dd></div>
                    <div><dt>Last updated</dt><dd><?= e(format_date($resource['updated_at'] ?? $resource['created_at'])) ?></dd></div>
                    <div><dt>Status</dt><dd><span class="status-badge <?= (int) $resource['is_published'] ? 'status-badge--active' : 'status-badge--draft' ?>"><?= (int) $resource['is_published'] ? 'Published' : 'Draft' ?></span></dd></div>
                </dl>
            </section>

            <section class="panel-card panel-card--note">
                <span class="panel-note__icon"><?= icon('shield') ?></span>
                <div>
                    <h2>File safety</h2>
                    <p>Uploads are checked by MIME type and file signature, renamed randomly and stored outside the public web root.</p>
                </div>
            </section>
        <?php else: ?>
            <section class="panel-card panel-card--note">
                <span class="panel-note__icon"><?= icon('folder') ?></span>
                <div>
                    <h2>Keep it useful</h2>
                    <p>Use a clear title and category so partners can find the right document quickly. Add a short description where the contents need context.</p>
                </div>
            </section>

            <section class="panel-card panel-card--note">
                <span class="panel-note__icon"><?= icon('lock') ?></span>
                <div>
                    <h2>Publish with intent</h2>
                    <p>Only published documents are visible to account holders. Save as a draft if the file still needs review.</p>
                </div>
            </section>
        <?php endif; ?>
    </aside>
</div>
<?php require includes_path('panel-footer.php');
