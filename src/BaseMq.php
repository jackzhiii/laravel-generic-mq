<?php

namespace Dhf\Mq;

class BaseMq
{
    public function setConnectionName($name)
    {
        $this->connectionName = $name;

        return $this;
    }

    public function setContainer($app)
    {
        $this->app = $app;
    }
}
