<?php

namespace Test\System;

use Asynchronicity\PHPUnit\Asynchronicity;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\MinkContext;
use PHPUnit\Framework\Assert;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class FeatureContext extends MinkContext
{
    use Asynchronicity;

    /**
     * The name of the most recently discussed product
     *
     * @var string
     */
    private $product;

    /**
     * @BeforeScenario
     */
    public function clearDatabase(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(Finder::create()->files()->name('*.json')->in(__DIR__ . '/../../var/'));
    }

    /**
     * @AfterScenario
     */
    public function waitForConsumersToFinish(): void
    {
        // this is quite arbitrary, but should let consumers finish their work, before we'd start running the next scenario
        sleep(2);
    }

    /**
     * @Given the catalog has a product :productName
     * @param string $productName
     */
    public function theCatalogHasAProduct(string $productName): void
    {
        $this->visit('http://catalog.localhost/createProduct');
        $this->assertSuccessfulResponse();
        $this->fillField('name', $productName);
        $this->pressButton('Create');
        $this->assertUrlRegExp('#/listProducts#');

        $this->product = $productName;
    }

    /**
     * @Then I should see that :productName has a stock level of :stockLevel
     * @param string $productName
     * @param string $stockLevel
     */
    public function iShouldSeeThatHasAStockLevelOf(string $productName, string $stockLevel): void
    {
        self::assertEventually(function () use ($productName, $stockLevel) {
            $this->visit('http://dashboard.localhost/');
            $this->assertSuccessfulResponse();

            $nameField = $this->findOrFail('css', '.product-name:contains("' . addslashes($productName) . '")');
            $actualStockLevel = (int)$this->findOrFail('css', '.stock-level', $nameField->getParent())->getText();

            assertEquals($stockLevel, $actualStockLevel);
        });
    }

    /**
     * @Given we have purchased and received :quantity items of this product
     * @param string $quantity
     */
    public function weHavePurchasedAndReceivedItemsOfThisProduct(string $quantity): void
    {
        self::assertEventually(function () use ($quantity) {
            $this->visit('http://purchase.localhost/createPurchaseOrder');
            $this->assertSuccessfulResponse();

            $this->selectOption('Product', $this->product);
            $this->fillField('Quantity', $quantity);
            $this->pressButton('Order');
        });

        self::assertEventually(function () {
            $this->visit('http://purchase.localhost/receiveGoods');
            $this->assertSuccessfulResponse();
            $this->pressButton('Receive');
        });
    }

    /**
     * @Given we have sold and delivered :quantity items of this product
     * @param string $quantity
     */
    public function weHaveSoldAndDeliveredItemsOfThisProduct(string $quantity): void
    {
        self::assertEventually(function () use ($quantity) {
            $this->visit('http://sales.localhost/createSalesOrder');
            $this->assertSuccessfulResponse();
            $this->selectOption('Product', $this->product);
            $this->fillField('Quantity', $quantity);
            $this->pressButton('Order');
        });

        self::assertEventually(function () {
            $this->visit('http://sales.localhost/deliverSalesOrder');
            $this->assertSuccessfulResponse();
            $this->pressButton('Deliver');
        });
    }

    /**
     * Proxy for `$this->find(...)`, which fails if no element matched the given locator.
     *
     * @param string $selector
     * @param string $locator
     * @param NodeElement|null $parentNode
     * @return NodeElement
     */
    private function findOrFail(string $selector, string $locator, NodeElement $parentNode = null): NodeElement
    {
        $element = ($parentNode ?: $this->getSession()->getPage())->find($selector, $locator);

        if (!$element instanceof NodeElement) {
            throw new RuntimeException(sprintf('Could not find %s using %s selector', $locator, $selector));
        }

        return $element;
    }

    private function assertSuccessfulResponse(): void
    {
        $this->assertSession();

        Assert::assertEquals(
            200,
            intval($this->getSession()->getStatusCode()),
            sprintf(
                "Expected a successful response. Response body: \n\n%s",
                $this->getReadableResponseBody()
            )
        );
    }

    private function getReadableResponseBody(): string
    {
        $body = $this->getSession()->getPage()->find('css', 'body');

        if ($body instanceof NodeElement) {
            return $body->getText();
        }

        return $this->getSession()->getPage()->getText();
    }
}
