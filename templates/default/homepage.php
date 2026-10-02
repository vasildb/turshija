<div class="homepage">
    <ul>
        <?php foreach ($posts as $p) { ?>
            <li>
                <a href="<?= htmlspecialchars($p->getUrl()) ?>"><small>[<?= $p->getDate()->format('d-m-Y') ?>]</small><?= htmlspecialchars($p->getProp('title') ?? '') ?></a>
            </li>
        <?php } ?>
    </ul>
</div>