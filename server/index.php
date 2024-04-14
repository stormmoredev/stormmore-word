<?php

require("../storm.php");

use authentication\AuthenticationCookie;
use authentication\StormUser;
use infrastructure\settings\Settings;
use infrastructure\Database;

$app = app('../src');

$app->addConfiguration(function(AppConfiguration $configuration,
                                Request $request,
                                Di $di)
{
    $settings = new Settings();
    SettingsLoader::load($settings, '@/settings.json');
    SettingsLoader::LoadIfExist($settings, "@/settings.$configuration->environment.json");
    $database = new Database($settings->database->getConnection());

    $language = $settings->getApplicationLanguage($request->getAcceptedLanguages());
    $i18n = $di->resolve(I18n::class);
    $i18n->loadLangFile("@/translations/lang/$language->primary.json");
    $i18n->loadLocalFile("@/translations/local/$language->local.json");

    $configuration->baseUrl = $settings->url;

    $configuration->errorPages = [
        500 => '@/templates/500.php',
        404 => '@/templates/404.php'];
    $configuration->aliases = [
        '@vendor' => '../vendor',
        '@backend' => "backend/templates",
        '@backend-layout' => 'backend/templates/layout.php',
        '@frontend' => "../server/themes/$settings->theme",
    ];
    $configuration->viewAddons = "../server/themes/$settings->theme/addons.php";

    $di->register($settings);
    $di->register($database);
});

$app->addIdentityUser(function(SessionStore $sessionStore,
                               Request $request,
                               Settings $settings,
                               Di $di,
                               AuthenticationCookie $authenticationCookie)
{
    $user = new StormUser();
    $user->language = $settings->getUserLanguage($request->getAcceptedLanguages());

    if (!$authenticationCookie->has()) return $user;

    $now = new DateTime();
    $sessionId = $authenticationCookie->get();
    $session = $sessionStore->load($sessionId);

    if (!$session or $session->valid_to < $now) {
        Cookies::delete('storm');
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

$app->beforeRun(function(StormUser $user, Request $request, Response $response)
{
    $isAdminUri = str_starts_with($request->uri, "/admin");
    $isAdminSignInUri = $request->uri == '/admin/signin';
    $canEnterAdmin = $user->canEnterPanel();

    if ($isAdminUri and !$canEnterAdmin and !$isAdminSignInUri) {
        $response->redirect('/admin/signin');
    }
});

$app->addRoute("/php", function() { phpinfo(); });
$app->addRoute("/hello/:name", function($request) {
    echo "Hello " . $request->name;
});

$app->run();