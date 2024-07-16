<?php

/** @noinspection PhpUnused */

use Random\Randomizer;

class FileNotFoundException extends Exception
{
}

class UnknownPathAliasException extends Exception
{
}

class DiResolveException extends Exception
{
}

class AjaxAuthenticationException extends Exception
{
}

class AuthenticationException extends Exception
{
}

class UnauthorizedException extends Exception
{
}

/**
 * @throws UnknownPathAliasException if path alias not found
 */
function resolve_path_alias(string $templatePath): string
{
    $appDirectory = App::getInstance()->configuration->directory;
    $aliases = App::getInstance()->configuration->aliases;
    if (str_starts_with($templatePath, "@/")) {
        return str_replace("@", $appDirectory, $templatePath);
    } else if (str_starts_with($templatePath, '@')) {
        $firstSeparator = strpos($templatePath, "/");
        if ($firstSeparator) {
            $alias = substr($templatePath, 0, $firstSeparator);
            $path = substr($templatePath, $firstSeparator);
        } else {
            $alias = $templatePath;
            $path = '';
        }

        array_key_exists($alias, $aliases) or throw new UnknownPathAliasException("Alias [$alias] doesn't exist");
        $templatePath = $appDirectory . "/" . $aliases[$alias] . $path;
    }

    return $templatePath;
}

function is_array_key_value_equal(array $array, string $key, mixed $value): bool
{
    return array_key_exists($key, $array) and $array[$key] == $value;
}

function array_key_value(array $array, string $key, mixed $default): mixed
{
    return array_key_exists($key, $array) ? $array[$key] : $default;
}

function split_file_name_and_ext(string $filename): array
{
    $lastDotPos = strrpos($filename, '.');
    if ($lastDotPos !== false and $lastDotPos > 0) {
        $name = substr($filename, 0, $lastDotPos);
        $ext = substr($filename, $lastDotPos + 1);
        return [$name, $ext];
    }
    return [$filename, ''];
}

function concatenate_paths(string ...$paths): string
{
    $path = '';
    for ($i = 0; $i < count($paths); $i++) {
        $element = $paths[$i];
        if ($i < count($paths) - 1 and !str_ends_with($element, "/")) {
            $element .= "/";
        }
        if (str_ends_with($path, "/") and str_starts_with($element, "/")) {
            $element = substr($element, 1);
        }
        $path .= $element;
    }
    return $path;
}

/**
 * @param int $length length with or without extension. Default 64. Optional.
 * @param string $extension file extension. Optional.
 * @param string $directory to check whether unique file exist or not. Optional
 * @return string generated unique file name
 */
function gen_unique_file_name(int $length = 64, string $extension = '', string $directory = ''): string
{
    $filename = '';
    if (!empty($extension)) {
        $length = $length - strlen($extension) - 1;
    }
    do {
        $randomizer = new Randomizer();
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $filename .= $characters[$randomizer->getInt(0, $charactersLength - 1)];
        }
        if (!empty($extension)) {
            $filename .= '.' . $extension;
        }
    } while (!empty($directory) and file_exists($directory . "/" . $filename));

    return $filename;
}

function none_empty_explode($delimiter, $string, $limit = PHP_INT_MAX): array
{
    if (str_starts_with($string, $delimiter)) {
        $string = substr($string, 1);
    }
    if (str_ends_with($string, $delimiter)) {
        $string = substr($string, 0, -1);
    }
    return explode($delimiter, $string, $limit);
}

function create_storm_app(string $appDirectory = ""): App
{
    return App::getInstance($appDirectory);
}

function di(string $key = null): mixed
{
    if ($key == null)
        return App::getInstance()->di;
    return App::getInstance()->di->$key;
}

function _(string $phrase, ...$args): string
{
    return _args($phrase, $args);
}

function _args(string $phrase, array $args): string
{
    $i18n = App::getInstance()->di->I18n;
    $translatedPhrase = $i18n->translate($phrase);
    if (count($args)) {
        return vsprintf($translatedPhrase, $args);
    }

    return $translatedPhrase;
}

/**
 * @throws Exception
 */
function import(string $file): void
{
    $file = resolve_path_alias($file);
    if (str_ends_with($file, "/*")) {
        $dir = str_replace("/*", "", $file);
        $files = scandir($dir);
        foreach ($files as $file) {
            if (str_ends_with($file, ".php")) {
                require_once($dir . "/" . $file);
            }
        }
    } else {
        $file = $file . ".php";
        file_exists($file) or throw new Exception("IMPORT: file [$file] doesn't exists");
        require_once($file);
    }
}

function url($path, $args = array()): string
{
    if (count($args)) {
        $query = http_build_query($args);
        if (!empty($query))
            $path = $path . "?" . $query;
    }
    $pos = strrpos($path, '.');
    if ($pos !== false and strlen($path) - $pos < 5) {
        return concatenate_paths(App::getInstance()->request->basePath, $path);
    }
    return concatenate_paths(App::getInstance()->request->baseUri, $path);
}

function back(string $url = "/"): Redirect
{
    if (array_key_exists('HTTP_REFERER', $_SERVER)) {
        return redirect($_SERVER['HTTP_REFERER']);
    }

    return redirect($url);
}

function redirect(string $url = "/", string $messageName = "", string $messageContent = null): Redirect
{
    if (!empty($messageName)) {
        RedirectMessage::add($messageName, $messageContent);
    }

    return new Redirect($url);
}

function isset_redirect_message($name): bool
{
    return RedirectMessage::isset($name);
}

function has_redirect_message($name): bool
{
    return RedirectMessage::has($name);
}

function get_redirect_message(string $name): string
{
    return RedirectMessage::get($name);
}

class Redirect
{
    public ?string $location = null;
    public ?string $body = null;

    public function __construct(string $url)
    {
        $baseUrl = App::getInstance()->configuration->baseUrl;
        if (str_starts_with($url, "http")) {
            $this->location = $url;
        } else if ($baseUrl != null and str_starts_with($baseUrl, 'http')) {
            $this->location = $baseUrl . $url;
        }
    }
}

function view($templateFileName, array|object $data = []): View
{
    return new View($templateFileName, $data);
}

function print_view($templateFileName, array|object $data = []): void
{
    $view = view($templateFileName, $data);
    echo $view->toHtml();
}

class View
{
    private array $bag = [];

    public function __construct(
        private readonly string       $fileName,
        private readonly array|object $data)
    {
    }

    public function __get($key)
    {
        return array_key_exists($key, $this->bag) ? $this->bag[$key] : null;
    }

    public function __set(string $name, $value): void
    {
        $this->bag[$name] = $value;
    }

    /**
     * @throws Exception
     */
    public function toHtml(): string
    {
        $app = App::getInstance();

        $templateFilePath = resolve_path_alias($this->fileName) . ".php";
        $cacheTemplateFileName = str_replace("../", "-", $templateFilePath);
        $cacheTemplateFileName = str_replace("./", "-", $cacheTemplateFileName);
        $cacheTemplateFileName = str_replace("/", "-", $cacheTemplateFileName);
        if (str_starts_with($cacheTemplateFileName, '-')) {
            $cacheTemplateFileName = substr($cacheTemplateFileName, 1);
        }
        $cacheDirectory = concatenate_paths($app->configuration->getCacheDirectory(), "/views/");
        $cachedTemplateFilePath = concatenate_paths($cacheDirectory, $cacheTemplateFileName);

        if ($app->configuration->isDevelopment() || !file_exists($templateFilePath)) {
            file_exists($templateFilePath) or throw new Exception("VIEW: [$templateFilePath] doesn't exist ");

            if (!is_dir($cacheDirectory)) {
                mkdir($cacheDirectory, 0755, true);
            }
            $compiler = new ViewCompiler($templateFilePath, $this->codeAssembler);
            $compiler->compileTo($cachedTemplateFilePath);
        }

        $data = $this->data;
        if (is_object($data)) {
            $data = get_object_vars($this->data);
        }
        if (is_array($data)) {
            foreach ($data as $name => $value) {
                $this->bag[$name] = $value;
            }
        }

        extract($this->bag, EXTR_OVERWRITE, 'wddx');

        ob_start();
        require $cachedTemplateFilePath;
        return ob_get_clean();
    }
}

interface IViewComponent
{
    function view(): View;
}

class ViewCompiler
{
    public string $file;

    function __construct(string $file)
    {
        $this->file = $file;
    }

    public function compileTo($destination): void
    {
        $content = $this->compile();
        file_put_contents($destination, $content);
    }

    /**
     * @throws FileNotFoundException if view/addon file not found
     * @throws UnknownPathAliasException
     */
    public function compile(): string
    {
        $content = file_get_contents($this->file);
        $content = $this->_compile($content);
        return $this->surround($content);
    }

