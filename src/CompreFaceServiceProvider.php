<?php

namespace Aoding9\CompreFace;

use Illuminate\Support\ServiceProvider;

class CompreFaceServiceProvider extends ServiceProvider {
    public function boot() {
        // 将配置文件发布到/config目录下
        if ($this->app->runningInConsole()) {
            $this->publishes([
                                 __DIR__ . '/config/compreFace.php' => config_path('compreFace.php'),
                             ]);
        }
    }

    public function register() {
        // 合并配置文件
        $this->mergeConfigFrom(__DIR__ . '/config/compreFace.php', 'compreFace');

        // 注册单例到容器，把config传进去
        $this->app->singleton(CompreFace::class, function($app) {
            return new CompreFace(config('compreFace.server'),config('compreFace.port'),config('compreFace.options'));
        });

        // 注册别名
        $this->app->alias(CompreFace::class, 'compreFace');
    }

    /**
     * @Desc 延迟注册
     * @return string[]
     */
    public function provides() {
        return [CompreFace::class, 'compreFace'];
    }
}
