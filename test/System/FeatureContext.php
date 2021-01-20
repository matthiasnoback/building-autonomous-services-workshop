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
use function Safe\json_decode;

final class FeatureContext extends MinkContext
{
    use Asynchronicity;

    /**
     * The name of the most recently discussed product
     */
    private ?string $product = null;

    /**
     * The most recently purchased quantity
     */
    private ?int $purchasedQuantity = null;

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
        $this->createProduct($productName);
    }

    /**
     * @Then I should see that :productName has a stock level of :stockLevel
     * @param string $productName
     * @param string $stockLevel
     */
    public function iShouldSeeThatHasAStockLevelOf(string $productName, string $stockLevel): void
    {
        self::assertEventually(
            function () use ($productName, $stockLevel) {
                $this->visit('http://dashboard.localtest.me/');
                $this->assertSuccessfulResponse();

                $nameField = $this->findOrFail('css', '.product-name:contains("' . addslashes($productName) . '")');
                $actualStockLevel = (int)$this->findOrFail('css', '.stock-level', $nameField->getParent())->getText();

                Assert::assertEquals($stockLevel, $actualStockLevel);
            }
        );
    }

    /**
     * @Given we have purchased and received :quantity items of this product
     * @param string $quantity
     */
    public function weHavePurchasedAndReceivedItemsOfThisProduct(string $quantity): void
    {
        $this->createPurchaseOrder($quantity);

        $this->receiveGoods();
    }

    /**
     * @Given we have sold and delivered :quantity items of this product
     * @param string $quantity
     */
    public function weHaveSoldAndDeliveredItemsOfThisProduct(string $quantity): void
    {
        $this->createSalesOrder($quantity);

        $this->deliverSalesOrder();
    }


    /**
     * @When we sell :quantity item of this product
     * @Given we have sold :quantity item of this product
     */
    public function weSellQuantityOfProduct(string $quantity): void
    {
        $this->createSalesOrder($quantity);
    }

    /**
     * @Then the sales order should be deliverable
     */
    public function theSalesOrderShouldBeDeliverable(): void
    {
        $this->deliverSalesOrder();
    }

    /**
     * @When we receive the goods for the purchase order that has been created
     */
    public function weReceiveGoodsForThePurchaseOrder(): void
    {
        $this->receiveGoods();
    }

    /**
     * @Then a purchase order should have been created for :quantity item of this product
     */
    public function aPurchaseOrderShouldHaveBeenCreated(string $quantity): void
    {
        self::assertEventually(function () use ($quantity) {
            $jsonDecodedData = $this->getResponseAsDecodedJsonData('http://purchase.localtest.me/listPurchaseOrders');

            Assert::assertCount(1, $jsonDecodedData);
            $firstItem = reset($jsonDecodedData);
            Assert::assertEquals((int)$quantity, $firstItem['quantity']);
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

        $statusCode = intval($this->getSession()->getStatusCode());
        Assert::assertTrue(
            $statusCode >= 200 && $statusCode < 400,
            sprintf(
                "Expected a successful response status. Actual status code: %d. Response body: \n\n%s",
                $statusCode,
                strpos($this->getSession()->getPage()->getContent(), '<html') === false
                    ? $this->getSession()->getPage()->getContent()
                    : $this->getReadableResponseBody()
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

    /**
     * @return mixed
     */
    private function getResponseAsDecodedJsonData(string $url)
    {
        $this->getSession()->setRequestHeader('Accept', 'application/json');

        $this->visit($url);

        $jsonData = (string)$this->getSession()->getPage()->getContent();

        return json_decode($jsonData, true);
    }

    private function createProduct(string $productName): void
    {
        $this->visit('http://catalog.localtest.me/createProduct');
        $this->assertSuccessfulResponse();
        $this->fillField('name', $productName);
        $this->pressButton('Create');
        $this->assertUrlRegExp('#/listProducts#');

        $this->product = $productName;
    }

    private function createPurchaseOrder(string $quantity): void
    {
        self::assertEventually(
            function () use ($quantity) {
                $this->visit('http://purchase.localtest.me/createPurchaseOrder');
                $this->assertSuccessfulResponse();

                Assert::assertIsString($this->product);
                $this->selectOption('Product', $this->product);
                $this->fillField('Quantity', $quantity);
                $this->pressButton('Order');
                $this->assertSuccessfulResponse();

                $this->purchasedQuantity = (int)$quantity;
            }
        );
    }

    private function receiveGoods(): void
    {
        self::assertEventually(
            function () {
                $this->visit('http://purchase.localtest.me/receiveGoods');
                $this->assertSuccessfulResponse();
                $this->pressButton('Receive');
                $this->assertSuccessfulResponse();
            }
        );

        if ($this->purchasedQuantity === null) {
            return;
        }

        self::assertEventually(
            function () {
                $stockLevels = $this->getResponseAsDecodedJsonData('http://stock.localtest.me/stockLevels');
                $firstProduct = reset($stockLevels);
                Assert::assertEquals($this->purchasedQuantity, $firstProduct['stockLevel']);
            }
        );
    }

    private function createSalesOrder(string $quantity): void
    {
        self::assertEventually(
            function () use ($quantity) {
                $this->visit('http://sales.localtest.me/createSalesOrder');
                $this->assertSuccessfulResponse();

                Assert::assertIsString($this->product);
                $this->selectOption('Product', $this->product);
                $this->fillField('Quantity', $quantity);
                $this->pressButton('Order');
                $this->assertSuccessfulResponse();
            }
        );
    }

    private function deliverSalesOrder(): void
    {
        self::assertEventually(
            function () {
                $this->visit('http://sales.localtest.me/deliverSalesOrder');
                $this->assertSuccessfulResponse();
                $this->pressButton('Deliver');
                $this->assertSuccessfulResponse();
            }
        );
    }
}
