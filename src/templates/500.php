<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>Storm CMS - 500</title>
    <link rel="icon" type="image/x-icon" href="/public/storm-cms.ico">
    <link href="themes/basic/public/main.css" rel="stylesheet">
</head>
<body>
<?php if(isset($e)): ?>
    <h1>Error <?php echo $e->getCode() ?: 500 ?></h1>
    <h2><?php echo $e->getMessage() ?></h2>
    <pre><?php echo $e->getTraceAsString() ?></pre>
    <?php debug_print_backtrace() ?>
<?php endif ?>
</body>
</html>
