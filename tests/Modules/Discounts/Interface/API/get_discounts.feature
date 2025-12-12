Feature: Discounts
    As a Sales Representative
    I want to encourage my key customers to make purchases
    So I need to check the value of discounted order quotes

    Scenario: Get fixed discount for 10 units of product
        When I send a POST request to "api/discounts/calculate" with body:
        """
        {
            "products": [
                {"code": "PROD1", "quantity": 10}
            ],
            "discounts": [
                {"type": "fixed", "amountInCents": 500}
            ],
            "selectedProducts": []
        }
        """
        Then the response status code should be 200
        And the response should be JSON
        And the JSON node "amount" should be numeric
        And the JSON node "currency" should be equal to "PLN"

    Scenario: Get percentage discount for 10 units of product
        When I send a POST request to "api/discounts/calculate" with body:
        """
        {
            "products": [
                {"code": "PROD1", "quantity": 10}
            ],
            "discounts": [
                {"type": "percentage", "percentage": 15}
            ],
            "selectedProducts": ["PROD1"]
        }
        """
        Then the response status code should be 200
        And the response should be JSON
        And the JSON node "amount" should be numeric

    Scenario: Get volume discount for 10 units of product, second product with not enough units
        When I send a POST request to "api/discounts/calculate" with body:
        """
        {
            "products": [
                {"code": "PROD1", "quantity": 10},
                {"code": "PROD2", "quantity": 2}
            ],
            "discounts": [
                {"type": "volume", "amountInCents": 100, "quantity": 5}
            ],
            "selectedProducts": ["PROD1", "PROD2"]
        }
        """
        Then the response status code should be 200
        And the response should be JSON
        And the JSON node "amount" should be numeric

    Scenario: Failing - Get fixed discount for not existing product
        When I send a POST request to "api/discounts/calculate" with body:
        """
        {
            "products": [
                {"code": "NON-EXISTENT", "quantity": 10}
            ],
            "discounts": [
                {"type": "fixed", "amountInCents": 500}
            ],
            "selectedProducts": ["NON-EXISTENT"]
        }
        """
        Then the response status code should be 404
        And the response should be JSON
        And the JSON node "errors.request" should be equal to 'Price "NON-EXISTENT" not found'

    Scenario: Failing - Invalid discount type
        When I send a POST request to "api/discounts/calculate" with body:
        """
        {
            "products": [
                {"code": "PROD1", "quantity": 10}
            ],
            "discounts": [
                {"type": "invalid_type", "amountInCents": 500}
            ],
            "selectedProducts": ["PROD1"]
        }
        """
        Then the response status code should be 422

    Scenario: Failing - Missing required fields
        When I send a POST request to "api/discounts/calculate" with body:
        """
        {
            "products": [],
            "discounts": [],
            "selectedProducts": []
        }
        """
        Then the response status code should be 422
