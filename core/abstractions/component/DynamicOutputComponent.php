<?php

namespace DarlingDataManagementSystem\abstractions\component;

use DarlingDataManagementSystem\interfaces\primary\Storable as StorableInterface;
use DarlingDataManagementSystem\interfaces\primary\Switchable as SwitchableInterface;
use DarlingDataManagementSystem\interfaces\primary\Positionable as PositionableInterface;
use DarlingDataManagementSystem\abstractions\component\OutputComponent as OutputCompoenentBase;
use DarlingDataManagementSystem\interfaces\component\DynamicOutputComponent as DynamicOutputComponentInterface;
use RuntimeException;

abstract class DynamicOutputComponent extends OutputCompoenentBase implements DynamicOutputComponentInterface
{

    private string $appDirectoryName;

    public function __construct(StorableInterface $storable, SwitchableInterface $switchable, PositionableInterface $positionable, string $appDirectoryName)
    {
        parent::__construct($storable, $switchable, $positionable);
        $this->appDirectoryName = $appDirectoryName;
        if(
            !is_dir(
                $this->expectedAppDirectoryPath()
            )
        )
        {
            throw new RuntimeException('App directory missing.');
        }
    }

    private function expectedAppDirectoryPath(): string
    {
        return str_replace(
            $this->currentSubDirectory(),
            'Apps' . DIRECTORY_SEPARATOR . $this->appDirectoryName,
            __DIR__
        );
    }

    private function currentSubDirectory(): string
    {
        return 'core' . DIRECTORY_SEPARATOR . 'abstractions' . DIRECTORY_SEPARATOR . 'component';
    }

    public function getSharedDynamicOutputFilesDirectoryPath(): string
    {
        $sharedDynamicOutputFilesDir = str_replace($this->currentSubDirectory(), 'SharedDynamicOutput' . DIRECTORY_SEPARATOR, __DIR__);
        if(!is_dir($sharedDynamicOutputFilesDir))
        {
            throw new RuntimeException('The Shared Dynamic Output directory does not exist.');
        }
        return $sharedDynamicOutputFilesDir;
    }


    public function getAppsDynamicOutputFilesDirectoryPath(): string
    {
        $appDynamicOutputFileDir = str_replace($this->currentSubDirectory(), 'Apps'. DIRECTORY_SEPARATOR . $this->appDirectoryName . DIRECTORY_SEPARATOR . 'DynamicOutput', __DIR__);
        if(!is_dir($appDynamicOutputFileDir))
        {
            throw new RuntimeException('The App\'s Dynamic Output directory does not exist.');
        }

        return $appDynamicOutputFileDir;
    }
}
