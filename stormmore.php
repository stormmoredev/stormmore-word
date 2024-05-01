<?php
/** @noinspection */

use JetBrains\PhpStorm\NoReturn;

class STORM
{
    public static ?App $instance = NULL;

    static function explode($delimiter, $string, $limit = PHP_INT_MAX): array
    {
        $partsUnclean = explode($delimiter, $string, $limit);
        $parts = [];
        foreach ($partsUnclean as $part) {
            if ($part != "")
                $parts[] = $part;
        }
        return $parts;
    }

    /**
     * @throws Exception
     */
    static function aliasPath(string $templatePath): string
    {
        STORM::$instance !== null or throw new Exception("Alias: initialize app before");

        $appDirectory = STORM::$instance->directory;
        $aliases = STORM::$instance->configuration->aliases;
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

            array_key_exists($alias, $aliases) or throw new Exception("Alias [$alias] doesn't exist");
            $templatePath = $appDirectory . "/" . $aliases[$alias] . $path;
        }

        return $templatePath;
    }
}

function app($appDirectory): App
{
    STORM::$instance = new App($appDirectory);
    return STORM::$instance;
}

function di(string $key = null): mixed
{
    if ($key == null)
        return STORM::$instance->di;
    return STORM::$instance->di->$key;
}

function _(string $phrase, ...$args): string
{
    return _args($phrase, $args);
}

function _args(string $phrase, array $args): string
{
    $i18n = STORM::$instance->di->I18n;
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
    $file = STORM::aliasPath($file);
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

function url($path, $args)
{
    if (count($args)) {
        $path = $path . "?" . http_build_query($args);
    }

    return $path;
}

function back(string $url = "/"): Redirect
{
    if (array_key_exists('HTTP_REFERER', $_SERVER)) {
        return redirect($_SERVER['HTTP_REFERER']);
    }

    return redirect($url);
}

function redirect(string $url = "/"): Redirect
{
    return new Redirect($url);
}

class Redirect
{
    public ?string $location = null;
    public ?string $body = null;

    public function __construct(string $url)
    {
        $baseUrl = STORM::$instance->configuration->baseUrl;
        if (str_starts_with($url, "http")) {
            $this->location = $url;
        } else if ($baseUrl != null and str_starts_with($baseUrl, 'http')) {
            $this->location = $baseUrl . $url;
        } else {
            $this->body = "<!DOCTYPE html><html lang=\"en\"><body>
                    <script type=\"text/javascript\">document.location.href=\"$this->redirectUrl\"</script>
                    </body>
                    </html>";
        }
    }
}

function view($templateFileName, array|object $data = []): View
{
    $addons = STORM::$instance->configuration->viewAddons;
    return new View($templateFileName, $data, $addons);
}

class View
{
    private ?string $addonsFilePath;
    private array $bag = [];

    public function __construct(
        private readonly string       $fileName,
        private readonly array|object $data = [],
        string                        $addonsFilePath = null)
    {
        $this->addonsFilePath = $addonsFilePath ? STORM::aliasPath($addonsFilePath) : null;
    }

    public function __get($key)
    {
        return array_key_exists($key, $this->bag) ? $this->bag[$key] : null;
    }

    public function __set(string $name, $value): void
    {
        $this->bag[$name] = $value;
    }

    function toHtml(): string
    {
        $env = STORM::$instance->configuration->environment;
        $appDirectory = STORM::$instance->directory;
        $cacheDirectory = "$appDirectory/.cache/";

        $templateFileName = STORM::aliasPath($this->fileName);
        $templateFileName = $templateFileName . '.php';

        $cachedTemplateFileName = str_replace("/", "-", $templateFileName);
        $cachedTemplateFilePath = $appDirectory . "/.cache/$cachedTemplateFileName";

        if ($env == 'development' || !file_exists($cachedTemplateFilePath)) {
            file_exists($templateFileName) or throw new Exception("VIEW: [$templateFileName] doesn't exist ");
            if (!is_dir($cacheDirectory)) mkdir($cacheDirectory);

            $compiler = new ViewCompiler($templateFileName);
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
        if ($this->addonsFilePath) {
            $expMessage = "VIEW: helpers [$this->addonsFilePath] doesn't exist";
            file_exists($this->addonsFilePath) or throw new Exception($expMessage);
            require_once $this->addonsFilePath;
        }

        require_once $cachedTemplateFilePath;
        return ob_get_clean();
    }
}

class ViewCompiler
{
    public string $file;

    function __construct(string $file)
    {
        $this->file = $file;
    }

    public function compileTo($destination)
    {
        $content = $this->compile();
        file_put_contents($destination, $content);
    }

    public function compile()
    {
        $content = file_get_contents($this->file);
        $content = $this->_compile($content);
        $content = $this->surround($content);
        return $content;
    }

    private function surround($content): string
    {
        preg_match('/@layout\s(.*?)\s/i', $content, $matches);
        if (!count($matches)) return $content;

        $content = str_replace($matches[0], '', $content);
        $layoutFilePath = STORM::aliasPath($matches[1]);
        file_exists($layoutFilePath) or throw new Exception("VIEW: layout [$layoutFilePath] doesn't exist");

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
                    $args = STORM::explode(" ", $parts[1]);
                    $args = implode(",", $args);
                }
                $phrase = '"' . $phrase . '"';
            }

            return "<?php echo _args($phrase, [$args]) ?>";
        }, $content);

        $content = preg_replace_callback('/{{\s*(.+?)\|\s*(.+?)}}/i', function ($matches) {
            $filterNodes = STORM::explode(' ', trim($matches[2]), 2);
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
                $file = STORM::aliasPath($matches[1]);
            } else {
                $file = dirname($this->file) . "/" . trim($matches[1]);
            }
            $file = trim($file);
            file_exists($file) or throw new Exception("VIEW: @include [$file] doesn't exist");
            $compiler = new ViewCompiler($file);
            return $compiler->compile();
        }, $content);

        $content = preg_replace('/@foreach\s*\((.*)\)/i', '<?php foreach($1) { ?>', $content);
        $content = preg_replace('/@end/i', '<?php } ?>', $content);

        return $content;
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

