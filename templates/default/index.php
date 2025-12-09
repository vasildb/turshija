<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <meta name="description" content="<?= $website->getProp('meta-description') ?>" />
    <meta content="<?= $website->getProp('meta-keywords') ?>" name="keywords" />

    <link href="/assets/css/style.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="<?= $website->getProp('favicon') ?>" />
    <title><?= $title; ?></title>
</head>

<body>
    <?= $header ?>
    <?= $content ?>
    <?= $footer ?>
</body>

</html>