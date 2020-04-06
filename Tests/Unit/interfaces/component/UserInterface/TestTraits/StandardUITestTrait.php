<?php

namespace UnitTests\interfaces\component\UserInterface\TestTraits;

use DarlingCms\classes\component\Crud\ComponentCrud;
use DarlingCms\classes\component\Driver\Storage\Standard as StorageDriver;
use DarlingCms\classes\component\OutputComponent;
use DarlingCms\classes\component\Template\UserInterface\StandardUITemplate;
use DarlingCms\classes\component\Web\Routing\Request;
use DarlingCms\classes\component\Web\Routing\Response;
use DarlingCms\classes\component\Web\Routing\Router;
use DarlingCms\classes\primary\Positionable;
use DarlingCms\classes\primary\Storable;
use DarlingCms\classes\primary\Switchable;
use DarlingCms\interfaces\component\Crud\ComponentCrud as ComponentCrudInterface;
use DarlingCms\interfaces\component\UserInterface\StandardUI;
use Exception;

trait StandardUITestTrait
{

    private $standardUI;
    private $router;
    private $currentRequest;
    private $generateComponentCalls = 0;

    public function getStandardUIContainer(): string
    {
        return 'StandardUITestStandardUIContainer';
    }

    public function tearDown(): void
    {
        // @todo : Working on fixing this...
        foreach ($this->getStoredComponents($this->getComponentLocation(), $this->getOutputComponentContainer()) as $storedComponent) {
            $this->getStandardUITestRouter()->getCrud()->delete($storedComponent);
        }
        foreach ($this->getStoredComponents($this->getComponentLocation(), $this->getStandardUITemplateContainer()) as $storedComponent) {
            $this->getStandardUITestRouter()->getCrud()->delete($storedComponent);
        }
        foreach ($this->getStoredComponents($this->getComponentLocation(), $this->getResponseContainer()) as $storedComponent) {
            $this->getStandardUITestRouter()->getCrud()->delete($storedComponent);
        }
        foreach ($this->getStoredComponents($this->getComponentLocation(), $this->getRequestContainer()) as $storedComponent) {
            $this->getStandardUITestRouter()->getCrud()->delete($storedComponent);
        }
    }

    protected function getStoredComponents(string $location, string $container): array
    {
        return $this->getStandardUITestRouter()->getCrud()->readAll(
            $location,
            $container
        );
    }

    public function getStandardUITestRouter(): Router
    {
        if (isset($this->router)) {
            return $this->router;
        }
        $this->router = new Router(
            new Storable(
                'StandardUITestRouter',
                $this->getComponentLocation(),
                $this->getRouterContainer()
            ),
            new Switchable(),
            $this->getCurrentRequest(),
            $this->getComponentCrudForRouter()
        );
        return $this->router;
    }

    public function getComponentLocation(): string
    {
        return 'StandardUITestComponentsLocation';
    }

    public function getRouterContainer(): string
    {
        return "StandardUITestRouterContainer";
    }

    public function getCurrentRequest(): Request
    {
        if (isset($this->currentRequest) === true) {
            //var_dump($this->currentRequest->getUrl());
            return $this->currentRequest;
        }
        $this->currentRequest = new Request(
            new Storable(
                'StandardUICurrentRequest',
                $this->getComponentLocation(),
                $this->getRequestContainer()
            ),
            new Switchable()
        );
        $this->currentRequest->import(['url' => $this->getRandomUrl()]);
        $this->getStandardUITestRouter()->getCrud()->create($this->currentRequest);
        return $this->currentRequest;
    }

    public function getRequestContainer(): string
    {
        return "StandardUITestRequestContainer";
    }

    private function getRandomUrl(): string
    {
        switch (rand(0, 1)) {
            case 0:
                return 'http://' . $this->randChars(rand(3, 4)) . '.' . $this->randChars(rand(3, 4)) . '/' . $this->randChars(rand(3, 9)) . '?' . $this->randChars(rand(4, 5));
            default:
                /** @noinspection PhpUndefinedMethodInspection */
                return $this->currentRequest->getUrl();
        }
    }

    private function randChars(int $limit): string
    {
        try {
            return bin2hex(random_bytes($limit));
        } catch (Exception $e) {
            return strval(rand(1000000, 9999999));
        }
    }

    private function getComponentCrudForRouter(): ComponentCrudInterface
    {
        if (isset($this->router) === true) {
            return $this->getStandardUITestRouter()->getCrud();
        }
        return new ComponentCrud(
            new Storable(
                'StandardUITestComponentCrudForStandardUITestRouter',
                $this->getComponentLocation(),
                $this->getComponentCrudContainer()
            ),
            new Switchable(),
            new StorageDriver(
                new Storable(
                    'StandardUITestStorageDriver',
                    $this->getComponentLocation(),
                    $this->getStorageDriverContainer()
                ),
                new Switchable()
            )
        );
    }

