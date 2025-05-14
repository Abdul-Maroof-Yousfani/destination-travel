1. OfferPricingRQ – FarePreference, PricingMethod, and ServicePricingOnlyPreference: (watch meeting)
What do these tags represent, and how do they function within the OfferPricingRQ request?
xxxxxxxxx

2. Passenger References – Multiple Passengers: (i use properly)
If I have 2 adults and 2 children, how should I correctly pass their PassengerRef values in the request?
xxxxxxxxx

3. OrderCreateRQ – Commission Tag: (remove this)
What is the purpose of the Commission tag in OrderCreateRQ, and how should it be used?
xxxxxxxxx

4. GetBundlePriceRQ – Cancellation and Change Fees: (need to discus again)
Where can I find details such as cancellation, refundability, and change fees in the GetBundlePriceRQ response?
xxxxxxxxx

5. Currency Format of Fees: (got extra tag in data list there is decimal define for use)
Are the fee amounts returned in cents (e.g., 8000 = $80.00) or in full currency values (e.g., 8000 = $8000)?
xxxxxxxxx

6. Currency Consistency: (yes return same)
Are fees always returned in USD, or can they appear in other currencies based on route or preference?


7. OrderCreateRQ – ContactList Tag: (user details but it willl very long)xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx Ask again
In the ContactList tag, should we use the end-user's contact details or our company’s contact details?


8. Structure of Query Tag – OfferPricingRQ vs OrderCreateRQ: (use as shown in the documents)
In OfferPricingRQ, only IDs are inside the Query tag, while in OrderCreateRQ, the entire request is wrapped inside Query. Which structure should I follow, or should I use them as shown in the documentation?


9. OrderCreateRQ – ContactInfoRef Tag: (its belong to user details with his address details)
What is the use of the ContactInfoRef tag, and should its value remain consistent or change dynamically?


10. OrderCreateRQ – Payment Type & Booking Limits on Test Credentials: (there is one more api for booking and do as many as i want)xxxxxxxxxxxxxxxx need to run this
How do we handle on-hold vs direct bookings? Is there a specific tag for payment type? Also, can we perform unlimited test bookings using test credentials?


11. AirShoppingRQ – Return Flight Preferences: (its set)
How do we send preferences like cabin class for return flights in the AirShoppingRQ request?


12. Unexpected Booking Class: (its set)
Why does the booking default to Business Class every time?