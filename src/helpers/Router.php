<?php
/**
 * Router - Basit bir URL yönlendirme sınıfı
 * 
 * Bu sınıf, HTTP isteklerini uygun controller'lara yönlendirir
 */
class Router
{
    private $routes = [];
    private $notFoundCallback;
    
    /**
     * GET metodu için bir route ekler
     * 
     * @param string $path URL yolu
     * @param callable|array $callback Controller ve metod bilgisi
     */
    public function get($path, $callback)
    {
        $this->addRoute('GET', $path, $callback);
    }
    
    /**
     * POST metodu için bir route ekler
     * 
     * @param string $path URL yolu
     * @param callable|array $callback Controller ve metod bilgisi
     */
    public function post($path, $callback)
    {
        $this->addRoute('POST', $path, $callback);
    }
    
    /**
     * Route ekler
     * 
     * @param string $method HTTP metodu (GET, POST)
     * @param string $path URL yolu
     * @param callable|array $callback Controller ve metod bilgisi
     */
    private function addRoute($method, $path, $callback)
    {
        // URL'den parametreleri yakalayacak regex oluştur
        $pattern = str_replace('/', '\/', $path);
        $pattern = '/^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $pattern) . '$/';
        
        $this->routes[$method][$pattern] = [
            'path' => $path,
            'callback' => $callback
        ];
    }
    
    /**
     * 404 sayfası için callback ekler
     * 
     * @param callable $callback Controller ve metod bilgisi
     */
    public function notFound($callback)
    {
        $this->notFoundCallback = $callback;
    }
    
    /**
     * URL'yi çözümler ve uygun controller'a yönlendirir
     */
    public function resolve()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // URL sonundaki / karakterini kaldır (varsa)
        $uri = rtrim($uri, '/');
        
        // Eğer URL boşsa, ana sayfaya yönlendir
        if (empty($uri)) {
            $uri = '/';
        }
        
        // URL ile eşleşen route'u bul
        $callback = $this->findRoute($method, $uri);
        
        // Eğer route bulunamazsa 404 sayfasına yönlendir
        if (!$callback) {
            if (is_callable($this->notFoundCallback)) {
                call_user_func($this->notFoundCallback);
            } else {
                header("HTTP/1.0 404 Not Found");
                require_once __DIR__ . '/../views/404.php';
            }
            return;
        }
        
        // Controller ve metod bilgisini al
        $controller = $callback['controller'];
        $method = $callback['method'];
        $params = $callback['params'];
        
        // Controller sınıfını yükle
        require_once __DIR__ . "/../controllers/{$controller}.php";
        
        // Controller nesnesini oluştur ve metodu çağır
        $controllerInstance = new $controller();
        
        // Named parametreleri sıralı array'e çevir
        $orderedParams = array_values($params);
        
        call_user_func_array([$controllerInstance, $method], $orderedParams);
    }
    
    /**
     * URL ile eşleşen route'u bulur
     * 
     * @param string $method HTTP metodu
     * @param string $uri URL yolu
     * @return array|false Controller ve metod bilgisi veya false
     */
    private function findRoute($method, $uri)
    {
        if (!isset($this->routes[$method])) {
            return false;
        }
        
        foreach ($this->routes[$method] as $pattern => $route) {
            if (preg_match($pattern, $uri, $matches)) {
                // Controller ve metod bilgisini al
                $callback = $route['callback'];
                $controller = is_array($callback) ? $callback[0] : null;
                $method = is_array($callback) ? $callback[1] : null;
                
                // URL'den yakalanan parametreleri al
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                return [
                    'controller' => $controller,
                    'method' => $method,
                    'params' => $params
                ];
            }
        }
        
        return false;
    }
}
?>