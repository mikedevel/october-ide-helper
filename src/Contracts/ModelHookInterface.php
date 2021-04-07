<?php

namespace Mikedevs\OctoberIdeHelper\Contracts;

use Mikedevs\OctoberIdeHelper\Console\ModelsCommand;
use Illuminate\Database\Eloquent\Model;

interface ModelHookInterface
{
    public function run(ModelsCommand $command, Model $model): void;
}
