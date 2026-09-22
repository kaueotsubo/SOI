    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/common.js"></script>
    <?php foreach (($scriptsPagina ?? []) as $script): ?>
        <script src="<?= e($script) ?>"></script>
    <?php endforeach; ?>
</body>

</html>
