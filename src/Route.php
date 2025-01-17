<?php
namespace EasyScf;
class Route {
    public $path;
    public $method;
    public $params;
    public $body;
    public $routes;
    public $item;

    public function __construct($event, $functionName)
    {
        //去除版本号

        $path = $event->path ?: $event->Message;
        //正则匹配
        $pattern = '/.*?\/(test|release)\/' . $functionName . '(.*)/';
        preg_match($pattern, $path, $matches);
        if ($matches[1]) {// 存在环境，非定时任务
            $path = $matches[2] ?: '/';
        } else {// 定时任务
            $path = $event->Message;
        }

        $this->method   = $event->httpMethod ?: 'GET';
        $this->params   = json_decode(json_encode($event->queryString), true);//获取param参数
        $this->body     = json_decode($event->body, true);//获取post参数
        $this->routes   = require 'routes.php';
    }

    public function init() {
        //路径
        $path       = $this->path ?: '/';

        foreach ($this->routes[$this->method] as $pattern => $functionStr) {
            //替换'/'为'\/'，并获取参数名
            $pattern = str_replace('/', '\/', $pattern);
            preg_match_all('/\{([a-zA-Z]+)\}/', $pattern, $matches);
            foreach ($matches[1] as $k => $v) {
                $paramsKey[] = $v;
            }

            //替换参数为正则表达式
            $pattern = preg_replace('/\{[a-zA-Z]+\}/', '([a-zA-Z0-9-_]+)', $pattern);

            //匹配路由
            if (preg_match("/^{$pattern}$/", $path, $matches)) {

                //设置参数
                if ($paramsKey) {
                    foreach ($paramsKey as $k => $v) {
                        $params[$v] = $matches[$k + 1];
                    }
                    $this->params = array_merge($this->params, $params);
                }
                $functionArr = explode('/', $functionStr);
                break;
            }
        }

        // 未定义的路由
        if (!$functionArr) {
            return false;
        }

        return [
            true,
            $this->camelCase($this->unCamelCase($functionArr[0])),
            lcfirst($this->camelCase($this->unCamelCase($functionArr[1]))),
            $this->params,
            $this->body,
        ];
    }

    /**
     * 下划线转驼峰
     *
     * @param string $string    原字符
     * @param string $separator 分隔符
     *
     * @return string
     */
    function camelCase(string $string, string $separator = '-')
    {
        $string = $separator . str_replace($separator, ' ', strtolower($string));
        return str_replace(' ', '', ucwords(ltrim($string, $separator)));
    }

    /**
     * 驼峰转下划线
     *
     * @param string $camelCaps
     * @param string $separator
     *
     * @return string
     */
    function unCamelCase(string $camelCaps, string $separator = '-')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}