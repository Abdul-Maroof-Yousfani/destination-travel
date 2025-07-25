Basic NDC Flow( Booking and Cancellation ): 
NDC APIs: AirShoppingRQ, OfferPriceRQ, OrderCreateRQ, OrderRetrieveRQ, OrderReshopRQ( Reprice ), OrderChangeRQ, OrderCancelRQ
Workflow: AirShoppingRQ > OfferPriceRQ > OrderCreateRQ > OrderRetrieveRQ > OrderReshopRQ( Reprice ) > OrderChangeRQ > OrderCancelRQ
TC1: Any Route, Direct/One-way, 1ADT
Order is Booked
Order is Repriced
Order is Ticketed
Order is Cancelled


order reshop, == not req for logs

OrderReshopRQ (use for update flight like new flight you have to submit order ids of new flight with this)


Workflow:
    ..AirShoppingRQ > ..OfferPriceRQ > ..OrderCreateRQ > ..OrderRetrieveRQ > OrderReshopRQ( Reprice ) > ...OrderChangeRQ > ...OrderCancelRQ