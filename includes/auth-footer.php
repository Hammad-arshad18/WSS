        </main>

        <footer class="auth-footer">
            <p>Access is by invitation. To request a partner, supplier or customer account, <a href="/contact">contact our team</a>.</p>
            <p class="auth-footer__legal">© <?= date('Y') ?> White Sea Star General Trading L.L.C · <a href="/privacy">Privacy</a></p>
        </footer>
    </section>
</div>

<?php require __DIR__ . '/flash.php'; ?>
<script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
<?php clear_form_state(); ?>
