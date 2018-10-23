# mpesa-integration
a sample code in laravel to demonstrate integration of mpesa for stkpush(mpesa express), C2B, B2C and B2B

# Mpesa Express
This is a service where you launch the user M-pesa menu directy from your application by calling the mpesa sim tool kit,and prefill  with the amount to pay, the user just inserts the PIN and money automatically  deducted from their Mpesa and sent to your Paybill.
The merchant simply pushes the transaction details of a payment to customer's phone. After transaction initiation, the customer receives a push notification.Customer enters their M-Pesa pin and presses ‘OK’. The transaction is now complete, and a real-time payment confirmation will be sent. The details are sent to the callback set and further processing such as subscription can be carried out.

# C2B
This is the normal customer to business service via either a paybill or till number. This service can be used in an instance where you need for customers to subscribe to a service and renew their subscription. e.g used in betting, purchasing electricity tokens.etc.

# B2C

This is the Business to Customer - a business is able to directly pay customers from their bulk account. It's a convinient way to do salary payments, promotions wins betting wins. etc. 

# B2B

This is mainly used to make payments between businesses. eg. it's close to the b2c, but instead of paying to a phone, you make the payment to another paybill.


# Instructions
1 : Create an account on Daraja, and create an app, i.e either C2B API or Lipa Na MPesa API , or use you can assign both to the same app.

2 : Generate a token that will be used in every transaction in daraja to m-pesa. NB: This token expires after about 1 hour, and you have to re-generate a new one after that. For this case I do a cron job  that generates another token after every 55 minutes

3 : If you are doing C2B, you have to register urls where the transaction detail are sent to.

4 : Happy coding, in this project we have the necessary methods implemented for interacting with daraja.

For any questions , shoot here : samlinncon@gmail.com





