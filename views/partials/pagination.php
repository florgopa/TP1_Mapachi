<?php
/**
 * Espera una variable $pagination = ['page' => int, 'totalPages' => int]
 * Reutiliza el resto de los parámetros GET actuales (filtros) al cambiar de página.
 */
if (($pagination['totalPages'] ?? 1) <= 1) {
    return;
}

$queryParams = $_GET;
?>
<div class="pagination">
    <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
        <?php $queryParams['page'] = $i; ?>
        <a href="?<?= http_build_query($queryParams) ?>"
           class="<?= $i === $pagination['page'] ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</div>