    /**
     * @throws UnknownPathAliasException
     * @throws FileNotFoundException
     */
    private function surround($content): string
    {
        preg_match('/@layout\s(.*?)\s/i', $content, $matches);
        if (!count($matches)) return $content;

        $content = str_replace($matches[0], '', $content);
        $layoutFilePath = resolve_path_alias($matches[1]);
        file_exists($layoutFilePath) or throw new FileNotFoundException("VIEW: layout [$layoutFilePath] doesn't exist");

        $layoutCompiler = new ViewCompiler($layoutFilePath);
        $layoutContent = $layoutCompiler->compile();
        return preg_replace('/@template/i', $content, $layoutContent);
    }

    private function _compile($content): string
    {
        $content = preg_replace_callback('/{{ _ (.+?)}}/i', function ($matches) {
            $phrase = $matches[1];
            $phrase = trim($phrase);
            $args = "[]";
            if (!str_starts_with($phrase, '$')) {
                if (str_contains($phrase, "|")) {
                    $parts = explode("|", $phrase);
                    $phrase = $parts[0];
                    $args = none_empty_explode(" ", $parts[1]);
                    $args = implode(",", $args);
                }
                $phrase = '"' . $phrase . '"';
            }

            return "<?php echo _args($phrase, [$args]) ?>";
        }, $content);

        $content = preg_replace_callback('/{{\s*(.+?)\|\s*(.+?)}}/i', function ($matches) {
            $filterNodes = none_empty_explode(' ', trim($matches[2]), 2);
            $filterName = 'format_' . $filterNodes[0];
            $filterArguments = $matches[1];
            if (count($filterNodes) > 1) {
                for ($i = 1; $i < count($filterNodes); $i++) {
                    $arg = $filterNodes[$i];
                    if (is_numeric($arg))
                        $filterArguments .= ",$arg";
                    else
                        $filterArguments .= ",'$arg'";
                }
            }

            return "<?php echo  $filterName ($filterArguments) ?>";
        }, $content);

        $content = preg_replace_callback('/{{([\s\S]*?)}}/i', function ($matches) {
            return "<?php echo $matches[1] ?>";
        }, $content);

        $content = preg_replace('/@if\s*\((.*)\)/i', '<?php if($1) { ?>', $content);
        $content = preg_replace('/@elseif\s*\((.*)\)/i', '<?php } else if($1) { ?>', $content);
        $content = preg_replace('/@else/i', '<?php } else { ?>', $content);
        $content = preg_replace('/@end/i', '<?php  } ?>', $content);

        $content = preg_replace_callback('/@include\s*(.*)/i', function ($matches) {
            if (str_starts_with($matches[1], '@')) {
                $file = resolve_path_alias($matches[1]);
            } else {
                $file = dirname($this->file) . "/" . trim($matches[1]);
            }
            $file = trim($file);
            file_exists($file) or throw new FileNotFoundException("VIEW: @include [$file] doesn't exist");
            $compiler = new ViewCompiler($file);
            return $compiler->compile();
        }, $content);

        $content = preg_replace_callback('/@component\s*(.*)/i', function ($matches) {
            $input = trim($matches[1]);
            $parts = preg_split('/\s+/', $input);
            $componentName = $parts[0];
            $args = array_slice($parts, 1);
            $args = implode($args);
            return "<?php print_view_component('$componentName', $args) ?>";
        }, $content);

        $content = preg_replace_callback('/@addons\s*(.*)/i', function ($matches) {
            $file = trim($matches[1]);
            return "<?php import(\"$file\") ?>";
        }, $content);

        $content = preg_replace('/@foreach\s*\((.*)\)/i', '<?php foreach($1) { ?>', $content);
        return preg_replace('/@end/i', '<?php } ?>', $content);
    }
}

function print_view_component(string $name, array $args = []): void
{
    $componentName = $name . 'Component';
    $fullyQualifiedComponentName = App::getInstance()->classLoader->findFullyQualifiedName($componentName);
    $codeAssembler = App::getInstance()->assembler;
    $component = $codeAssembler->assembleObject($fullyQualifiedComponentName)->build();
    if ($component instanceof IViewComponent) {
        foreach ($args as $name => $value) {
            Setter::set($component, $name, $value);
        }
        echo $component->view()->toHtml();
    } else {
        throw new Exception("VIEW: @component [$componentName] is not a view component");
    }
}

function format_date($date, $format = null): string
{
    return _format_date($date, false, $format);
}

function format_datetime($date, $format = null): string
{
    return _format_date($date, true, $format);
}

function format_js_datetime($date): string
{
    if (!$date) return '';
    try {
        if (!$date instanceof DateTime) {
            $date = new DateTime($date);
        }
        return $date->format('Y-m-d H:i:s O');
    } catch (Exception) {
        return "";
    }
}

function _format_date($date, $includeTime = false, $format = null): string
{
    if (!$date) return '';
    if (!is_object($date)) {
        $date = new DateTime($date);
    }

    $i18n = di(I18n::class);
    $date->setTimezone($i18n->culture->timeZone);
    if ($format == null) {
        $format = $includeTime ? $i18n->culture->dateTimeFormat : $i18n->culture->dateFormat;
    }

    return $date->format($format);
}

function format_money($value, $currency = null): string
{
    $i18n = di(I18n::class);
    if (!$currency)
        $currency = $i18n->culture->currency;
    $fmt = numfmt_create($i18n->culture->locale, NumberFormatter::CURRENCY);
    return numfmt_format_currency($fmt, $value, $currency);
}

class Language implements JsonSerializable
{
    public string $code;
    public string $primary;
    public string $local;

    public function __construct($language)
    {
        $this->code = $language;
        if (str_contains($this->code, '-')) {
            $p = explode('-', $this->code);
            $this->primary = $p[0];
            $this->local = strtolower($this->code);
        } else {
            $this->primary = $this->code;
            $this->local = $this->code . '-' . $this->code;
        }
    }

    public function equals($obj): bool
    {
        if ($obj instanceof Language) {
            return $this->code == $obj->code or $this->primary == $obj->primary;
        }

        return false;
    }

    public function jsonSerialize(): string
    {
        return $this->code;
    }
}

class Culture
{
    public string $locale = "en-US";
    public string $dateFormat = "Y-m-d";
    public string $dateTimeFormat = "Y-m-d H:i";
    public string $currency = "USD";
    public DateTimeZone $timeZone;

    public function getLanguage(): Language
    {
        return new Language($this->locale);
    }
}

class I18n
{
    public Culture $culture;
    public array $translations = [];

    public function __construct()
    {
        $this->culture = new Culture();
        $this->culture->timeZone = new DateTimeZone(date_default_timezone_get());
    }

    /**
     * @throws UnknownPathAliasException
     */
    public function loadLangFile($filePath): void
    {
        $path = resolve_path_alias($filePath);
        file_exists($path) or throw new Exception("I18n: Language file [$path] doesn't exist");
        $this->translations = json_decode(file_get_contents($path), true);
    }

    /**
     * @throws UnknownPathAliasException
     */
    public function loadLocalFile($filePath): void
    {
        $path = resolve_path_alias($filePath);
        file_exists($path) or throw new Exception("I18n: Locale file [$path] doesn't exist");
        $locale = json_decode(file_get_contents($path), true);

        foreach (['dateFormat', 'dateTimeFormat', 'currency', 'timeZone', 'locale'] as $key) {
            if (array_key_exists($key, $locale)) {
                $this->culture->$key = $locale[$key];
            }
        }
    }

    public function translate($phrase): string
    {
        if (array_key_exists($phrase, $this->translations)) {
            return $this->translations[$phrase];
        }

        return $phrase;
    }
}

class Di
{
    private array $container = [];

    public function __get(string $name)
    {
        return $this->container[$name];
    }

    public function resolve(string $name): mixed
    {
        return $this->container[$name];
    }

    public function register(object $obj): void
    {
        $reflection = new ReflectionClass($obj);
        $name = $reflection->getName();
        $this->container[$name] = $obj;
    }

    public function registerAs(object $obj, string $name): void
    {
        $this->container[$name] = $obj;
    }

    public function isRegistered($key): bool
    {
        return array_key_exists($key, $this->container);
    }

    /**
     * @throws DiResolveException
     */
    public function resolveReflectionMethod(ReflectionMethod $reflection): array
    {
        $args = [];
        try {
            $parameters = $reflection->getParameters();
            foreach ($parameters as $parameter) {
                $arg = $this->resolveParameter($parameter);
                $args[] = $arg;
            }
        } catch (Exception $e) {
            $class = $reflection->getDeclaringClass()->getName();
            $method = $reflection->getName();
            $prmName = $parameter->getName();
            $prmType = $parameter->getType();

            $parameter = $prmName;
            if ($prmType) {
                $parameter = $prmType . ' $' . $prmName;
            }
            $method == "__construct" ? $method = 'Constructur' : $method = "Method [$method]";
            $message = "Could not create [$class]. $method parameter [$parameter] can't be resolved.";
            throw new DiResolveException($message);
        }

        return $args;
    }

