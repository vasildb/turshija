<div class="homepage">
    <ul>
        <?php foreach ($posts as $p) { ?>
            <li>
                <a href="<?= htmlspecialchars($p->getUrl()) ?>"><small>[<?= htmlspecialchars($p->getDisplayDate()) ?>]</small><?= htmlspecialchars($p->getProp('title') ?? '') ?></a>
                <?php if ($p->getPhotos()) { ?>
                    <a class="post-photos" href="<?= htmlspecialchars($p->getUrl()) ?>">
                        <?php foreach ($p->getPhotos() as $name) { ?>
                            <img src="<?= htmlspecialchars('/assets/photos/' . rawurlencode($name)) ?>" alt="" loading="lazy" />
                        <?php } ?>
                    </a>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
    <?php if (!empty($photos)) { ?>
        <div class="photos">
            <?php foreach ($photos as $src) { ?>
                <a href="<?= htmlspecialchars($src) ?>"><img src="<?= htmlspecialchars($src) ?>" alt="" loading="lazy" /></a>
            <?php } ?>
        </div>
    <?php } ?>
</div>