<?php

require("../stormmore.php");

$form = new stdClass();
$form->name = "Michał";

$app = create_storm_app();
$app->addConfiguration(function(AppConfiguration $configuration)
{
    $configuration->cacheDir = '../.cache';
});
$app->addRoute("/", function(Request $request) {
    echo $_SERVER['DOCUMENT_ROOT'];
    if ($request->isPost()) {
        rename('index.php', 'install.php');
        rename('app.php', 'index.php');
        return redirect();
    }
});
$app->run();
?>

<h1><?php echo $form->name ?></h1>
<form method="post">
    <input type="submit" value="save" />
</form>