    /**
     * @throws ReflectionException
     */
    public function resolveReflectionFunction(ReflectionFunction $reflection): array
    {
        $parameters = $reflection->getParameters();
        foreach ($parameters as $parameter) {
            $arg = $this->resolveParameter($parameter);
            $args[] = $arg;
        }
        return $args;
    }

    /**
     * @param ReflectionParameter $parameter
     * @return mixed
     * @throws ReflectionException
     * @throws Exception
     */
    private function resolveParameter(ReflectionParameter $parameter): object
    {
        $names = [];
        if ($parameter->hasType()) {
            $typeName = $parameter->getType()->getName();
            if (strtolower($typeName) == 'di') {
                return $this;
            }

            if (!$this->isRegistered($typeName)) {
                $reflection = new ReflectionClass($typeName);
                $constructor = $reflection->getConstructor();
                if ($constructor == null) {
                    $this->register($reflection->newInstance());
                } else {
                    $args = $this->resolveReflectionMethod($constructor);
                    $instance = $reflection->newInstanceArgs($args);
                    $this->register($instance);
                }
            }

            return $this->container[$typeName];
        }

        $names[] = $parameter->getName();
        $names[] = ucfirst($parameter->getName());
        foreach ($names as $name) {
            if ($this->isRegistered($name)) {
                return $this->$name;
            }
        }

        $parameterName = '$' . $parameter->getName();
        $functionName = $parameter->getDeclaringFunction()->getName();
        $className = $parameter->getDeclaringClass()?->getName();
        if ($className) {
            $functionName = $className . $functionName;
        }
        throw new Exception("DI: Function [$functionName()] parameter [$parameterName] not found");
    }
}

class RedirectMessage
{
    private static string $name = 'redirect-msg-';

    public static function isset($name): bool
    {
        $cookieName = self::$name . $name;
        if (Cookies::has($cookieName)) {
            Cookies::delete($cookieName);
            return true;
        }

        return false;
    }

    public static function add(string $name, string $message = ''): void
    {
        Cookies::set(self::$name . $name, $message);
    }

    public static function has($name): bool
    {
        return Cookies::has(self::$name . $name);
    }

    public static function get($name): string
    {
        $message = null;
        $cookieName = self::$name . $name;
        if (Cookies::has($cookieName)) {
            $message = Cookies::get($cookieName);
            Cookies::delete($cookieName);
        }

        return $message;
    }
}

class Cookies
{
    static function get(string $name): string
    {
        return $_COOKIE[$name];
    }

    static function has(string $name): bool
    {
        return array_key_exists($name, $_COOKIE);
    }

    static function set(string $name, string $value): void
    {
        $_COOKIE[$name] = $value;
        setcookie($name, $value, 0, '/');
    }

    static function delete(string $name): void
    {
        unset($_COOKIE[$name]);
        setcookie($name, '', -1, '/');
    }
}

class Response
{
    public int $code = 200;
    public string $redirect;
    public ?string $location = null;
    public ?string $body = null;
    /**
     * @type string[]
     */
    public array $headers = [];

    public function setCookie($name, $value): void
    {
        Cookies::set($name, $value);
    }

    public function setRedirectMessage(string $name, string $message = ''): void
    {
        RedirectMessage::add($name, _($message));
    }

    public function addHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }
}

class UploadedFile
{
    function __construct(
        public string $formName,
        public string $name,
        public string $path,
        public string $type,
        public string $tmp,
        public int    $error,
        public int    $size
    )
    {
    }

    public function isImage(): bool
    {
        return $this->isUploaded() and getimagesize($this->tmp) !== false;
    }

    public function delete(): void
    {
        unlink($this->tmp);
    }

    /**
     * Check whether file was uploaded by user
     * @return bool
     */
    public function wasUploaded(): bool
    {
        return $this->error != 4;
    }

    /**
     * Check whether file was uploaded successfully
     * @return bool
     */
    public function isUploaded(): bool
    {
        return $this->error == 0;
    }

    /**
     * @param int $maxSize (KB)
     * @return int
     */
    public function exceedSize(int $maxSize): int
    {
        return $this->size > ($maxSize * 1024);
    }

    /**
     * @param string $directory directory to write file
     * @param array $options
     * @return bool
     */
    public function move(string $directory, array $options = []): bool
    {
        $filename = $this->name;
        if (is_array_key_value_equal($options, 'filename', true)) {
            $filename = $options['filename'];
        }
        if (is_array_key_value_equal($options, 'gen-unique-filename', true)) {
            $length = array_key_value($options, 'gen-filename-len', 64);
            list(, $extension) = split_file_name_and_ext($this->name);
            $filename = gen_unique_file_name($length, $extension, $directory);
        }
        if (move_uploaded_file($this->tmp, $directory . "/" . $filename)) {
            $this->name = $filename;
            return true;
        }

        return false;
    }
}

class Request extends ArrayObject
{
    private RequestValidator $requestValidator;

    public string $uri;
    public string $baseUri;
    /**
     * in case of /path/to/script/index.php/my-module returns /my-module
     */
    public string $requestUri;
    public string $basePath;
    public string $query;
    public ?array $acceptedLanguages = null;
    public array $parameters = [];
    public array $getParameters;
    public array $postParameters;
    public array $routeParameters;

    /**
     * @type UploadedFile[]
     */
    public array $files;
    public string $method;
    public object $body;

    function __construct(CodeAssembler $codeAssembler)
    {
        $this->requestValidator = new RequestValidator($this, $codeAssembler);

        $this->query = array_key_exists('QUERY_STRING', $_SERVER) ? $_SERVER['QUERY_STRING'] : "";
        $this->uri = strtok($_SERVER["REQUEST_URI"], '?');
        $this->requestUri = array_key_value($_SERVER, 'PATH_INFO', '/');

        $self = $_SERVER['PHP_SELF'];
        $self = substr($self, 0, strrpos($self, '.php') + 4);
        $this->basePath = substr($self, 0, strpos($self, '.php'));
        $this->basePath = substr($this->basePath, 0, strrpos($this->basePath, '/'));
        if (str_starts_with($this->uri, $self)) {
            $this->baseUri = $self;
        } else {
            $this->baseUri = $this->basePath;
        }

        $this->getParameters = $_GET;
        $this->postParameters = $_POST;
        $this->parameters = array_merge($_GET, $_POST);

        $this->method = $_SERVER['REQUEST_METHOD'];

        if (array_key_exists("CONTENT_TYPE", $_SERVER) && $_SERVER["CONTENT_TYPE"] == "application/json") {
            $data = file_get_contents('php://input');
            $this->body = json_decode($data);
        }

        $this->files = $this->parseFiles();
        $this->parameters = $this->sanitize($this->parameters);

        parent::__construct($this->parameters);

        unset($_GET);
        unset($_POST);
    }

    private function parseFiles(): array
    {
        $files = array();
        foreach ($_FILES as $formFieldName => $formFieldFiles) {
            if (is_array($formFieldFiles['name'])) {
                $size = count($formFieldFiles['name']);
                for ($i = 0; $i < $size; $i++) {
                    $files[] = new UploadedFile($formFieldName,
                        $formFieldFiles['name'][$i],
                        $formFieldFiles['full_path'][$i],
                        $formFieldFiles['type'][$i],
                        $formFieldFiles['tmp_name'][$i],
                        $formFieldFiles['error'][$i],
                        $formFieldFiles['size'][$i]);
                }
            } else {
                $files[] = new UploadedFile($formFieldName,
                    $formFieldFiles['name'],
                    $formFieldFiles['full_path'],
                    $formFieldFiles['type'],
                    $formFieldFiles['tmp_name'],
                    $formFieldFiles['error'],
                    $formFieldFiles['size']);
            }
        }

        return $files;
    }

