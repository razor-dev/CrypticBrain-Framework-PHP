<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CrypticBrain::app()->charset; ?>" />
    <title>CrypticBrain Framework</title>
    <meta name="author" content="CrypticBrain">
    <base href="<?php echo CrypticBrain::app()->getRequest()->getBaseUrl(); ?>" />
    <link rel="shortcut icon" href="/" />
    <?php echo CHtml::cssFile('css/reset.css'); ?>
    <?php echo CHtml::cssFile('css/IcoMoon.css'); ?>
    <?php echo CHtml::cssFile('css/Opentip.css'); ?>
    <?php echo CHtml::scriptFile('js/vendors/Opentip.js'); ?>
</head>
<body>

<div id="page-layout">
    <div class="message-box">
        <div class="message-box-header"></div>
        <div class="message-box-content"></div>
        <div class="message-box-footer"></div>
    </div>
</div>
</body>
</html>