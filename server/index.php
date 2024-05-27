<?php

require("../stormmore.php");

use authentication\AuthenticationCookie;
use authentication\StormUser;
use infrastructure\settings\Settings;
use infrastructure\Database;

$app = create_storm_app('../src');

$app->addConfiguration(function(AppConfiguration $configuration, Di $di)
{
    $settings = new Settings();
    SettingsLoader::load($settings, '@/settings.json');
    SettingsLoader::LoadIfExist($settings, "@/settings.$configuration->environment.json");
    $database = new Database($settings->database->getConnection());
    $di->register($settings);
    $di->register($database);

    $configuration->baseUrl = $settings->url;
    $configuration->unauthorizedRedirect = "/signin";
    $configuration->unauthenticatedRedirect = "/signin";
    $configuration->errorPages = [
        500 => '@/templates/500.php',
        404 => '@/templates/404.php'];
    $configuration->aliases = [
        '@vendor' => '../vendor',
        '@backend' => "backend/templates",
        '@backend-layout' => 'backend/templates/layout.php',
        '@media' => "../server/media",
        '@profile' => "../server/media/profile",
        '@frontend' => "../server/themes/$settings->theme",
    ];
    $configuration->cacheEnabled = true;
    $configuration->cacheDir = '../.cache';
    $configuration->viewAddons = "../server/themes/$settings->theme/addons.php";
});

$app->addI18n(function(Request $request, Settings $settings, I18n $i18n) {
    $language = $settings->getApplicationLanguage($request->getAcceptedLanguages());
    $i18n->loadLangFile("@/translations/lang/$language->primary.json");
    $i18n->loadLocalFile("@/translations/local/$language->local.json");
});

$app->addIdentityUser(function(SessionStorage       $sessionStore,
                               Request              $request,
                               Settings             $settings,
                               Di                   $di,
                               AuthenticationCookie $authenticationCookie)
{
    $user = new StormUser();
    $user->language = $settings->getUserLanguage($request->getAcceptedLanguages());

    if (!$authenticationCookie->has()) return $user;

    $now = new DateTime();
    $sessionId = $authenticationCookie->get();
    $session = $sessionStore->load($sessionId);

    if (!$session or $session->valid_to < $now) {
        $authenticationCookie->delete();
        return $user;
    }

    if ($session->remember) {
        $validTo = $now->modify($settings->session->rememberSessionLifeTime);
    } else {
        $validTo = $now->modify($settings->session->sessionLifeTime);
    }
    $sessionStore->update($sessionId, $validTo);

    $user->authenticate();
    $user->id = $session->user_id;
    $user->name = $session->name;
    $user->role = $session->role;
    $user->email = $session->email;

    return $user;
});

$app->beforeRun(function(StormUser $user, Request $request, Database $database)
{
    $database->begin();

    $isAdminUri = str_starts_with($request->uri, "/admin");
    $canEnterAdmin = $user->canEnterPanel();
    if ($isAdminUri and !$canEnterAdmin) {
        return redirect('/signin');
    }
});
$app->onSuccess(function(Database $database) {$database->commit();});
$app->onFailure(function (Database $database) {$database->rollback();});
$app->addRoute("/php", function() { phpinfo(); });
$app->addRoute("/hello/:name", function($request) {
    echo "Hello " . $request->name;
});

$app->run();