    private function sanitize(array $parameters): array
    {
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $parameters[$key] = $this->sanitize($value);
            } else if (is_numeric($value)) {
                $parameters[$key] = $value * 1;
            } else if ($value == 'true' || $value == 'false') {
                $parameters[$key] = ($value === 'true');
            }
        }

        return $parameters;
    }

    public function getReferer(): ?string
    {
        $referer = null;
        if (array_key_exists('HTTP_REFERER', $_SERVER)) {
            $referer = $_SERVER['HTTP_REFERER'];
        }
        return $referer;
    }

    public function encodeRequestUri(): string
    {
        return urlencode($_SERVER["REQUEST_URI"]);
    }

    public function decodeParameter(string $name): ?string
    {
        $parameter = $this->getParameter($name);
        if ($parameter) {
            $parameter = urldecode($parameter);
        }
        return $parameter;
    }

    public function addRouteParameters(array $parameters): void
    {
        $this->routeParameters = $parameters;
        $this->parameters = array_merge($this->parameters, $parameters);
        $this->exchangeArray($this->parameters);
    }

    function isPost(): bool
    {
        return $this->method == 'POST';
    }

    function isGet(): bool
    {
        return $this->method == 'GET';
    }

    function isDelete(): bool
    {
        return $this->method == 'DELETE';
    }

    public function isPut(): bool
    {
        return $this->method == 'PUT';
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    public function hasParameter(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    public function hasGetParameter(string $name): bool
    {
        return array_key_exists($name, $this->getParameters);
    }

    public function getParameter(string $name, $defaultValue = null): mixed
    {
        if ($this->hasParameter($name)) {
            return $this->parameters[$name];
        }

        return $defaultValue;
    }

    public function get(...$names): mixed
    {
        if (count($names) == 1) {
            return $this->getParameter($names[0]);
        }

        $parameters = array();
        foreach ($names as $name) {
            $parameters[] = $this->getParameter($name);
        }
        return $parameters;
    }

    public function getInt(string $name, ?int $defaultValue = null): ?int
    {
        if ($this->hasParameter($name) and is_int($this->getParameter($name))) {
            return intval($this->parameters[$name]);
        }

        return $defaultValue;
    }

    /**
     * @param string $name
     * @return UploadedFile|null
     */
    public function getFile(string $name): UploadedFile|null
    {
        foreach ($this->files as $file) {
            if ($file->formName == $name) {
                return $file;
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @return UploadedFile[]
     */
    public function getFiles(string $name): array
    {
        $files = array();
        foreach ($this->files as $file) {
            if ($file->formName == $name) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * @param string $name
     * @return bool
     * Check whether request has uploaded valid file
     */
    public function hasFile(string $name): bool
    {
        return $this->getFile($name)?->isUploaded() ?? false;
    }

    /**
     * @return Language[]
     */
    public function getAcceptedLanguages(): array
    {
        if ($this->acceptedLanguages) {
            return $this->acceptedLanguages;
        }

        $this->acceptedLanguages = [];
        if (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
            $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($languages as $language) {
                if (str_contains($language, ';')) {
                    $this->acceptedLanguages[] = new Language(explode(';', $language)[0]);
                } else {
                    $this->acceptedLanguages[] = new Language($language);
                }
            }
        }

        return $this->acceptedLanguages;
    }

    function toView($data = null): array
    {
        $this->parameters['validation'] = $this->validationResult;

        if ($data != null) {
            if (is_object($data)) {
                $data = (array)$data;
            }
            if (is_array($data)) {
                return array_merge($data, $this->parameters);
            }
        }

        return $this->parameters;
    }

    public function toObject(array $map = null): object
    {
        $obj = new stdClass();
        $this->assign($obj, $map);
        return $obj;
    }

    public function assign(object $obj, array $map = null): void
    {
        if ($map == null) {
            foreach ($this->parameters as $name => $value) {
                Setter::set($obj, $name, $value);
            }
        } else {
            foreach ($map as $mapKey => $mapValue) {
                $destinationField = $mapValue;
                if (is_int($mapKey)) {
                    $requestField = $mapValue;
                } else {
                    $requestField = $mapKey;
                }
                Setter::set($obj, $destinationField, $this->getParameter($requestField));
            }
        }
    }

    public function validate($rules): ValidationResult
    {
        return $this->requestValidator->validate($rules);
    }

    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    public function __isset($key)
    {
        return $this->offsetExists($key);
    }
}

class Form
{
    public Request $request;
    public array|object|null $model;
    public array $rules;
    public ?ValidationResult $validationResult = null;

    function __construct($request, object $model = null)
    {
        $this->request = $request;
        $this->model = $model;
    }

    function addRules(array $rules): void
    {
        $this->rules = $rules;
    }

    function removeRule(string $field, string $name): void
    {
        if (array_key_exists($field, $this->rules) and array_key_exists($name, $this->rules[$field])) {
            unset($this->rules[$field][$name]);
        }
        if (array_key_exists($field, $this->rules) and ($key = array_search($name, $this->rules[$field])) !== false) {
            unset($this->rules[$field][$key]);
        }
    }

    function label(string $for, string $text, string $className = ""): string
    {
        return html::label($for, _($text), $className);
    }

    function select($name, $options, $class = null, $required = null, $disabled = null,
                    $autofocus = null, $onChange = null, $onClick = null): string
    {
        $value = $this->getValue($name);
        return html::select($name, $options, $value, $class, $required, $disabled, $autofocus, $onChange, $onClick);
    }

    function text(string $name, $class = null, $required = null, $disabled = null,
                         $autofocus = null, $onChange = null, $onClick = null): string
    {
        $value = $this->getValue($name);
        return html::text($name, $value, $class, $required, $disabled, $autofocus, $onChange, $onClick);
    }

    public function password(string $name, string $class = null): string
    {
        $value = $this->getValue($name);
        return html::password($name, $value, $class);
    }

    function checkbox($name): string
    {
        $value = $this->getValue($name);
        return html::checkbox($name, $value);
    }

    function printError($name, string $message = null): void
    {
        if ($this->validationResult != null) {
            $field = $this->validationResult->__get($name);
            $message = empty($message) ? $field->message : $message;
            echo html::error($field->valid, $message);
        }
    }

    function hasError($name): ?bool
    {
        return $this->validationResult?->__get($name)->invalid;
    }

    function printIfError(string $name, string $present): void
    {
        if ($this->hasError($name))
            echo $present;
    }

    function printIfElseError(string $name, string $present, string $notPresent): void
    {
        if ($this->hasError($name))
            echo $present;
        else
            echo $notPresent;
    }

    function validate(): ValidationResult
    {
        $this->validationResult = $this->request->validate($this->rules);
        return $this->validationResult;
    }

    function isValid(): bool
    {
        return $this->validationResult?->isValid();
    }

    function isInvalid(): bool
    {
        return $this->validationResult != null and !$this->validationResult->isValid();
    }

    function isSubmittedSuccessfully(): bool
    {
        return $this->request->isPost() and $this->validate()->isValid();
    }
    

    public function getValue($name, $empty = null): mixed
    {
        if ($this->request->hasParameter($name)) {
            return Getter::get($this->request->parameters, $name);
        }

        $value = Getter::get($this->model, $name);
        if ($value != null) return $value;

        return $empty;
    }
}

class ValidationField
{
    public bool $valid = true;
    public bool $invalid = false;
    public string $message = "";

    public function __toString()
    {
        return $this->message;
    }
}

class ValidationResult
{
    public bool $isValid = true;
    public array $errors = [];

    function addError(string $field, $value): void
    {
        $this->isValid = false;
        $this->errors[$field] = $value;
    }

    function isValid(): bool
    {
        return $this->isValid;
    }

    function __get($name)
    {
        $field = new ValidationField();
        if (array_key_exists($name, $this->errors)) {
            $field->invalid = true;
            $field->valid = false;
            $field->message = $this->errors[$name];
        }

        return $field;
    }
}

class RequestValidator
{
    public Request $request;
    public CodeAssembler $codeAssembler;

    function __construct(Request $request, CodeAssembler $codeAssembler)
    {
        $this->request = $request;
        $this->codeAssembler = $codeAssembler;
    }

    function validate($rules): ValidationResult
    {
        $result = new ValidationResult();
        foreach ($rules as $fieldName => $subrules) {
            $value = $this->request->getParameter($fieldName);
            foreach ($subrules as $subruleKey => $subruleValue) {
                if (!is_int($subruleKey)) {
                    $validatorName = $subruleKey;
                    $arguments = $subruleValue;
                } else {
                    $validatorName = $subruleValue;
                    $arguments = null;
                }

                $validator = $this->instantiateValidator($validatorName);

                $validatorResult = $validator->validate($value, $fieldName, $this->request->parameters, $arguments);
                if (!$validatorResult->valid) {
                    $result->addError($fieldName, $validatorResult->message);
                    break;
                }
            }
        }
        return $result;
    }

    private function instantiateValidator($validatorName): IValidator
    {
        if (!str_ends_with($validatorName, "Validator") and !str_contains($validatorName, "\\")) {
            $validatorName = $this->normalizeValidatorName($validatorName);
        }
        $validatorName = App::getInstance()->classLoader->findFullyQualifiedName($validatorName);
        $validator = $this->codeAssembler->assembleObject($validatorName)->build();
        if ($validator instanceof IValidator) {
            return $validator;
        }

        throw new Exception("Validator: $validatorName is not a valid validator");
    }

    private function normalizeValidatorName(string $validatorName): string
    {
        $normalizedName = '';
        $len = strlen($validatorName);
        $i = 0;
        while ($i < $len) {
            $char = $validatorName[$i];
            if ($char == '-' or $char == '_') {
                if ($i + 1 < $len) {
                    $validatorName[$i + 1] = strtoupper($validatorName[$i + 1]);
                }
            } else {
                $normalizedName .= $char;
            }
            $i++;
        }

        return ucfirst($normalizedName) . 'Validator';
    }
}

class ValidatorResult
{
    public function __construct(
        public bool   $valid = true,
        public string $message = "")
    {
    }
}

interface IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult;
}

class UncheckedValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($value === true) {
            return new ValidatorResult(false, _("Field has to be unchecked"));
        }
        return new ValidatorResult();
    }
}

class CheckedValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($value !== true) {
            return new ValidatorResult(false, _("Field has to be checked"));
        }
        return new ValidatorResult();
    }
}

class OptionValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (!in_array($value, $args)) {
            return new ValidatorResult(false, _("Invalid [$value] option"));
        }
        return new ValidatorResult();
    }
}

class RequiredValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (empty($value)) {
            $message  = array_key_value($args, 'message', _('Field is required'));
            return new ValidatorResult(false, $message);
        }
        return new ValidatorResult();
    }
}

class AlphaValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (!ctype_alpha($value)) {
            return new ValidatorResult(false, _("Allowed only alphabetic characters"));
        }
        return new ValidatorResult();
    }
}

class AlphaNumValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (!ctype_alnum($value)) {
            return new ValidatorResult(false, _("Allowed only alpha-numeric characters"));
        }
        return new ValidatorResult();
    }
}

class NumberValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (!is_numeric($value)) {
            return new ValidatorResult(false, _("It's not a number"));
        }
        return new ValidatorResult();
    }
}

class EmailValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return new ValidatorResult(false, _("It's not a valid email address"));
        }
        return new ValidatorResult();
    }
}

class MinValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (is_numeric($value)) {
            if (count($args) > 0 && $value < $args[0]) {
                return new ValidatorResult(false, _("Value should be at least %s", $args[0]));
            }
        } else if (is_string($value)) {
            if (count($args) > 0 && mb_strlen($value) < $args[0]) {
                return new ValidatorResult(false, _("Length should be at least %s", $args[0]));
            }
        }
        return new ValidatorResult();
    }
}

class MaxValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (is_string($value)) {
            if (count($args) > 0 && mb_strlen($value) > $args[0]) {
                return new ValidatorResult(false, _("Length shouldn't be greater then %s", $args[0]));
            }
        }
        if (is_numeric($value)) {
            if (count($args) > 0 && $value > $args[0]) {
                return new ValidatorResult(false, _("Value shouldn't be greater then %s", $args[0]));
            }
        }
        return new ValidatorResult();
    }
}

class MaxlengthValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $max): ValidatorResult
    {
        if (is_int($max) > 0 && mb_strlen($value) > $max) {
            return new ValidatorResult(false, _("Length shouldn't be greater then %s", $max));
        }
        return new ValidatorResult();
    }
}

class IntValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $arg): ValidatorResult
    {
        if (!is_int($value)) {
            return new ValidatorResult(false, _("It is not integer"));
        }
        return new ValidatorResult();
    }
}

class RangeValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (is_numeric($value)) {
            if (count($args) > 0 && $value < $args[0]) {
                return new ValidatorResult(false, _("Value should be at least %s", $args[0]));
            }
            if (count($args) > 1 && $value > $args[1]) {
                return new ValidatorResult(false, _("Value shouldn't be greater then %s", $args[1]));
            }
        } else if (is_string($value)) {
            if (count($args) > 0 && mb_strlen($value) < $args[0]) {
                return new ValidatorResult(false, _("Length should be at least %s", $args[0]));
            }
            if (count($args) > 1 && mb_strlen($value) > $args[1]) {
                return new ValidatorResult(false, _("Length shouldn't be greater then %s", $args[1]));
            }
        }
        return new ValidatorResult();
    }
}

class IdentityUser
{
    private bool $isAuthenticated = false;
    public bool $isAnonymous = true;
    public string $id;
    public string $name;
    public string $email;
    public array $data = [];
    public array $claims = [];

    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }

    public function authenticate(): void
    {
        $this->isAuthenticated = true;
        $this->isAnonymous = false;
    }

    public function hasClaims(array $claims): bool
    {
        return count(array_intersect($this->claims, $claims)) == count($claims);
    }

    public function __get(string $key): mixed
    {
        return $this->data[$key];
    }

    public function __set(string $key, string $value): void
    {
        $this->data[$key] = $value;
    }
}

#[Attribute]
class Controller
{
}

#[Attribute]
class Route
{
    public array $urls = array();

    public function __construct(string ...$url)
    {
        $this->urls = $url;
    }
}

#[Attribute]
class PostMethod
{
}

#[Attribute]
class GetMethod
{
}

#[Attribute]
class Get
{
}

#[Attribute]
class Authenticate
{
}

#[Attribute]
class AjaxAuthenticate
{
}

#[Attribute]
class Authorize
{
    public array $claims = array();

    public function __construct(string ...$claims)
    {
        $this->claims = $claims;
    }
}

class ResponseCache
{
    private bool $cacheRequest = false;

    public function __construct(
        private readonly AppConfiguration $configuration,
        private readonly Request          $request,
        private readonly Response         $response,
        private readonly I18n             $i18n
    )
    {
    }

    public function cache(): void
    {
        $this->cacheRequest = true;
    }

    public function read(): object|null
    {
        if (!$this->configuration->cacheEnabled) return null;

        $id = $this->requestToFileName($this->request);
        $cacheFilePath = concatenate_paths($this->cacheDir(), $id);
        if (is_file($cacheFilePath)) {
            $cacheFile = new stdClass();
            $cacheFile->headers = [];
            $cacheFile->body = null;

            $file = fopen($cacheFilePath, "r");
            $cacheFile->createdAt = fgets($file);
            while (($line = fgets($file)) !== false) {
                $line = trim($line);
                if ($line === "-CONTENT:") {
                    break;
                }
                $header = explode(":", $line);
                $cacheFile->headers[$header[0]] = $header[1];
            }
            while (($line = fgets($file, 1024)) !== false) {
                $cacheFile->body .= $line;
            }
            fclose($file);

            return $cacheFile;
        }

        return null;
    }

    public function write(): void
    {
        if (!$this->configuration->cacheEnabled) return;
        if (!$this->cacheRequest) return;

        $dir = $this->cacheDir();
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        if ($this->cacheRequest and $this->response->code == 200) {
            $id = $this->requestToFileName($this->request);
            $filePath = concatenate_paths($this->cacheDir(), $id);

            $file = fopen($filePath, "w");
            fwrite($file, date('m-d-Y H:i:s') . "\n");
            fwrite($file, "Content-Encoding: gzip \n");
            foreach ($this->response->headers as $name => $value) {
                fwrite($file, "$name:$value\n");
            }
            fwrite($file, "-CONTENT:\n");
            fwrite($file, gzencode($this->response->body));
            fclose($file);
        }
    }

    /**
     * glob function is used to delete files
     * https://www.php.net/manual/en/function.glob.php
     * @param string $pattern
     * @return void
     */
    public function delete(string $pattern): void
    {
        $pattern = $this->cacheDir() . "/" . $pattern;
        $files = glob($pattern);
        foreach ($files as $file) {
            while (file_exists($file)) {
                if (!unlink($file)) {
                    usleep(2000);
                }
            }
        }
    }

    private function cacheDir(): string
    {
        return concatenate_paths($this->configuration->getCacheDirectory(), "/responses");
    }

    private function requestToFileName(Request $request): string
    {
        $id = $request->requestUri;
        $id .= "-" . $this->i18n->culture->getLanguage()->primary;
        if ($request->query != '') {
            $id .= "-" . $request->query;
        }
        if ($id != "/") {
            $id = substr($id, 1);
        }
        return str_replace("/", "-", $id);
    }
}

class ClassScanner
{
    private array $directories;

    function __construct(...$directories)
    {
        $this->directories = $directories;
    }