function format_js_datetime($date, $format = null): string
{
    if (!$date) return '';
    if (!is_object($date)) {
        $date = new DateTime($date);
    }

    return $date->format('Y-m-d H:i:s O');
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

    public function loadLangFile($filePath): void
    {
        $path = STORM::aliasPath($filePath);
        file_exists($path) or throw new Exception("I18n: Language file [$path] doesn't exist");
        $this->translations = json_decode(file_get_contents($path), true);
    }

    public function loadLocalFile($filePath): void
    {
        $path = STORM::aliasPath($filePath);
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

    public function resolveReflectionFunction(ReflectionFunctionAbstract $reflection): array
    {
        $args = [];
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
    private function resolveParameter(ReflectionParameter $parameter): mixed
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
                    $args = $this->resolveReflectionFunction($constructor);
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

class Flash
{
    private static string $name = 'flash-msg-';

    public static function set(string $name): void
    {
        Cookies::set(self::$name . $name, '_');
    }

    public static function isset($name): bool
    {
        $cookieName = self::$name . $name;
        if (Cookies::has($cookieName)) {
            Cookies::delete($cookieName);
            return true;
        }

        return false;
    }

    public static function add(string $name, string $message): void
    {
        Cookies::set(self::$name . $name, $message);
    }

    public static function exist($name): bool
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

    public function setFlashFlag(string $name): void
    {
        Flash::set($name);
    }

    public function addFlashMessage(string $name, string $message): void
    {
        Flash::add($name, _($message));
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

    public function isValid(): bool
    {
        return $this->error == 0;
    }

    public function isValidImage(): bool
    {
        return $this->isValid() and getimagesize($this->tmp) !== false;
    }

    public function delete(): void
    {
        unlink($this->tmp);
    }

    /**
     * Check whether file was upload by user
     * @return bool
     */
    public function wasUploaded(): bool
    {
        return $this->error != 4;
    }

    /**
     * Check whether file was upload successfully
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
}

class Request extends ArrayObject
{
    public string $uri;
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
    public ValidationResult $validationResult;

    function __construct()
    {
        $this->query = array_key_exists('QUERY_STRING', $_SERVER) ? $_SERVER['QUERY_STRING'] : "";
        $this->uri = $this->parseRequestUri();
        $this->getParameters = $_GET;
        $this->postParameters = $_POST;
        $this->parameters = array_merge($_GET, $_POST);

        $this->method = $_SERVER['REQUEST_METHOD'];

        $this->validationResult = new ValidationResult();

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

    function addRouteParameters(array $parameters): void
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

    public function exist(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    public function getParameter(string $name, $defaultValue = null): mixed
    {
        if ($this->exist($name)) {
            return $this->parameters[$name];
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

    public function hasValidFile(string $name): bool
    {
        return $this->getFile($name)?->isValid() ?? false;
    }

    public function hasValidImageFile(string $name): bool
    {
        return $this->getFile($name)?->isValidImage() ?? false;
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

    public function toObject(): object
    {
        $obj = new stdClass();
        foreach ($this->parameters as $key => $parameter) {
            $obj->$key = $parameter;
        }

        return $obj;
    }

    public function bind(object $var): object
    {
        foreach ($this->parameters as $name => $value) {
            Setter::set($var, $name, $value);
        }

        return $var;
    }

    function validate($rules): ValidationResult
    {
        $requestValidator = new RequestValidator($this);
        $this->validationResult = $requestValidator->validate($rules);
        return $this->validationResult;
    }

    function __get($key)
    {
        return $this->offsetGet($key);
    }

    function __isset($key)
    {
        return $this->offsetExists($key);
    }

    //returns request uri relative to application file
    //path/on/hdd/index.php/product/first /returns product/first
    private function parseRequestUri(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $pos = strpos($_SERVER['REQUEST_URI'], "?");
        if ($pos) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        $endOfFirstSegment = strpos($requestUri, "/", 1);
        if ($endOfFirstSegment) {
            $getcwd = str_replace("\\", "/", getcwd());
            $search = substr($requestUri, 0, $endOfFirstSegment);
            $lastOccuranceOf = strripos($getcwd, $search);
            $common = substr($getcwd, $lastOccuranceOf);
            $requestUri = str_replace($common, "", $requestUri);
            $requestUri = $requestUri == "" ? "/" : $requestUri;
        }

        if ($requestUri != '/' && str_ends_with($requestUri, "/")) {
            $requestUri = substr($requestUri, 0, -1);
        }

        return $requestUri;
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

    function error($name): ?string
    {
        if ($this->validationResult != null) {
            $field = $this->validationResult->__get($name);
            return html::error($field->valid, $field->message);
        }

        return null;
    }

    function validate(): ValidationResult
    {
        $requestValidator = new RequestValidator($this->request);
        $this->validationResult = $requestValidator->validate($this->rules);
        return $this->validationResult;
    }

    function isValid(): bool
    {
        return $this->validationResult?->isValid();
    }

    public function getValue($name, $empty = null): mixed
    {
        if ($this->request->exist($name)) {
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

    function addError($field, $value): void
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

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    function validate($rules): ValidationResult
    {
        $callables = [];
        foreach ($rules as $field => $definition) {
            $callables[$field] = $this->toCallableArray($definition);
        }

        $result = new ValidationResult();
        foreach ($callables as $field => $callableArray) {
            $value = null;
            if ($this->request->exist($field)) {
                $value = $this->request->$field;
            }

            $validationMsg = $this->validateField($callableArray, $value, $field, $this->request);
            if (is_string($validationMsg) && !empty($validationMsg)) {
                $result->addError($field, $validationMsg);
            }
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    private function validateField($callables, $value, $name, $parameters)
    {
        foreach ($callables as $callable) {
            $args = [];
            if (str_contains($callable, ':')) {
                $parts = explode(":", $callable);
                $callable = $parts[0];
                $args = array_slice($parts, 1);
            }
            $callable = trim($callable);
            $callable .= '_validator';

            if (!is_callable($callable)) {
                throw new Exception("Validator: validator [$callable] doesn't exist.");
            }

            $res = $callable($value, $name, $parameters, $args);
            if ($res) return $res;
        }

        return false;
    }

    private function toCallableArray($defintion): array
    {
        if (is_array($defintion)) {
            $callables = [];
            foreach ($defintion as $subdefinitions) {
                $callables = array_merge($callables, $this->toCallableArray($subdefinitions));
            }
            return $callables;
        }
        if (is_string($defintion)) {
            if (str_contains($defintion, ' ')) {
                $subrules = STORM::explode(' ', $defintion);
                foreach ($subrules as $key => $value)
                    $subrules[$key] = $value;
                return $subrules;
            }
            return array($defintion);
        }

        return [];
    }
}

function unchecked_validator($value): ?string
{
    if ($value === true) {
        return "Field has to be unchecked";
    }

    return null;
}

function checked_validator($value): ?string
{
    if ($value !== true) {
        return "Field has to be checked";
    }

    return null;
}

function option_validator($value, $name, $parameters, $args): ?string
{
    if (!in_array($value, $args)) {
        return "Invalid [$value] option";
    }

    return null;
}

function required_validator($value, $name, $parameters): ?string
{
    if (empty($value)) {
        return _("Field is required", $name);
    }

    return null;
}

function alpha_validator($value): ?string
{
    if (!ctype_alpha($value)) {
        return _("Allowed only alphabetic characters");
    }

    return null;
}

function alphanum_validator($value): ?string
{
    if (!ctype_alnum($value)) {
        return _("Allowed only alpha-numeric characters");
    }

    return null;
}

function number_validator($value): ?string
{
    if (!is_numeric($value)) {
        return _("It's not a number");
    }

    return null;
}

function email_validator($value): ?string
{
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        return _("It's not a valid email address");
    }

    return null;
}

function min_validator($value, $name, $parameters, $args): ?string
{
    if (is_numeric($value)) {
        if (count($args) > 0 && $value < $args[0]) {
            return _("Value should be at least %s", $args[0]);
        }
    } else if (is_string($value)) {
        if (count($args) > 0 && mb_strlen($value) < $args[0]) {
            return _("Length should be at least %s", $args[0]);
        }
    }

    return null;
}

function max_validator($value, $name, $parameters, $args): ?string
{
    if (is_string($value)) {
        if (count($args) > 0 && mb_strlen($value) > $args[0]) {
            return _("Length shouldn't be greater then %s", $args[0]);
        }
    }
    if (is_numeric($value)) {
        if (count($args) > 0 && $value > $args[0]) {
            return _("Value shouldn't be greater then %s", $args[0]);
        }
    }

    return null;
}

function range_validator($value, $name, $parameters, $args): ?string
{
    if (is_numeric($value)) {
        if (count($args) > 0 && $value < $args[0]) {
            return _("Value should be at least %s", $args[0]);
        }
        if (count($args) > 1 && $value > $args[1]) {
            return _("Value shouldn't be greater then %s", $args[1]);
        }
    } else if (is_string($value)) {
        if (count($args) > 0 && mb_strlen($value) < $args[0]) {
            return _("Length should be at least %s", $args[0]);
        }
        if (count($args) > 1 && mb_strlen($value) > $args[1]) {
            return _("Length shouldn't be greater then %s", $args[1]);
        }
    }

    return null;
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
class Authenticated
{
}

#[Attribute]
class Claim
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
        private readonly AppConfiguration  $appConfiguration,
        private readonly Request  $request,
        private readonly Response $response,
        private readonly I18n     $i18n
    )
    {
    }

    public function cache(): void
    {
        $this->cacheRequest = true;
    }

    public function read(): object|null
    {
        if (!$this->appConfiguration->cacheEnabled) return null;

        $id = $this->requestToFileName($this->request);
        $cacheFilePath = $this->cacheDir() . "/" . $id;
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
        if (!$this->appConfiguration->cacheEnabled) return;
        if (!$this->cacheRequest) return;

        $dir = $this->cacheDir();
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if ($this->cacheRequest and $this->response->code == 200) {
            $id = $this->requestToFileName($this->request);
            $filePath = $this->cacheDir() . "/" . $id;

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
     * use glob function to delete files
     * https://www.php.net/manual/en/function.glob.php
     * @param string $pattern
     * @return void
     */
    public function delete(string $pattern): void
    {
        $pattern = $this->cacheDir() . "/" . $pattern;
        $files = glob($pattern);
        foreach ($files as $file) {
            while(file_exists($file)) {
                if (!unlink($file)) {
                    usleep(2000);
                }
            }
        }
    }

    private function cacheDir(): string
    {
        $directory = $this->appConfiguration->directory;
        return $directory . "/.cache/responses";
    }

    private function requestToFileName(Request $request): string
    {
        $id = $request->uri;
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
            $message = "RouteScanner: Class [%s] has %s attribute but it can't be initnialized. Add 'Use %s' below your namespace or fallback to global space #[\%s]";

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
                    initnialized. Add 'Use %s' below your namespace or fallback to global space #[\%s]";

            foreach (['Route'] as $attributeName) {
                if (str_ends_with($name, $attributeName) && !class_exists($name)) {
                    $className = $reflection->getDeclaringClass()->getName();
                    throw new Exception(sprintf($message, $className, $reflection->name,
                        $attributeName, $attributeName, $attributeName));
                }
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

    function __construct($directory, $fileName)
    {
        $this->cacheDirectory = $directory;
        $this->cacheFilePath = $directory . '/' . $fileName;
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
    public ?string $baseUrl = null;
    public ?string $environment = null;
    public ?string $settingsClassName = null;
    public ?string $settingsFilePath = null;
    public array $aliases = array();
    public ?array $errorPages = null;
    public ?string $viewAddons = null;
    public bool $cacheEnabled = true;
    public ?string $unauthenticatedRedirect = null;
    public ?string $unauthorizedRedirect = null;

    function __construct(string $directory)
    {
        $this->directory = $directory;
        $this->environment = getenv("APP_ENV");
    }

    function settings($settingClassName, $settingsPathName): void
    {
        $this->settingsClassName = $settingClassName;
        $this->settingsFilePath = $settingsPathName;
    }

    public function isDevelopment(): bool
    {
        return $this->environment == 'development';
    }

    public function isProduction(): bool
    {
        return $this->environment == 'production';
    }
}

class SettingsLoader
{
    public static function LoadIfExist(string|object $object, $filePath): ?object
    {
        if (file_exists(STORM::aliasPath($filePath))) {
            return self::load($object, $filePath);
        }

        return null;
    }

    public static function load(string|object $object, $filePath): object
    {
        $filePath = STORM::aliasPath($filePath);
        is_file($filePath) or throw new Exception("SettingsLoader: File $filePath doesn't exist");
        $json = json_decode(file_get_contents($filePath));

        if (is_string($object)) {
            class_exists($object) or throw new Exception("SettingsLoader: Class $object doesn't exist");
            $object = new $object;
        }

        self::map($json, $object);

        return $object;
    }

    private static function map($source, $destination)
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
        $configuration = STORM::$instance->configuration;
        file_put_contents($configuration->settingsFilePath, json_encode($obj));
    }
}

class App
{
    private array $classes;
    public ?closure $lazyI18n = null;
    public ?closure $lazyIdentityUser = null;
    public ?closure $lazyConfiguration = null;
    public AppConfiguration $configuration;
    public string $directory;
    public ?closure $beforeRunCallback;
    public ?closure $afterSuccessfulRunCallback;
    public ?closure $afterFailedRunCallback;
    public array $routes = [];
    public Di $di;

    function __construct($directory)
    {
        $this->configuration = new AppConfiguration($directory);
        $this->directory = $directory;
        $this->di = new Di();

        spl_autoload_register(function ($className) {
            if (isset($this->classes) && array_key_exists($className, $this->classes)) {
                require_once $this->classes[$className];
            }

            $classFileName = $this->directory . "/" . $className . '.php';
            $classFileName = str_replace("\\", "/", $classFileName);
            if (file_exists($classFileName)) {
                require_once $classFileName;
            }
        });
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
        $this->lazyConfiguration = $callable;
    }

    public function addI18n(callable $callable): void
    {
        $this->lazyI18n = $callable;
    }

    public function addIdentityUser(callable $callable): void
    {
        $this->lazyIdentityUser = $callable;
    }

    public function run(): void
    {
        try {
            $i18n = new I18n();
            $request = new Request();
            $response = new Response();
            $responseCache = new ResponseCache($this->configuration, $request, $response, $i18n);

            $this->di->register(new IdentityUser());
            $this->di->register($i18n);
            $this->di->register($this->configuration);
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

            $classCache = new VariableCache($this->directory . '/.cache', 'classes');
            $routeCache = new VariableCache($this->directory . '/.cache', "routes");

            if ($this->configuration->environment === 'development' or !$classCache->exist()) {
                $classScanner = new ClassScanner($this->directory);
                $this->classes = $classScanner->scan();
                $classCache->save($this->classes);
            }
            $this->classes = $classCache->load();

            if ($this->configuration->environment === 'development' or !$routeCache->exist()) {
                $routeScanner = new RouteScanner($this->classes);
                $routes = $routeScanner->scan();
                $routeCache->save($routes);
            }
            $routes = $routeCache->load();
            $this->addRoutes($routes);
            $this->configureIdentityUser();

            $executionRoute = $this->findRoute($request->uri);
            $executionRoute or throw new Exception("APP: route for [$request->uri] doesn't exist", 404);
            $request->addRouteParameters($executionRoute->parameters);

            $result = $this->runCallable($this->beforeRunCallback);
            if ($result == null) {
                $executionRunner = new ExecutionRouteRunner($executionRoute, $this->di);
                $result = $executionRunner->run();
            }

            if ($result instanceof View) {
                $response->body = $result->toHtml();
            } else if ($result instanceof Redirect) {
                $response->location = $result->location;
                $response->body = $result->body;
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

            $this->runCallable($this->afterSuccessfulRunCallback);
        }
        catch (Exception $e)
        {
            $this->runCallable($this->afterFailedRunCallback);

            $code = (!is_int($e->getCode()) or $e->getCode() == 0) ? 500 : $e->getCode();
            if ($code == 401 and $this->configuration->unauthenticatedRedirect) {
                header("Location: {$this->configuration->unauthenticatedRedirect}");
                die;
            }
            if ($code == 403 and $this->configuration->unauthorizedRedirect) {
                header("Location: {$this->configuration->unauthorizedRedirect}");
                die;
            }

            http_response_code($code);

            $errorPage = $this->configuration->errorPages ?? array();
            if (array_key_exists($code, $errorPage)) {
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

    private function configureApp(): void
    {
        if ($this->lazyConfiguration != null) {
            $this->runCallable($this->lazyConfiguration);
        }

        if ($this->configuration->settingsFilePath != null) {
            $filePath = STORM::aliasPath($this->configuration->settingsFilePath);
            $className = $this->configuration->settingsClassName;
            $settings = SettingsLoader::load($className, $filePath);
            $this->di->register($settings);
        }

        if ($this->configuration->errorPages) {
            foreach ($this->configuration->errorPages as $code => $file) {
                $this->configuration->errorPages[$code] = STORM::aliasPath($file);
            }
        }

        if ($this->configuration->aliases == null) {
            $this->configuration->aliases = array();
        }
    }

    private function configureIdentityUser(): void
    {
        if ($this->lazyIdentityUser != null) {
            $user = $this->runCallable($this->lazyIdentityUser);
            $user != null or throw new Exception("AddIdentityUser returned value is null");
            $user instanceof IdentityUser or throw new Exception("AddIdentityUser returned value is not IdentityUser");

            $this->di->registerAs($user, IdentityUser::class);
            $this->di->register($user);
        }
    }

    private function configureI18n(): void
    {
        if ($this->lazyI18n != null) {
            $this->runCallable($this->lazyI18n);
        }
    }

    private function runCallable(callable $callable): mixed
    {
        $reflection = new ReflectionFunction($callable);
        $args = $this->di->resolveReflectionFunction($reflection);
        return $reflection->invokeArgs($args);
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

        $requestSegments = STORM::explode("/", $requestUri);
        foreach ($this->routes as $route => $destination) {
            if (substr_count($route, "/") == substr_count($requestUri, "/")) {
                $routeSegments = STORM::explode("/", $route);
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
        private ExecutionRoute $executionRoute,
        private Di             $di
    )
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

            $this->validateAuthentication($class, $method, $this->executionRoute->pattern);
            $this->validateClaims($class, $method, $this->executionRoute->pattern);

            $constructor = $class->getConstructor();
            if ($constructor) {
                $args = $this->di->resolveReflectionFunction($constructor);
            }
            $obj = $class->newInstanceArgs($args);
            $args = $this->di->resolveReflectionFunction($method);
            return $method->invokeArgs($obj, $args);
        }

        return null;
    }

    private function validateAuthentication(ReflectionClass $class, ReflectionMethod $method, $pattern): void
    {
        if (count($class->getAttributes(Authenticated::class)) or
            count($method->getAttributes(Authenticated::class))) {
            $user = $this->di->resolve(IdentityUser::class);
            if (!$user->isAuthenticated()) {
                throw new Exception("APP: authentication required $pattern", 401);
            }
        }
    }

    private function validateClaims(ReflectionClass $class, ReflectionMethod $method, $pattern): void
    {
        $classAttributes = $class->getAttributes(Claim::class);
        $methodAttributes = $method->getAttributes(Claim::class);
        $classClaims = $this->getClaimsFromAttribute($classAttributes);
        $methodClaims = $this->getClaimsFromAttribute($methodAttributes);

        $requiredClaims = array_merge($classClaims, $methodClaims);

        if ($classAttributes or $methodAttributes) {
            $user = $this->di->resolve(IdentityUser::class);
            if (!$user->hasClaims($requiredClaims)) {
                throw new Exception("APP: Claim required $pattern", 403);
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
    static function set($var, $name, $value)
    {
        $reflection = new ReflectionObject($var);
        $methodName = 'set' . $name;
        if ($reflection->hasMethod($methodName)) {
            $method = $reflection->getMethod($methodName);
            $method->invoke($var, $value);
        } else if ($reflection->hasProperty($name)) {
            $var->$name = $value;
        }
    }
}

class js
{
    static function i18n(array $phrases, $name): string
    {
        $jsarray = "<script type=\"text/javascript\">\n";
        foreach($phrases as $phrase) {
            $translation = _($phrase);
            $jsarray .= $name . "['" . $phrase . "']" . " = '$translation';\n";
        }
        $jsarray .= "</script>\n";

        return $jsarray;
    }
}

class html
{
    static function select($name, $values, $selected = null, $class = null, $required = null, $disabled = null,
                           $autofocus = null, $onChange = null, $onClick = null)
    {
        $html = "";
        $html .= "<select id=\"$name\" name=\"$name\" ";
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

    static function options($options, $selected = null)
    {
        $html = "";
        foreach ($options as $value => $name) {
            $attr = '';
            if ($selected != null && $value == $selected)
                $attr = "selected";
            $html .= "<option value=\"$value\" $attr>$name</option>";
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

    private static function attr($attr, $value = null): string
    {
        if (empty($value)) return '';

        if (is_bool($value) && $value) {
            return "$attr ";
        }

        return "$attr=\"$value\" ";
    }
}

