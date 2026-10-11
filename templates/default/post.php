<div class="blog-post">
    <h1 class="section-title"><?= htmlspecialchars($title ?? '') ?></h1>
    <div class="section-line"></div>
    <?= $post ?>
    <?php if (!empty($photos)) { ?>
        <div class="photos">
            <?php foreach ($photos as $src) { ?>
                <a href="<?= htmlspecialchars($src) ?>"><img src="<?= htmlspecialchars($src) ?>" alt="" loading="lazy" /></a>
            <?php } ?>
        </div>
    <?php } ?>
</div>