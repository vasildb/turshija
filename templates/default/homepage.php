<div class="homepage">
    <ul>
        <?php foreach ($posts as $p) { ?>
            <li>
                <a href="<?= $p->getUrl(); ?>"><small>[<?= $p->getDate()->format('d-m-Y') ?>]</small><?= $p->getProp('title'); ?></a>
            </li>
        <?php } ?>
    </ul>
</div>