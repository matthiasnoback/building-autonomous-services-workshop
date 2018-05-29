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
     * @When I take a look at the dashboard
     */
    public function iTakeALookAtTheDashboard(): void
    {
        $this->visit('http://dashboard.localhost/');
        $this->assertResponseStatus(200);
    }

    /**
     * @Then I should see that :productName has a stock level of :stockLevel
     */
    public function iShouldSeeThatHasAStockLevelOf(string $productName, string $stockLevel): void
    {
        $nameField = $this->findOrFail('css', '.product-name:contains("' . addslashes($productName) . '")');
        $actualStockLevel = (int)$this->findOrFail('css', '.stock-level', $nameField->getParent())->getText();

        assertEquals($stockLevel, $actualStockLevel);
    }

    /**
     * @Given we have purchased and received :quantity items of this product
     */
    public function weHavePurchasedAndReceivedItemsOfThisProduct(string $quantity): void
    {
        $this->visit('http://purchase.localhost/createPurchaseOrder');

        $this->fillField($this->findQuantityFieldNameFor($this->product), $quantity);
        $this->pressButton('Order');

        $this->visit('http://purchase.localhost/receiveGoods');
        $this->pressButton('Receive');
    }

    /**
     * @Given we have sold and delivered :quantity items of this product
     */
    public function weHaveSoldAndDeliveredItemsOfThisProduct(string $quantity): void
    {
        $this->visit('http://sales.localhost/createSalesOrder');
        $this->fillField($this->findQuantityFieldNameFor($this->product), $quantity);
        $this->pressButton('Order');

        $this->visit('http://sales.localhost/deliverSalesOrder');
        $this->pressButton('Deliver');
    }

    private function findQuantityFieldNameFor(string $productName): ?string
    {
        $nameElement = $this->findElementContainingProductName($productName);
        $quantityFieldName = $this->findOrFail('css', 'input.quantity', $nameElement->getParent())->getAttribute('name');

        return $quantityFieldName;
    }

    private function findElementContainingProductName(string $productName): NodeElement
    {
        return $this->findOrFail('css', '.product-name:contains("' . addslashes($productName) . '")');
    }

    /**
     * Proxy for `$this->find(...)`, which fails if no element matched the given locator.
     */
    private function findOrFail(string $selector, string $locator, NodeElement $parentNode = null): NodeElement
    {
        $element = ($parentNode ?: $this->getSession()->getPage())->find($selector, $locator);

        if (!$element instanceof NodeElement) {
            throw new \RuntimeException(sprintf('Could not find %s using %s selector', $locator, $selector));
        }

        return $element;
    }
}
