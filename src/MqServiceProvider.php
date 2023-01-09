<?php

namespace Dhf\Mq;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Dhf\Mq\Conectors\NullConnector;
use Dhf\Mq\Conectors\RedisConnector;
use Dhf\Mq\Conectors\KafkaConnector;

class MqServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
        $this->registerManager();
        $this->registerConnection();
    }

    protected function registerConfig()
    {
        $source = realpath(__DIR__ . '/../config/mq.php');
        
        // if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
        //     $this->publishes([$source => config_path('mq.php')]);
        // } elseif ($this->app instanceof LumenApplication) {
        //     $this->app->configure($source, 'mq');
        // }

        if ($this->app instanceof LumenApplication) {
            $this->app->configure($source, 'mq');
        }

        $this->mergeConfigFrom($source, 'mq');
    }

    protected function registerManager()
    {
        $this->app->singleton('mq', function($app) {
            return tap(new MqManager($app), function($manager) {
                $this->registerConnectors($manager);
            });
        });
    }

    protected function registerConnection()
    {
        $this->app->singleton('mq.connection', function($app) {
            return $app['mq']->connection();
        });
    }

    public function registerConnectors($manager)
    {
        foreach (['Null', 'Redis', 'Kafka'] as $connector) {
            $this->{"register{$connector}Connector"}($manager);
        }
    }

    protected function registerNullConnector($manager)
    {
        $manager->addConnector('null', function() {
            return new NullConnector;
        });
    }

    protected function registerRedisConnector($manager)
    {
        $manager->addConnector('redis', function() {
            return new RedisConnector($this->app['redis']);
        });
    }

    protected function registerKafkaConnector($manager)
    {
        $manager->addConnector('kafka', function() {
            return new KafkaConnector();
        });
    }
}
