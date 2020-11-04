<?php

namespace DarlingDataManagementSystem\abstractions\component\UserInterface;

use DarlingDataManagementSystem\interfaces\primary\Storable;
use DarlingDataManagementSystem\interfaces\primary\Switchable;
use DarlingDataManagementSystem\interfaces\primary\Positionable;
use DarlingDataManagementSystem\abstractions\component\OutputComponent as CoreOutputComponent;
use DarlingDataManagementSystem\interfaces\component\UserInterface\ResponseUI as ResponseUIInterface;
use DarlingDataManagementSystem\interfaces\component\Web\Routing\Router as RouterInterface;

abstract class ResponseUI extends CoreOutputComponent implements ResponseUIInterface
{

    private RouterInterface $router;

    public function __construct(Storable $storable, Switchable $switchable, Positionable $positionable, RouterInterface $router)
    {
        parent::__construct($storable, $switchable, $positionable);
        $this->router = $router;
    }

}