    public function getComponentCrudContainer(): string
    {
        return "StandardUITestComponentCruds";
    }

    public function getStorageDriverContainer(): string
    {
        return "StandardUITestStorageDrivers";
    }

    public function getOutputComponentContainer(): string
    {
        return "StandardUITestOutputComponentContainer";
    }

    protected function getStandardUITemplateContainer(): string
    {
        return 'StandardUITestStandardUITemplateContainer';
    }

    protected function getResponseContainer(): string
    {
        return 'StandardUITestResponseContainer';
    }

    public function testRouterIsSetPostInstantiation(): void
    {
        $this->assertEquals
        ("DarlingCms\classes\component\Web\Routing\Router",
            $this->getStandardUI()->export()['router']->getType()
        );
    }

    public function getStandardUI(): StandardUI
    {
        return $this->standardUI;
    }

    public function setStandardUI(StandardUI $standardUI): void
    {
        $this->standardUI = $standardUI;
    }

    public function testGetTemplatesAssignedToResponsesReturnsArrayOfStandardUITemplates(): void
    {
        foreach (
            $this->getStandardUI()->getTemplatesAssignedToResponses() as $template) {
            $this->assertTrue(in_array('DarlingCms\interfaces\component\Template\UserInterface\StandardUITemplate', class_implements($template)));
        }
    }

    public function testGetTemplatesAssignedToResponsesReturnsArrayOfAllStandardUITemplatesAssignedToAllResponsesToCurrentRequest(): void
    {
        $templates = [];
        foreach ($this->getStoredResponses() as $response) {
            if ($response->respondsToRequest(
                    $this->getCurrentRequest(),
                    $this->getStandardUITestRouter()->getCrud()
                ) === true) {
                foreach ($response->getTemplateStorageInfo() as $storable) {
                    $template = $this->getStandardUITestRouter()->getCrud()->read($storable);
                    while (
                        isset(
                            $templates[strval(
                                $template->getPosition()
                            )]) === true) {
                        $template->increasePosition();
                    }
                    $templates[strval($template->getPosition())] = $template;
                }
            }
        }
        $this->assertEquals($templates, $this->getStandardUI()->getTemplatesAssignedToResponses());
    }

    private function getStoredResponses(): array
    {
        $responses = [];
        foreach ($this->getStandardUITestRouter()->getCrud()->readAll(
            $this->getComponentLocation(),
            $this->getResponseContainer()
        ) as $response) {
            array_push($responses, $response);
        }
        return $responses;
    }

    public function testGetOutputComponentsAssignedToResponsesReturnsArrayOfOutputComponents()
    {
        foreach (
            $this->getStandardUI()->getOutputComponentsAssignedToResponses() as $outputComponentTypes) {
            foreach ($outputComponentTypes as $outputComponent) {
                $this->assertTrue(
                    in_array(
                        'DarlingCms\interfaces\component\OutputComponent',
                        class_implements($outputComponent)
                    )
                );

            }
        }
    }

    private function devStoredComponentInfo(): void
    {
        var_dump(
            [
                'Current Request Url' => $this->getCurrentRequest()->getUrl(),
                '# Sorted Requests' => count(
                    $this->getStoredComponents(
                        $this->getComponentLocation(),
                        $this->getRequestContainer()
                    )
                ),
                '# Sorted Templates' => count(
                    $this->getStoredComponents(
                        $this->getComponentLocation(),
                        $this->getStandardUITemplateContainer()
                    )
                ),
                '# Sorted Output Components' => count(
                    $this->getStoredComponents(
                        $this->getComponentLocation(),
                        $this->getOutputComponentContainer()
                    )
                ),
                '# Sorted Responses' => count(
                    $this->getStoredComponents(
                        $this->getComponentLocation(),
                        $this->getResponseContainer()
                    )
                ),
            ]
        );

        foreach ($this->getStoredComponents($this->getComponentLocation(), $this->getOutputComponentContainer()) as $storedComponent) {
            var_dump(
                $storedComponent->getName(),
                $storedComponent->getUniqueId(),
                $storedComponent->getLocation(),
                $storedComponent->getContainer(),
                $storedComponent->getType()
            );
        }

    }

    public function testGetOutputComponentsAssignedToResponsesReturnsArrayWhoseTopLevelIndexesAreValidOutputComponentTypes()
    {
        foreach (
            $this->getStandardUI()->getOutputComponentsAssignedToResponses() as $outputComponentType => $outputComponents) {
            $this->assertTrue(
                in_array(
                    'DarlingCms\interfaces\component\OutputComponent',
                    class_implements($outputComponentType)
                )
            );
        }
    }

