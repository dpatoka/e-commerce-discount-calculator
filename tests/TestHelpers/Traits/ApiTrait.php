<?php

declare(strict_types=1);

namespace App\Tests\TestHelpers\Traits;

use App\Kernel;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

trait ApiTrait
{
    private KernelInterface $kernel;

    private Response $response;

    /**
     * @AfterScenario
     */
    public function tearDown(): void
    {
        $this->kernel->shutdown();
    }

    /**
     * @BeforeScenario
     */
    public function bootKernel(): void
    {
        $this->kernel = new Kernel('test', true);
        $this->kernel->boot();
    }

    /**
     * @Then the response status code should be :code
     */
    public function theResponseStatusCodeShouldBe(int $code): void
    {
        Assert::assertEquals($code, $this->response->getStatusCode());
    }

    /**
     * @Then the JSON node :node should be equal to :value
     */
    public function theJsonNodeShouldBeEqualTo(string $node, string $value): void
    {
        $data = $this->getResponseJson();
        $current = $this->getDataFromNode($node, $data);

        Assert::assertEquals($value, $current);
    }

    /**
     * @Then the JSON node :node should have :count elements
     */
    public function theJsonNodeShouldHaveElements(string $node, int $count): void
    {
        $data = $this->getResponseJson();
        $elements = $this->getDataFromNode($node, $data);

        Assert::assertIsArray($elements);
        Assert::assertEquals($count, count($elements));
    }

    /**
     * @Then the JSON node :node should be numeric
     */
    public function theJsonNodeShouldBeNumeric(string $node): void
    {
        $data = $this->getResponseJson();
        $current = $this->getDataFromNode($node, $data);

        Assert::assertIsNumeric($current);
    }

    /**
     * @Then the response should be JSON
     */
    public function theResponseShouldBeJson(): void
    {
        $content = $this->getContent();
        Assert::assertJson($content);
    }

    private function getDataFromNode(string $node, mixed $data): mixed
    {
        $keys = explode('.', $node);
        $current = $data;

        foreach ($keys as $key) {
            Assert::assertIsArray($current, 'Current value must be an array to access key: ' . $key);
            Assert::assertArrayHasKey($key, $current);

            $current = $current[$key];
        }

        return $current;
    }

    /**
     * @return array<mixed,mixed>
     */
    private function getResponseJson(): array
    {
        $content = $this->getContent();
        $decoded = json_decode($content, true);

        if (!is_array($decoded)) {
            return [];
        }

        return $decoded;
    }

    private function getContent(): string
    {
        $content = $this->response->getContent();
        Assert::assertNotFalse($content);

        return $content;
    }
}
