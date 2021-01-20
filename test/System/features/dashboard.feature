Feature:
  The dashboard offers the manager of the warehouse an overview of all the relevant information.

  Scenario: Products that haven't been purchased yet
    Given the catalog has a product "Mars rover"
    Then I should see that "Mars rover" has a stock level of 0

  Scenario: A product that has been purchased
    Given the catalog has a product "Mars rover"
    And we have purchased and received 10 items of this product
    Then I should see that "Mars rover" has a stock level of 10

  Scenario: A product that has been purchased and sold
    Given the catalog has a product "Mars rover"
    And we have purchased and received 10 items of this product
    And we have sold and delivered 5 items of this product
    Then I should see that "Mars rover" has a stock level of 5

  Scenario: We order a product that isn't in stock
    Given the catalog has a product "Mars rover"
    When we sell 1 item of this product
    Then a purchase order should have been created for 1 item of this product

  Scenario: We receive goods for the purchase order that was created for a sales order
    Given the catalog has a product "Mars rover"
    And we have sold 1 item of this product
    When we receive the goods for the purchase order that has been created
    Then the sales order should be deliverable