    public function testGetOutputComponentsAssignedToResponsesReturnsArrayWhoseSecondLevelIndexesAreNumericStrings()
    {
        foreach (
            $this->getStandardUI()->getOutputComponentsAssignedToResponses() as $outputComponentTypes) {
            foreach ($outputComponentTypes as $index => $outputComponent) {
                $this->assertTrue(
                    is_numeric($index)
                );

            }
        }
    }

    public function testGetOutputComponentsAssignedToResponsesReturnsArrayOfAllOutputComponentsAssignedToAllResponsesToCurrentRequest(): void
    {
        $outputComponents = [];
        foreach ($this->getStoredResponses() as $response) {
            if ($response->respondsToRequest(
                    $this->getCurrentRequest(),
                    $this->getStandardUITestRouter()->getCrud()
                ) === true) {
                foreach (
                    $response->getOutputComponentStorageInfo() as $storable
                ) {
                    $outputComponent = $this->getStandardUITestRouter()->getCrud()->read($storable);
                    if (isset($outputComponents[$outputComponent->getType()][strval($outputComponent->getPosition())]) === true) {
                        $outputComponent->increasePosition();
                    }
                    $outputComponents[$outputComponent->getType()][strval($outputComponent->getPosition())] = $outputComponent;
                }
            }
        }
        $this->assertEquals(
            $outputComponents,
            $this->getStandardUI()->getOutputComponentsAssignedToResponses(
                $this->getComponentLocation(),
                $this->getResponseContainer()
            )
        );
    }

    public function testGetTemplatesAssignedToResponsesReturnsArrayWhoseIndexesAreNumericStrings()
    {
        foreach (
            $this->getStandardUI()->getTemplatesAssignedToResponses() as $index => $templates) {
            $this->assertTrue(is_numeric($index));
        }
    }

    public function testGetOutputReturnsCollectiveOutputFromOutputComponentsOrganizedByTemplateThenOutputComponentPosition()
    {
        // @todo implement this test | remove dev assertion
        $expectedOutput = '';
        foreach ($this->getStandardUI()->getTemplatesAssignedToResponses() as $template) {
            foreach ($template->getTypes() as $type) {
                foreach ($this->getStandardUI()->getOutputComponentsAssignedToResponses() as $outputComponentType) {
                    foreach ($outputComponentType as $outputComponent) {
                        $expectedOutput .= $outputComponent->getOutput();
                    }
                }
            }
        }
        $this->assertEquals($expectedOutput, $this->getStandardUI()->getOutput());
    }

    protected function generateStoredTestComponents()
    {
        // @devNote: The generateStoredOutputComponent() and generateStandardUITemplate() methods are call from with generateStoredResponse()
        $this->generateComponentCalls++;
        $this->generateStoredResponse();

    }

    protected function generateStoredResponse(): Response
    {
        $response = new Response(
            new Storable('StandardUITestResponse',
                $this->getComponentLocation(),
                $this->getResponseContainer()
            ),
            new Switchable()
        );
        $response->addTemplateStorageInfo($this->generateStoredStandardUITemplate());
        $response->addOutputComponentStorageInfo($this->generateStoredOutputComponent());
        $response->addRequestStorageInfo($this->getCurrentRequest());
        $this->getStandardUITestRouter()->getCrud()->create($response);
        return $response;
    }

    private function generateStoredStandardUITemplate(): StandardUITemplate
    {
        $standardUITemplate = new StandardUITemplate(
            new Storable(
                'StandardUITestTemplate',
                $this->getComponentLocation(),
                $this->getStandardUITemplateContainer()
            ),
            new Switchable(),
            new Positionable((rand(0, 100) / 100))
        );
        $standardUITemplate->addType($this->generateStoredOutputComponent(false));
        $this->getStandardUITestRouter()->getCrud()->create($standardUITemplate);
        return $standardUITemplate;
    }

    private function generateStoredOutputComponent(bool $saveToStorage = true): OutputComponent
    {
        $outputComponent = new OutputComponent(
            new Storable(
                '',
                $this->getComponentLocation(),
                $this->getOutputComponentContainer()
            ),
            new Switchable(),
            new Positionable((rand(0, 100) / 100))
        );
        $outputComponent->import(['output' => 'Some plain text' . strval(rand(10000, 99999))]);
        if ($saveToStorage === true) {
            $this->getStandardUITestRouter()->getCrud()->create($outputComponent);
        }
        return $outputComponent;
    }

    protected function setStandardUIParentTestInstances(): void
    {
        $this->setOutputComponent($this->getStandardUI());
        $this->setOutputComponentParentTestInstances();
    }
}
