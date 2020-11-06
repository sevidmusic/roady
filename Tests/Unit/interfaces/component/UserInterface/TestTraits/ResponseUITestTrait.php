<?php

namespace UnitTests\interfaces\component\UserInterface\TestTraits;

use DarlingDataManagementSystem\interfaces\component\UserInterface\ResponseUI as ResponseUIInterface;
use DarlingDataManagementSystem\interfaces\component\Web\Routing\Router as RouterInterface;
use DarlingDataManagementSystem\classes\component\Web\Routing\Router as CoreRouter;
use DarlingDataManagementSystem\interfaces\component\Web\Routing\Request as RequestInterface;
use DarlingDataManagementSystem\classes\component\Web\Routing\Request as CoreRequest;
use DarlingDataManagementSystem\interfaces\component\Web\Routing\Response as ResponseInterface;
use DarlingDataManagementSystem\classes\component\Web\Routing\Response as CoreResponse;
use DarlingDataManagementSystem\interfaces\component\Crud\ComponentCrud as ComponentCrudInterface;
use DarlingDataManagementSystem\classes\component\Crud\ComponentCrud as CoreComponentCrud;
use DarlingDataManagementSystem\interfaces\component\Driver\Storage\StorageDriver as StorageDriverInterface;
use DarlingDataManagementSystem\classes\component\Driver\Storage\StorageDriver as CoreStorageDriver;
use DarlingDataManagementSystem\classes\primary\Storable as CoreStorable;
use DarlingDataManagementSystem\classes\primary\Switchable as CoreSwitchable;
use DarlingDataManagementSystem\interfaces\primary\Positionable as PositionableInterface;
use DarlingDataManagementSystem\classes\primary\Positionable as CorePositionable;
use DarlingDataManagementSystem\classes\component\Web\App as CoreApp;
use DarlingDataManagementSystem\interfaces\component\OutputComponent as OutputComponentInterface;
use DarlingDataManagementSystem\classes\component\OutputComponent as CoreOutputComponent;

trait ResponseUITestTrait
{

    public static function generateTestOutputComponent(): OutputComponentInterface
    {
        $outputComponent = new CoreOutputComponent(
             new CoreStorable(
                'TestOutputComponent',
                self::getTestComponentLocation(),
                self::getTestComponentContainer()
            ),
            new CoreSwitchable(),
            new CorePositionable(),
        );
        $outputComponent->import(['output' => strval(rand(1000000,9999999))]);
        return $outputComponent;
    }

    public static function generateTestResponse(): ResponseInterface
    {
        $request = self::getRequest();
        self::getComponentCrud()->create($request);
        $outputComponent = self::generateTestOutputComponent();
        self::getComponentCrud()->create($outputComponent);
        $response = new CoreResponse(
             new CoreStorable(
                'TestResponse',
                self::getTestComponentLocation(),
                ResponseInterface::RESPONSE_CONTAINER,
            ),
            new CoreSwitchable(),
            new CorePositionable(),
        );
        $response->addRequestStorageInfo($request);
        $response->addOutputComponentStorageInfo($outputComponent);
        return $response;
    }

    public static function setUpBeforeClass(): void
    {
        self::getComponentCrud()->create(self::generateTestResponse());
    }

    private static function readAllFromContainer(string $container): array
    {
        return self::getComponentCrud()->readAll(self::getTestComponentLocation(), self::getTestComponentContainer());
    }

    private static function deleteAllInContainer(string $container): void
    {
        foreach(self::getComponentCrud()->readAll(self::getTestComponentLocation(), $container) as $storable)
        {
            self::getComponentCrud()->delete($storable);
        }
    }

    public static function tearDownAfterClass(): void
    {
        //var_dump(count(self::readAllFromContainer(self::getTestComponentContainer())));
        //var_dump(count(self::readAllFromContainer(ResponseInterface::RESPONSE_CONTAINER)));
        self::deleteAllInContainer(self::getTestComponentContainer());
        self::deleteAllInContainer(ResponseInterface::RESPONSE_CONTAINER);
        //var_dump(count(self::readAllFromContainer(self::getTestComponentContainer())));
        //var_dump(count(self::readAllFromContainer(ResponseInterface::RESPONSE_CONTAINER)));
    }

    private $responseUI;

    protected function setResponseUIParentTestInstances(): void
    {
        $this->setOutputComponent($this->getResponseUI());
        $this->setOutputComponentParentTestInstances();
    }

