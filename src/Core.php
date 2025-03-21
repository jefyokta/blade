<?php

/**
 * Oktaax - Real-time Websocket and HTTP Server using Swoole
 *
 * @package Oktaax
 * @author Jefyokta
 * @license MIT License
 * 
 * @link https://github.com/jefyokta/oktaax
 *
 * @copyright Copyright (c) 2024, Jefyokta
 *
 * MIT License
 *
 * Copyright (c) 2024 Jefyokta
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */





namespace JefyOkta\Blade;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

class Core
{

    /**
     * 
     * @var  \Illuminate\View\Factory $viewFactory
     * 
     */
    private $viewFactory;



    /**
     * @param string $viewsDir
     * @param string $cacheDir
     * @param ?array $config
     * 
     */
    public function __construct(string $viewsDir, string $cacheDir)
    {
        $container = new Container();
        $filesystem = new Filesystem();
        $dispatcher = new \Illuminate\Events\Dispatcher($container);


        $bladeCompiler = new BladeCompiler($filesystem, $cacheDir);


        $engineResolver = new EngineResolver();
        $engineResolver->register('blade', function () use ($bladeCompiler, $filesystem) {
            return new CompilerEngine($bladeCompiler, $filesystem);
        });
        $engineResolver->register('php', function () use ($filesystem) {
            return new \Illuminate\View\Engines\PhpEngine($filesystem);
        });

        $viewFinder = new FileViewFinder($filesystem, [$viewsDir]);
        $this->viewFactory = new Factory($engineResolver, $viewFinder, $dispatcher);
        $this->registerDirectives($bladeCompiler);

    }
    private function registerDirectives(BladeCompiler $compiler)
    {

        
        $compiler->directive('method', function ($expression) {
            return " echo \\Oktaax\\Blade\\BladeDirectives::methodField($expression); ?>";
        });


        /**
         * @example
         * @requestHas("page")
         * {{ $request->all()->page }}
         * @endRequestHas
         * check if request has $key
         */
        $compiler->directive("requestHas", function ($key) {
            return " if(xrequest()->has($key)): ?>";
        });


        $compiler->directive("endRequestHas", function () {
            return " endif; ?>";
        });

        /**
         * 
         * 
         */

        $compiler->directive("hasMessage", function () {
            return " if(!is_null(xrequest()->cookie('X-Message'))): ?>\n \$message = xrequest()->cookie('X-Message'): ?>";
        });

        $compiler->directive("endHasMessage", function () {
            return " endif; ?>";
        });

        $compiler->directive("hasErrorMessage", function () {
            return " if(!is_null(xrequest()->cookie('X-ErrMessage'))): ?>\n \$message = xrequest()->cookie('X-Message'): ?>";
        });

        $compiler->directive("endHasErrorMessage", function () {
            return " endif; ?>";
        });



    }

    public function render(string $view, array $data = []): string
    {


        return $this->viewFactory->make($view, $data)->render();
    }
}