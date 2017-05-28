<?php

namespace App\Providers;

use App\Managers\BaseModelManager;
use App\Managers\BaseResponseManager;
use App\Managers\ModelManagerInterface;
use App\Managers\ResponseManagerInterface;
use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

class ManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ModelManagerInterface::class, BaseModelManager::class);

        $this->app->bind(ResponseManagerInterface::class, function() {
            return new BaseResponseManager(
                (new Manager())->setSerializer(new JsonApiSerializer())
            );
        });
    }
}
