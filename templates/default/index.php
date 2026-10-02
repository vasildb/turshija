<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <meta name="description" content="<?= htmlspecialchars($website->getProp('meta-description') ?? '') ?>" />
    <meta content="<?= htmlspecialchars($website->getProp('meta-keywords') ?? '') ?>" name="keywords" />

    <link href="/assets/css/style.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="<?= htmlspecialchars($website->getProp('favicon') ?? '') ?>" />
    <title><?= htmlspecialchars($title) ?></title>
</head>

<body>
    <?= $header ?>
    <?= $content ?>
    <?= $footer ?>
</body>

</html>