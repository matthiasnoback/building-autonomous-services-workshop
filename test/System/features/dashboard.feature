Feature:
  The dashboard offers the manager of the warehouse an overview of all the relevant information.

  Scenario: Products that haven't been purchased yet
    Given the catalog has a product "Mars rover"
    Then I should see that "Mars rover" has a stock level of 0

  Scenario: A product that has been purchased
    Given the catalog has a product "Mars rover"
    And we have purchased and received 10 items of this product
    Then I should see that "Mars rover" has a stock level of 10

  Scenario: Auto-deliver a product that has been purchased and sold
    Given the catalog has a product "Mars rover"
    And we have purchased and received 10 items of this product
    And we have sold 5 items of this product
    Then I should see that "Mars rover" has a stock level of 5

  Scenario: Auto-create a purchase order in case the current stock level is insufficient
    Given the catalog has a product "Mars rover"
    And we have sold 5 items of this product
    When we receive goods for the automatically created purchase order
    Then the automatically created sales order for this should be delivered
    And I should see that "Mars rover" has a stock level of 0