    public function getResponseUI(): ResponseUIInterface
    {
        return $this->responseUI;
    }

    public function setResponseUI(ResponseUIInterface $responseUI): void
    {
        $this->responseUI = $responseUI;
    }

    public function getResponseUITestArgs(): array
    {
        return [
            new CoreStorable(
                'MockResponseUIName',
                self::getTestComponentLocation(),
                self::getTestComponentContainer()
            ),
            new CoreSwitchable(),
            new CorePositionable(),
            self::getRouter()
        ];
    }

    public function testGetRouterTestMethodReturnsARouterImplemnetationInstance(): void
    {
        $this->assertTrue(
            $this->isProperImplementation(
                RouterInterface::class,
                self::getRouter()
            )
        );
    }

    public static function getRouter(): RouterInterface
    {
        return new CoreRouter(
            new CoreStorable(
                'StandardUITestRouter' . strval(rand(0, 999)),
                self::getTestComponentLocation(),
                self::getTestComponentContainer()
            ),
            new CoreSwitchable(),
            self::getRequest(),
            self::getComponentCrud()
        );
    }

    protected static function getTestComponentLocation(): string
    {
        return 'ResponseUITestComponents';
    }

    protected static function getTestComponentContainer(): string
    {
        return 'TestComponents';
    }

    public static function getRequest(): RequestInterface
    {
        return new CoreRequest(
            new CoreStorable(
                'StandardUICurrentRequest' . strval(rand(0, 999)),
                self::getTestComponentLocation(),
                self::getTestComponentContainer()
            ),
            new CoreSwitchable()
        );
    }

    private static function getComponentCrud(): ComponentCrudInterface
    {
        return new CoreComponentCrud(
            new CoreStorable(
                'StandardUITestComponentCrudForStandardUITestRouter' . strval(rand(0, 999)),
                self::getTestComponentLocation(),
                self::getTestComponentContainer()
            ),
            new CoreSwitchable(),
            self::getStandardStorageDriver()
        );
    }

    private static function getStandardStorageDriver(): StorageDriverInterface
    {
        return new CoreStorageDriver(
            new CoreStorable(
                'StandardUITestStorageDriver' . strval(rand(0, 999)),
                self::getTestComponentLocation(),
                self::getTestComponentContainer()
            ),
            new CoreSwitchable()
        );
    }

    public function testRouterPropertyIsAssignedARouterImplementationInstancePostInstantiation(): void
    {
        $this->assertTrue(
            $this->isProperImplementation(
                RouterInterface::class,
                $this->getResponseUI()->export()['router']
            )
        );
    }

    private function expectedResponses(): array
    {
        return $this->getResponseUI()->export()['router']->getResponses(
            self::getTestComponentLocation(),
            ResponseInterface::RESPONSE_CONTAINER
        );
    }

    private function sortPositionables(PositionableInterface ...$postionables): array
    {
        $sorted = [];
        foreach($postionables as $postionable) {
            while(isset($sorted[strval($postionable->getPosition())]))
            {
                $postionable->increasePosition();
            }
            $sorted[strval($postionable->getPosition())] = $postionable;
        }
        return $sorted;
    }

    private function getRoutersCompoenentCrud(): ComponentCrudInterface
    {
         return $this->getResponseUI()->export()['router']->export()['crud'];
    }

    private function expectedOutput(): string
    {
        $expectedOutput = '';
        $expectedResponses = $this->expectedResponses();
        $sortedResponses = $this->sortPositionables(...$expectedResponses);;
        foreach($sortedResponses as $response)
        {
            $outputComponents = [];
            foreach($response->getOutputComponentStorageInfo() as $storable)
            {
                $component = $this->getRoutersCompoenentCrud()->read($storable);
                if($this->isProperImplementation(OutputComponentInterface::class, $component))
                {
                    array_push($outputComponents, $component);
                }
            }
            $sortedOutputComponents = $this->sortPositionables(...$outputComponents);
            foreach($sortedOutputComponents as $outputComponent)
            {
                $expectedOutput .= $outputComponent->getOutput();
            }
        }
        var_dump($expectedOutput);
        return '';
    }

    public function testGetOutputReturnsCollectiveOutputFromAllResponsesReturnedByRouterSortedByResponsePositionThenOutputComponentPosition(): void
    {
        $this->assertEquals(
            $this->expectedOutput(),
            $this->getResponseUI()->getOutput()
        );
    }
}