    /**
     * @throws Exception
     */
    public function scan(): array
    {
        $classes = [];
        foreach ($this->getPhpFiles() as $phpFilePath) {
            $phpFileClasses = $this->getClass($phpFilePath);
            foreach ($phpFileClasses as $phpFileClass) {
                !in_array($phpFileClass, $classes) or throw new Exception("ClassScanner: 
                    Class already exist [$phpFileClass]");
                $classes[$phpFileClass] = $phpFilePath;
            }
        }

        return $classes;
    }

    private function getClass($phpFile): array
    {
        $namespace = null;
        $classes = array();
        $tokens = token_get_all(file_get_contents($phpFile));
        foreach ($tokens as $i => $token) {
            $value = $this->getNthTokenValue($tokens, $i);
            if ($value == "namespace") {
                $namespace = $this->getNthTokenValue($tokens, $i + 2) . "\\";
            }

            if (in_array($value, ['class', 'interface', 'trait'])) {
                $whitespace = $this->getNthTokenValue($tokens, $i + 1);
                $name = $this->getNthTokenValue($tokens, $i + 2);
                if ($whitespace != null && trim($whitespace) == '' && !empty($name)) {
                    $classes[] = $namespace . $name;
                }
            }
        }

        return $classes;
    }

    private function getNthTokenValue($tokens, $i): string|null
    {
        if (array_key_exists($i, $tokens) && is_array($tokens[$i]) && array_key_exists(1, $tokens[$i])) {
            return $tokens[$i][1];
        }

        return null;
    }

    /**
     * @throws Exception
     */
    private function getPhpFiles(): array
    {
        $phpFiles = array();
        foreach ($this->directories as $directory) {
            is_dir($directory) or throw new Exception("ClassScanner: path [$directory] it's not directory");

            $directoryPhpFiles = $this->searchPhpFiles($directory);
            $phpFiles = array_merge($directoryPhpFiles, $phpFiles);
        }

        return $phpFiles;
    }

    private function searchPhpFiles($directory): array
    {
        $phpFiles = [];
        $resources = array_diff(scandir($directory), array('.', '..'));
        foreach ($resources as $resource) {
            $path = $directory . '/' . $resource;
            if (is_dir($path)) {
                $phpFiles = array_merge($phpFiles, $this->searchPhpFiles($path));
            } else if (str_ends_with($path, ".php")) {
                $phpFiles[] = $path;
            }
        }

        return $phpFiles;
    }
}

class RouteScanner
{
    function __construct(
        private $classes
    )
    {
    }

    /**
     * @throws Exception
     */
    public function scan(): array
    {
        $routes = [];
        foreach ($this->classes as $fileClass => $filePath) {
            ob_start();
            $message = "RouteScanner: file [$filePath] with class doesn't exist.";
            file_exists($filePath) or throw new Exception($message);
            require_once $filePath;
            ob_get_clean();

            $reflection = new ReflectionClass("\\" . $fileClass);
            $this->validateClassAttribute($reflection);
            $attributes = $reflection->getAttributes(Controller::class);
            if (!count($attributes)) continue;

            $controllerUrl = "";
            $attributes = $reflection->getAttributes(Route::class);
            if (count($attributes)) {
                $controllerUrl = $attributes[0]->newInstance()->urls[0];
                $controllerUrl = $this->normalizeUrl($controllerUrl);
            }

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $this->validateMethodAttribute($method);
                $attributes = $method->getAttributes(Route::class);
                $methodName = strtolower($method->getName());
                if (count($attributes)) {
                    $urls = $attributes[0]->newInstance()->urls;
                    foreach ($urls as $url) {
                        if (!str_starts_with($url, "/") and !empty($controllerUrl)) {
                            $url = $controllerUrl . (str_ends_with($controllerUrl, "/") ?: "/") . $url;
                        } else {
                            $url = $this->normalizeUrl($url);
                        }
                        $routes[$url] = [$fileClass, $methodName];
                    }
                } else {
                    $url = $controllerUrl;
                    if ($methodName !== "index") {
                        $url .= "/" . $methodName;
                    }
                    $routes[$url] = [$fileClass, $methodName];
                }
            }
        }

        uksort($routes, function ($key1, $key2) {
            $lengthMatch = substr_count($key2, "/") <=> substr_count($key1, "/");
            if ($lengthMatch) {
                return $lengthMatch;
            }
            return $key1 <=> $key2;
        });

        return $routes;
    }

    private function validateClassAttribute(ReflectionClass $reflection): void
    {
        $attributes = $reflection->getAttributes();
        foreach ($attributes as $attribute) {
            $name = $attribute->getName();
            $message = "RouteScanner: Class [%s] has %s attribute but it can't be instantiate. 
                Add 'Use %s' below your namespace or fallback to global space #[\%s]";

            foreach (['Controller', 'Route'] as $attributeName) {
                if (str_ends_with($name, $attributeName) && !class_exists($name)) {
                    throw new Exception(sprintf($message, $reflection->name,
                        $attributeName, $attributeName, $attributeName));
                }
            }
        }
    }

    private function validateMethodAttribute(ReflectionMethod $reflection): void
    {
        $attributes = $reflection->getAttributes();
        foreach ($attributes as $attribute) {
            $name = $attribute->getName();
            $message = "RouteScanner: Method [%s->%s] has %s attribute but it can't be
                    instantiate. Add 'Use %s' below your namespace or fallback to global space #[\%s]";

            $attributeName = 'Route';
            if (str_ends_with($name, $attributeName) && !class_exists($name)) {
                $className = $reflection->getDeclaringClass()->getName();
                throw new Exception(sprintf($message, $className, $reflection->name,
                    $attributeName, $attributeName, $attributeName));
            }
        }
    }

    private function normalizeUrl($url)
    {
        if (str_starts_with($url, "/")) return $url;

        return "/" . $url;
    }
}

class VariableCache
{
    private string $cacheDirectory;
    private string $cacheFilePath;

    function __construct(AppConfiguration $configuration, $fileName)
    {
        $this->cacheDirectory = $configuration->getCacheDirectory();
        $this->cacheFilePath = concatenate_paths($this->cacheDirectory, $fileName);
    }

    function exist(): bool
    {
        return file_exists($this->cacheFilePath);
    }

    function save(array $var): void
    {
        if (!is_dir($this->cacheDirectory)) {
            mkdir($this->cacheDirectory, 0777, true);
        }

        $serialized = serialize($var);
        file_put_contents($this->cacheFilePath, $serialized);
    }

    function load(): array
    {
        $classes = file_get_contents($this->cacheFilePath);
        return unserialize($classes);
    }
}

class AppConfiguration
{
    public string $directory;
    public ?string $cacheDir = null;
    public ?string $baseUrl = null;
    public ?string $environment = null;
    public ?string $settingsClassName = null;
    public ?string $settingsFilePath = null;
    public array $aliases = array();
    public ?array $errorPages = null;
    public bool $cacheEnabled = true;
    public ?string $unauthenticatedRedirect = null;
    public ?string $unauthorizedRedirect = null;

    function __construct(string $directory)
    {
        $this->directory = $directory;
        $this->environment = getenv("STORM_ENV");
    }

    function settings($settingClassName, $settingsPathName): void
    {
        $this->settingsClassName = $settingClassName;
        $this->settingsFilePath = $settingsPathName;
    }

    public function isDevelopment(): bool
    {
        return str_starts_with($this->environment, 'development');
    }

    public function isProduction(): bool
    {
        return $this->environment == 'production';
    }

    public function getCacheDirectory(): string
    {
        if ($this->cacheDir) {
            return $this->cacheDir;
        }
        return concatenate_paths($this->directory, "/storm-cache/");
    }
}

class SettingsLoader
{
    /**
     * @throws UnknownPathAliasException
     */
    public static function LoadIfExist(string|object $object, $filePath): ?object
    {
        if (file_exists(resolve_path_alias($filePath))) {
            return self::load($object, $filePath);
        }

        return null;
    }

    /**
     * @throws UnknownPathAliasException
     */
    public static function load(string|object $object, $filePath): object
    {
        $filePath = resolve_path_alias($filePath);
        is_file($filePath) or throw new Exception("SettingsLoader: File $filePath doesn't exist");
        $json = json_decode(file_get_contents($filePath));

        if (is_string($object)) {
            class_exists($object) or throw new Exception("SettingsLoader: Class $object doesn't exist");
            $object = new $object;
        }

        self::map($json, $object);

        return $object;
    }

    private static function map($source, $destination): void
    {
        if ($source == null) return;

        $reflection = new ReflectionClass($destination);
        foreach (get_object_vars($source) as $name => $value) {
            $setMethodName = "set" . ucfirst($name);
            if ($reflection->hasMethod($setMethodName)) {
                $reflection->getMethod($setMethodName)->invoke($destination, $value);
                continue;
            }
            $reflection->hasProperty($name) or
            throw new Exception("SettingsLoader: settings doesn't have property [$name]");

            $property = $reflection->getProperty($name);
            $type = $property->getType();
            if (is_object($value) and $type == 'array') {
                $reflection->getProperty($name)?->setValue($destination, (array)$value);
            } else if (is_object($value) && $property->hasType()) {
                $propertyValueObject = $property->getValue($destination);
                self::map($value, $propertyValueObject);
            } else {
                $reflection->getProperty($name)?->setValue($destination, $value);
            }
        }
    }

    public static function save($obj): void
    {
        $configuration = App::getInstance()->configuration;
        file_put_contents($configuration->settingsFilePath, json_encode($obj));
    }
}

class ClassLoader
{
    public array $classes;

    public function __construct(
        private readonly string $appDir)
    {
    }

    public function load(string $className): void
    {
        if (isset($this->classes) && array_key_exists($className, $this->classes)) {
            require_once $this->classes[$className];
        }

        $classFileName = $this->appDir . "/" . $className . '.php';
        $classFileName = str_replace("\\", "/", $classFileName);
        if (file_exists($classFileName)) {
            require_once $classFileName;
        }
    }

