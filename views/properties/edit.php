<section class="container section narrow">
    <h1>Editar propiedad</h1>
    <?php $action = BASE_URL . '/propiedades/' . (int) $property['id']; ?>
    <?php require VIEWS_DIR . '/properties/_form.php'; ?>
</section>
