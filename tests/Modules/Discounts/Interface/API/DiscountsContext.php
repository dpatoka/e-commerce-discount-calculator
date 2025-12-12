<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Interface\API;

use App\Tests\TestHelpers\Traits\ApiTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\HttpFoundation\Request;

class DiscountsContext implements Context
{
    use ApiTrait;

    /**
     * @When I send a POST request to :url with body:
     */
    public function iSendPostRequestWithBody(string $url, PyStringNode $body): void
    {
        $request = Request::create(
            $url,
            'POST',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $body->getRaw()
        );

        $this->response = $this->kernel->handle($request);
    }
}