    public function findFullyQualifiedName(string $className): string
    {
        foreach ($this->classes as $fullyQualifiedName => $fileName) {
            if (str_ends_with($fullyQualifiedName, $className)) {
                return $fullyQualifiedName;
            }
        }
        return $className;
    }
}

readonly class CodeAssembler
{
    public function __construct(private Di $di)
    {
    }

    public function assembleCallable(?callable $callable): CallableAssembler
    {
        return new CallableAssembler($callable, $this->di);
    }

    public function assembleObject($name): ObjectAssembler
    {
        return new ObjectAssembler($name, $this->di);
    }
}

readonly class ObjectAssembler
{
    public function __construct(
        private string $name,
        private Di     $di)
    {
    }

    public function build(): object
    {
        $args = [];
        $class = new ReflectionClass($this->name);
        $constructor = $class->getConstructor();
        if ($constructor) {
            $args = $this->di->resolveReflectionMethod($constructor);
        }
        return $class->newInstanceArgs($args);
    }
}

readonly class CallableAssembler
{
    public function __construct(
        private ?closure $closure,
        private Di       $di)
    {
    }

    public function run(): mixed
    {
        if ($this->closure == null) {
            return null;
        }
        $reflection = new ReflectionFunction($this->closure);
        $args = $this->di->resolveReflectionFunction($reflection);
        return $reflection->invokeArgs($args);
    }
}

class App
{
    private static ?App $instance = null;
    private array $routes = [];
    private ?closure $addI18nCallback = null;
    private ?closure $addIdentityUserCallback = null;
    private ?closure $addConfigurationCallback = null;
    private ?closure $beforeRunCallback = null;
    private ?closure $afterSuccessfulRunCallback = null;
    private ?closure $afterFailedRunCallback = null;
    public Di $di;
    public AppConfiguration $configuration;
    public ClassLoader $classLoader;
    public CodeAssembler $assembler;
    public Request $request;

    private function __construct(string $directory = null)
    {
        if ($directory == null) {
            $directory = getcwd();
        }
        $this->configuration = new AppConfiguration($directory);
        $this->configuration->directory = $directory;
        $this->classLoader = new ClassLoader($directory);
        $this->di = new Di();
        $this->assembler = new CodeAssembler($this->di);
    }

    public static function getInstance(?string $appDir = null): App
    {
        if (self::$instance == null) {
            self::$instance = new App($appDir);
        }

        return self::$instance;
    }

    public function beforeRun(callable $callable): void
    {
        $this->beforeRunCallback = $callable;
    }

    public function onSuccess(callable $callable): void
    {
        $this->afterSuccessfulRunCallback = $callable;
    }

    public function onFailure(callable $callable): void
    {
        $this->afterFailedRunCallback = $callable;
    }

    public function addRoute(string $key, $value): void
    {
        $this->routes[$key] = $value;
    }

    public function addConfiguration(callable $callable): void
    {
        $this->addConfigurationCallback = $callable;
    }

    public function addI18n(callable $callable): void
    {
        $this->addI18nCallback = $callable;
    }

    public function addIdentityUser(callable $callable): void
    {
        $this->addIdentityUserCallback = $callable;
    }

    public function run(): void
    {
        try {
            $i18n = new I18n();
            $this->request = $request = new Request($this->assembler);
            $response = new Response();
            $responseCache = new ResponseCache($this->configuration, $request, $response, $i18n);

            spl_autoload_register(function ($className) {
                $this->classLoader->load($className);
            });

            $this->di->register(new IdentityUser());
            $this->di->register($i18n);
            $this->di->register($this->configuration);
            $this->di->register($this->classLoader);
            $this->di->register($request);
            $this->di->register($response);
            $this->di->register($responseCache);

            $this->configureApp();
            $this->configureI18n();

            $cachedResponse = $responseCache->read();
            if ($cachedResponse) {
                foreach ($cachedResponse->headers as $name => $value) {
                    header("$name: $value");
                }
                if ($this->configuration->isDevelopment()) {
                    header("cached: $cachedResponse->createdAt");
                }
                echo $cachedResponse->body;
                die;
            }

            $classCache = new VariableCache($this->configuration, 'classes');
            $routeCache = new VariableCache($this->configuration, "routes");

            if ($this->configuration->isDevelopment() or !$classCache->exist()) {
                $classScanner = new ClassScanner($this->configuration->directory);
                $classes = $classScanner->scan();
                $classCache->save($classes);
            }
            $classes = $classCache->load();
            $this->classLoader->classes = $classes;

            if ($this->configuration->isDevelopment() or !$routeCache->exist()) {
                $routeScanner = new RouteScanner($classes);
                $routes = $routeScanner->scan();
                $routeCache->save($routes);
            }
            $routes = $routeCache->load();
            $this->addRoutes($routes);
            $this->configureIdentityUser();

            $executionRoute = $this->findRoute($request->requestUri);
            $executionRoute or throw new Exception("APP: route for [$request->uri] doesn't exist", 404);
            $request->addRouteParameters($executionRoute->parameters);

            $result = $this->assembler->assembleCallable($this->beforeRunCallback)->run();
            if ($result == null) {
                $executionRunner = new ExecutionRouteRunner($request, $executionRoute, $this->di);
                $result = $executionRunner->run();
            }
            $this->assembler->assembleCallable($this->afterSuccessfulRunCallback)->run();

            if ($result instanceof View) {
                $response->body = $result->toHtml();
            } else if ($result instanceof Redirect) {
                $response->location = $result->location;
            } else if (is_object($result) or is_array($result)) {
                $response->addHeader("Content-Type", "application/json; charset=utf-8");
                $response->body = json_encode($result);
            } else if (is_string($result) || is_numeric($result)) {
                $response->body = $result;
            }

            if ($response->location) {
                header("Location: $response->location");
                die;
            }

            http_response_code($response->code);
            foreach ($response->headers as $name => $value) {
                header("$name: $value");
            }
            echo $response->body;

            $responseCache->write();
        } catch (Exception $e) {
            if ($this->afterFailedRunCallback) {
                try {
                    $this->assembler->assembleCallable($this->afterFailedRunCallback)->run();
                } finally {
                }
            }

            if ($e instanceof AjaxAuthenticationException) {
                http_response_code(401);
                die;
            }
            if ($e instanceof AuthenticationException and $this->configuration->unauthenticatedRedirect) {
                $redirect = $this->request->encodeRequestUri();
                $location = $this->configuration->unauthenticatedRedirect . '?redirect=' . $redirect;
                header("Location: $location");
                die;
            }
            if ($e instanceof UnauthorizedException and $this->configuration->unauthorizedRedirect) {
                header("Location: {$this->configuration->unauthorizedRedirect}");
                die;
            }

            $code = (!is_int($e->getCode()) or $e->getCode() == 0) ? 500 : $e->getCode();
            http_response_code($code);

            $errorPage = $this->configuration->errorPages ?? array();
            if (array_key_exists($code, $errorPage)) {
                include_once $this->configuration->errorPages[$code];
                include_once $this->configuration->errorPages[$code];
            } else {
                echo $e->getMessage();
                echo '</br>';
                echo $e->getTraceAsString();
            }
        }
    }

    private function addRoutes(array $routes): void
    {
        $this->routes = array_merge($routes, $this->routes);
    }

    /**
     * @throws UnknownPathAliasException
     */
    private function configureApp(): void
    {
        if ($this->addConfigurationCallback != null) {
            $this->assembler->assembleCallable($this->addConfigurationCallback)->run();
        }

        if ($this->configuration->settingsFilePath != null) {
            $filePath = resolve_path_alias($this->configuration->settingsFilePath);
            $className = $this->configuration->settingsClassName;
            $settings = SettingsLoader::load($className, $filePath);
            $this->di->register($settings);
        }

        if ($this->configuration->errorPages) {
            foreach ($this->configuration->errorPages as $code => $file) {
                $this->configuration->errorPages[$code] = resolve_path_alias($file);
            }
        }

        if ($this->configuration->aliases == null) {
            $this->configuration->aliases = array();
        }
    }

    private function configureIdentityUser(): void
    {
        if ($this->addIdentityUserCallback != null) {
            $user = $this->assembler->assembleCallable($this->addIdentityUserCallback)->run();
            $user != null or throw new Exception("AddIdentityUser returned value is null");
            $user instanceof IdentityUser or throw new Exception("AddIdentityUser returned value is not IdentityUser");

            $this->di->registerAs($user, IdentityUser::class);
            $this->di->register($user);
        }
    }

    private function configureI18n(): void
    {
        if ($this->addI18nCallback != null) {
            $this->assembler->assembleCallable($this->addI18nCallback)->run();
        }
    }

    private function matchSegments(array $routeSegments, array $requestSegments): ?array
    {
        $parameters = [];
        foreach ($routeSegments as $i => $routeSegment) {
            if (str_starts_with($routeSegment, ":")) {
                $name = str_replace(":", "", $routeSegment);
                $parameters[$name] = $requestSegments[$i];
            } else if ($routeSegment != $requestSegments[$i]) {
                return null;
            }
        }

        return $parameters;
    }

