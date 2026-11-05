<?php

$messages = flash_messages();
?>
<?php if ($messages !== []): ?>
    <div class="flash-stack" aria-live="polite" aria-atomic="true">
        <?php foreach ($messages as $message): ?>
            <?php
            $type = in_array($message['type'], ['success', 'error', 'info'], true) ? $message['type'] : 'info';
            $iconName = $type === 'success' ? 'check' : ($type === 'error' ? 'alert' : 'spark');
            ?>
            <div class="flash flash--<?= e($type) ?>" role="<?= $type === 'error' ? 'alert' : 'status' ?>" data-flash>
                <span class="flash__icon"><?= icon($iconName) ?></span>
                <p><?= e($message['message']) ?></p>
                <button type="button" class="flash__close" aria-label="Dismiss message" data-flash-close>
                    <?= icon('close') ?>
                </button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
