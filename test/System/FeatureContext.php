<?php

namespace Test\System;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class FeatureContext extends MinkContext
{
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
     * @Given the catalog has a product :productName
     * @param string $productName
     */
    public function theCatalogHasAProduct(string $productName): void
    {
        $this->visit('http://catalog.localhost/createProduct');
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
        $this->assertEventually(function () use ($productName, $stockLevel) {
            $this->visit('http://dashboard.localhost/');
            $this->assertResponseStatus(200);

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
        $this->assertEventually(function () use ($quantity) {
            $this->visit('http://purchase.localhost/createPurchaseOrder');

            $this->selectOption('Product', $this->product);
            $this->fillField('Quantity', $quantity);
            $this->pressButton('Order');

            $this->visit('http://purchase.localhost/receiveGoods');
            $this->pressButton('Receive');
        });
    }

    /**
     * @Given we have sold and delivered :quantity items of this product
     * @param string $quantity
     */
    public function weHaveSoldAndDeliveredItemsOfThisProduct(string $quantity): void
    {
        $this->assertEventually(function () use ($quantity) {
            $this->visit('http://sales.localhost/createSalesOrder');
            $this->selectOption('Product', $this->product);
            $this->fillField('Quantity', $quantity);
            $this->pressButton('Order');

            $this->visit('http://sales.localhost/deliverSalesOrder');
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
            throw new \RuntimeException(sprintf('Could not find %s using %s selector', $locator, $selector));
        }

        return $element;
    }

    private function assertEventually(callable $probe): void
    {
        $startTime = time();
        $timeoutInSeconds = 5;
        $waitBeforeRetryingInSeconds = 0.5;
        $keepTrying = true;
        $lastException = null;

        while ($keepTrying) {
            try {
                $probe();

                // if no exception occurs, then we assume everything is good
                return;
            } catch (\Exception $exception) {
                $lastException = $exception;

                // sleep for half a second
                usleep($waitBeforeRetryingInSeconds * 1000000);
            }

            if (time() - $startTime >= $timeoutInSeconds) {
                $keepTrying = false;
            }
        }

        throw new \RuntimeException(sprintf(
            'Probe failed. Last exception: %s',
            $lastException instanceof \Exception ? (string)$lastException : 'n/a'
        ));
    }
}