    private function findRoute($requestUri): ?ExecutionRoute
    {
        foreach ($this->routes as $pattern => $destination) {
            if ($pattern == $requestUri) {
                return new ExecutionRoute($pattern, $destination);
            }
        }

        $requestSegments = none_empty_explode("/", $requestUri);
        foreach ($this->routes as $route => $destination) {
            if (substr_count($route, "/") == substr_count($requestUri, "/")) {
                $routeSegments = none_empty_explode("/", $route);
                $parameters = $this->matchSegments($routeSegments, $requestSegments);
                if ($parameters) {
                    return new ExecutionRoute($route, $destination, $parameters);
                }
            }
        }
        return null;
    }
}

readonly class ExecutionRouteRunner
{
    public function __construct(
        private Request        $request,
        private ExecutionRoute $executionRoute,
        private Di             $di)
    {
    }

    public function run(): mixed
    {
        $endpoint = $this->executionRoute->endpoint;
        if (is_callable($endpoint)) {
            $callable = new ReflectionFunction($endpoint);
            $args = $this->di->resolveReflectionFunction($callable);
            $callable->invokeArgs($args);
        }
        if (is_array($endpoint)) {
            $args = [];
            $class = new ReflectionClass($endpoint[0]);
            $method = $class->getMethod($endpoint[1]);

            $pattern = $this->executionRoute->pattern;

            $this->validateAjaxAuthentication($class, $method, $pattern);
            $this->validateRequestType($class, $method, $pattern);
            $this->validateAuthentication($class, $method, $pattern);
            $this->validateClaims($class, $method, $pattern);

            $constructor = $class->getConstructor();
            if ($constructor) {
                $args = $this->di->resolveReflectionMethod($constructor);
            }
            $obj = $class->newInstanceArgs($args);
            $args = $this->di->resolveReflectionMethod($method);
            return $method->invokeArgs($obj, $args);
        }

        return null;
    }

    /**
     * @throws AjaxAuthenticationException with code 401 if request is not authenticated
     */
    private function validateAjaxAuthentication(ReflectionClass $class, ReflectionMethod $method, $pattern): void
    {
        if (count($class->getAttributes(AjaxAuthenticate::class)) or
            count($method->getAttributes(AjaxAuthenticate::class))) {
            $user = $this->di->resolve(IdentityUser::class);
            if (!$user->isAuthenticated()) {
                throw new AjaxAuthenticationException("APP: authentication required $pattern", 401);
            }
        }
    }

    /**
     * @throws Exception with code 404 if request method is different then required.
     */
    private function validateRequestType(ReflectionClass $class, ReflectionMethod $method, $pattern): void
    {
        if (count($class->getAttributes(PostMethod::class)) or
            count($method->getAttributes(PostMethod::class))) {
            if (!$this->request->isPost()) {
                throw new Exception("POST required. $pattern", 404);
            }
        }

        if (count($class->getAttributes(GetMethod::class)) or
            count($method->getAttributes(GetMethod::class))) {
            if (!$this->request->isGet()) {
                throw new Exception("GET required. $pattern", 404);
            }
        }
    }

    private function validateAuthentication(ReflectionClass $class, ReflectionMethod $method, $pattern): void
    {
        if (count($class->getAttributes(Authenticate::class)) or
            count($method->getAttributes(Authenticate::class))) {
            $user = $this->di->resolve(IdentityUser::class);
            if (!$user->isAuthenticated()) {
                throw new AuthenticationException("APP: authentication required $pattern", 401);
            }
        }
    }

    private function validateClaims(ReflectionClass $class, ReflectionMethod $method, $pattern): void
    {
        $classAttributes = $class->getAttributes(Authorize::class);
        $methodAttributes = $method->getAttributes(Authorize::class);
        $classClaims = $this->getClaimsFromAttribute($classAttributes);
        $methodClaims = $this->getClaimsFromAttribute($methodAttributes);

        $requiredClaims = array_merge($classClaims, $methodClaims);

        if ($classAttributes or $methodAttributes) {
            $user = $this->di->resolve(IdentityUser::class);
            if (!$user->hasClaims($requiredClaims)) {
                throw new UnauthorizedException("APP: Claim required $pattern", 403);
            }
        }
    }

    private function getClaimsFromAttribute(array $attributes): array
    {
        if (count($attributes)) {
            return $attributes[0]->newInstance()->claims;
        }

        return [];
    }
}

class ExecutionRoute
{
    public array|closure $endpoint;
    public string $pattern;

    public array $parameters;

    function __construct(string $pattern, array|closure $execution, array $parameters = array())
    {
        $this->pattern = $pattern;
        $this->endpoint = $execution;
        $this->parameters = $parameters;
    }
}

class Getter
{
    static function get($var, $name): mixed
    {
        if ($var == null) {
            return null;
        } else if (is_array($var)) {
            if (array_key_exists($name, $var))
                return $var[$name];
        } else if (is_object($var)) {
            $reflection = new ReflectionObject($var);
            $methodName = 'get' . $name;
            if ($reflection->hasMethod($methodName)) {
                $method = $reflection->getMethod($methodName);
                return $method->invoke($var);
            } else if ($reflection->hasProperty($name)) {
                return $var->$name;
            }
        }

        return null;
    }
}

class Setter
{
    static function set($var, $name, $value): void
    {
        $reflection = new ReflectionObject($var);
        $methodName = 'set' . $name;
        if ($reflection->hasMethod($methodName)) {
            $method = $reflection->getMethod($methodName);
            $method->invoke($var, $value);
        } else if ($reflection->hasProperty($name) or $var instanceof stdClass) {
            $var->$name = $value;
        }
    }
}

class js
{
    static function i18n(array $phrases, $name): string
    {
        $jsArray = "<script type=\"text/javascript\">\n";
        foreach ($phrases as $phrase) {
            $translation = _($phrase);
            $jsArray .= $name . "['" . $phrase . "']" . " = '$translation';\n";
        }
        $jsArray .= "</script>\n";

        return $jsArray;
    }
}

class html
{
    static function label(string $for, string $text, string $className = ""): string
    {
        return "<label for=\"$for\" class=\"$className\">$text</label>";
    }

    static function text(string $name, string|null $value = "", $class = null, $required = null,
                                $disabled = null, $autofocus = null, $onChange = null, $onClick = null): string
    {
        $html = "<input type=\"text\" id=\"$name\" name=\"$name\" value=\"$value\" ";
        $html .= self::attr('class', $class);
        $html .= self::attr('disabled', $disabled);
        $html .= "/>";
        return $html;
    }

    static function link(string $name, string $href, array $attributes = []): string
    {
        $attributes['href'] = $href;
        $html = "<a ";
        foreach ($attributes as $key => $value) {
            $html .= "$key=\"$value\" ";
        }
        $html .= ">$name</a>";
        return $html;
    }

    static function password(string $name, string|null $value = "", string $class = null): string
    {
        $html = "<input type=\"password\" id=\"$name\" name=\"$name\" value=\"$value\" ";
        $html .= self::attr('class', $class);
        $html .= "/>";
        return $html;
    }

    static function checkbox($name, bool $checked = null): string
    {
        $html = "<input type=\"checkbox\" name=\"$name\" value=\"false\" checked style=\"display: none\" /> \n";
        $html .= "<input type=\"checkbox\" name=\"$name\" id=\"$name\" value=\"true\" ";
        $html .= self::attr('checked', $checked);
        $html .= "/> \n";

        return $html;
    }

    static function select($name, $values, $selected = null, $class = null, $required = null, $disabled = null,
                           $autofocus = null, $onChange = null, $onClick = null): string
    {
        $html = "<select id=\"$name\" name=\"$name\" ";
        $html .= self::attr('class', $class);
        $html .= self::attr('required', $required);
        $html .= self::attr('disabled', $disabled);
        $html .= self::attr('autofocus', $autofocus);
        $html .= self::attr('onChange', $onChange);
        $html .= self::attr('onClick', $onClick);
        $html .= ">";
        $html .= html::options($values, $selected);
        $html .= "</select>";
        return $html;
    }

    static function options($options, $selected = null): string
    {
        $html = "";
        foreach ($options as $value => $name) {
            $attr = '';
            if ($selected != null && $value == $selected)
                $attr = "selected";
            $html .= "<option ";
            $html .= "value=\"$value\" ";
            $html .= "$attr>$name</option>";
        }
        return $html;
    }

    static function error($valid, $message, string $class = "form-error"): string
    {
        $html = "";
        if (!$valid) {
            $html = "<div class=\"$class\">$message</div>";
        }
        return $html;
    }

    private static function attr($attr, $value = null): string
    {
        if (empty($value)) return '';

        if ($value === true) {
            return $attr . " ";
        }

        return "$attr=\"$value\" ";
    }
}

