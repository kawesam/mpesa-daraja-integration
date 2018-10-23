<?php

namespace App\Http\Controllers;

use App\B2BLog;
use App\B2BPayment;
use App\B2CLog;
use App\B2CPayment;
use App\C2BLog;
use App\C2BPayment;
use App\Helpers\Mpesa;
use App\SdkPushLog;
use App\StkPushPayment;
use Carbon\Carbon;
use Hamcrest\Core\Set;
use Illuminate\Http\Request;
use anlutro\LaravelSettings\Facade as Setting;
use Illuminate\Support\Facades\Log;


class HooksController extends Controller
{
    //function for SDK push
    public function sdkPush(Request $request){
        $businessShortCode = env('shortcode');
        $passKey = env('PassKey');
        $callBackUrl = env('CallBackURL');
        $time = Carbon::now()->format('YmdHis');
        $endUrl = 'mpesa/stkpush/v1/processrequest';


        $requestBody = [
            //Fill in the request parameters with valid values
            'BusinessShortCode' => $businessShortCode,
            'Password' => base64_encode($businessShortCode.$passKey.$time),
            'Timestamp' => $time,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $request->get('amount'),
            'PartyA' => $request->get('phone'),
            'PartyB' => $businessShortCode,
            'PhoneNumber' => $request->get('phone'),
            'CallBackURL' =>  $callBackUrl,
            'AccountReference' => $request->get('account'),
            'TransactionDesc' => $request->get('description')

        ];

        $response = Mpesa::post($endUrl,$requestBody);

        return $response;


    }

    //callback to process sdkpush payments made
    public function receiveSdkPayments(Request $request){

        Log::info(json_encode($request->all()));

        $content = $request->all();
        $sdk_push_logs = new SdkPushLog;
        $sdk_push_logs->content = json_encode($content);
        $sdk_push_logs->save();

        //get the resultcode to determine if the transaction was successful
        $resultCode = $content['Body']['stkCallback']['ResultCode'];

        if($resultCode == 0){
            //a successful transaction push
            //save the transactions in stk_push payments table
            //0- means transaction was successful
            $stk_push_payments = new StkPushPayment;
            $stk_push_payments->MpesaReceiptNumber = $content['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
            $stk_push_payments->phone = $content['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];
            $stk_push_payments->amount = $content['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
            $stk_push_payments->ResultCode = $content['Body']['stkCallback']['ResultCode'];
            $stk_push_payments->ResultDesc = $content['Body']['stkCallback']['ResultDesc'];
            $stk_push_payments->status = 0;

            $stk_push_payments->save();
            //good point to notify a user that the payment was well received.
        } else{
            //transaction failed for some reason
            //1- means failed transaction
            $stk_push_payments = new StkPushPayment;
            $stk_push_payments->ResultCode = $content['Body']['stkCallback']['ResultCode'];
            $stk_push_payments->ResultDesc = $content['Body']['stkCallback']['ResultDesc'];
            $stk_push_payments->status = 1;
            $stk_push_payments->save();

            //good point to send a message to the person who triggered the payment with instructions to pay directly via paybill.
        }


    }

    //query an STK push transaction
    public function stKPushQuery(Request $request){
        $businessShortCode = env('shortcode');
        $passKey = env('PassKey');
        $time = Carbon::now()->format('YmdHis');
        $CheckoutRequestID = $request->input('CheckoutRequestID');
        $endUrl = 'mpesa/stkpushquery/v1/query';

        $requestBody = [
            'BusinessShortCode' => $businessShortCode,
            'Password' =>base64_encode($businessShortCode.$passKey.$time),
            'Timestamp' => $time,
            'CheckoutRequestID' => $CheckoutRequestID
        ];

        $response = Mpesa::post($endUrl,$requestBody);
        return $response;
    }

    //generate access token sample
    public function getAccessToken(){
        $token = Mpesa::generateToken();

        Setting::set('mpesa-api',$token['access_token']);

        Setting::save();

        return $token['access_token'];
    }


    //register confirmation urls
    public function registerConfirmationUrl(Request $request){
        $shortCode = env('SAFARICOM_PAYBILL');
        $responseType = 'Completed';
        $confirmationUrl = env('SAFARICOM_CONFIRMATION_URL');
        $validationUrl = env('SAFARICOM_VALIDATION_URL');
        $endUrl = 'mpesa/c2b/v1/registerurl';

        $requestBody = [
            'ShortCode' => $shortCode,
            'ResponseType' => $responseType,
            'ConfirmationURL' => $confirmationUrl,
            'ValidationURL' => $validationUrl
        ];

        $response = Mpesa::post($endUrl,$requestBody);
        return $response;

    }

    //validation url
    public function registerValidationUrl(Request $request){
        Log::info(json_encode($request->all()));

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => "Validation successful"
        ]);
    }


    //method to simulate c2B
    public function simulateC2BMethod(Request $request){
        $shortCode = env('SAFARICOM_PAYBILL');
        $commandId = "CustomerPayBillOnline";
        $endUrl = 'mpesa/c2b/v1/simulate';

        $requestBody = [
            'ShortCode' => $shortCode,
            'CommandID' => $commandId,
            'Amount' => "100",
            'Msisdn' => "254708374149",
            'BillRefNumber' => "00000"
        ];

        $response = Mpesa::post($endUrl,$requestBody);
        return $response;
    }

    //method to process payments received from mpesa c2B
    public function processPaymentsReceived(Request $request){

        Log::info(json_encode($request->all()));

//        print_r($request->all());exit();
        $content = $request->all();

        $c2b_logs = new C2BLog;
        $c2b_logs->content = json_encode($content);
        $c2b_logs->save();

        //store the c2b transaction
        $c2bTransaction = new C2BPayment;
        $c2bTransaction->TransactionType = $content['TransactionType'];
        $c2bTransaction->transactionId = $content['TransID'];
        $c2bTransaction->amount = $content['TransAmount'];
        $c2bTransaction->businesscode = $content['BusinessShortCode'];
        $c2bTransaction->billrefnumber = $content['BillRefNumber'];
        $c2bTransaction->organization_float = $content['OrgAccountBalance'];
        $c2bTransaction->ThirdPartyTransID = $content['ThirdPartyTransID'];
        $c2bTransaction->phone = $content['MSISDN'];
        $c2bTransaction->firstname = $content['FirstName'];
        $c2bTransaction->middlename = $content['MiddleName'];
        $c2bTransaction->lastname = $content['LastName'];
        $c2bTransaction->save();


        //return the transaction is accepted
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted'
        ]);
    }


    //simulate B2C payment
    public function simulateB2CMethod(Request $request){
        $initiatorName = env('INITIATOR_NAME');
        $securityCredential = env('SECURITY_CREDENTIAL');
        $commandId = "BusinessPayment";
        $amount = $request->input('amount');
        $partyA = env('PARTY_A');
        $partyB = $request->input('phone');
        $remarks = "please";
        $occasion ="work";
        $endUrl = 'mpesa/b2c/v1/paymentrequest';
        $queueTimeOutURL = env('QueueTimeOutURL');
        $resulturl = env('ResultURL');

        $requestBody = [
            'InitiatorName' => $initiatorName,
            'SecurityCredential' => $securityCredential,
            'CommandID' => $commandId,
            'Amount' =>$amount,
            'PartyA' => $partyA,
            'PartyB' => $partyB,
            'Remarks' => $remarks,
            'QueueTimeOutURL' => $queueTimeOutURL,
            'ResultURL' => $resulturl,
            'Occasion' =>$occasion
        ];

        $response = Mpesa::post($endUrl,$requestBody);
        return $response;
    }

    //callback for the b2c disbursment method
    public function initiatePaymentsDisbursement(Request $request){
        Log::info(json_encode($request->all()));

        $b2c_content = $request->all();

        $log = new B2CLog;
        $log->content = json_encode($b2c_content);
        $log->save();

        $paymentResultCode = $b2c_content['Result']['ResultCode'];
        //check if the transaction was successful
        if($paymentResultCode == 0){
            //log transaction details
            $transactionId = $b2c_content['Result']['TransactionID'];
            $resultDescription = $b2c_content['Result']['ResultDesc'];
            $TransactionAmount = $b2c_content['Result']['ResultParameters']['ResultParameter'][0]['Value'];
            $receiverDetails = $b2c_content['Result']['ResultParameters']['ResultParameter'][4]['Value'];
            $receiverPhone = explode("-",$receiverDetails)[0];
            $receiverNames = explode("-",$receiverDetails)[1];
            $transactionCompletionTime = $b2c_content['Result']['ResultParameters']['ResultParameter'][5]['Value'];
            $B2CUtilityAccountAvailableFunds = $b2c_content['Result']['ResultParameters']['ResultParameter'][6]['Value'];
            $B2CWorkingAccountAvailableFunds = $b2c_content['Result']['ResultParameters']['ResultParameter'][7]['Value'];

            //0 -means transaction was successful

            $b2c_payment = new B2CPayment;
            $b2c_payment->transactionId = $transactionId;
            $b2c_payment->resultcode = $paymentResultCode;
            $b2c_payment->ResultDesc = $resultDescription;
            $b2c_payment->amount = $TransactionAmount;
            $b2c_payment->phone = $receiverPhone;
            $b2c_payment->receiver_names = $receiverNames;
            $b2c_payment->B2CUtilityAccountAvailableFunds = $B2CUtilityAccountAvailableFunds;
            $b2c_payment->B2CWorkingAccountAvailableFunds = $B2CWorkingAccountAvailableFunds;
            $b2c_payment->transaction_status = 0;
            $b2c_payment->save();

        } else {
            //transaction failed
            //1 -means transaction failed
            $b2c_payment = new B2CPayment;
            $b2c_payment->transactionId = $b2c_content['Result']['TransactionID'];
            $b2c_payment->resultcode = $b2c_content['Result']['ResultCode'];
            $b2c_payment->ResultDesc = $b2c_content['Result']['ResultDesc'];
            $b2c_payment->transaction_status = 1;
            $b2c_payment->save();

        }


    }

    //callback for timeout in b2c
    public function recordTimeOut(Request $request){

    }

    //method to simulate b2b transfer
    public function simulateB2BTransfer(Request $request){
        $initiator = env('Initiator');
        $securityCredential = env('SECURITY_CREDENTIAL');
        $commandId = "BusinessPayBill";
        $senderIdentifierType = "4";
        $receiverIdentifierType = "4";
        $amount = $request->input('amount');
        $partyA = env('B2BPartyA');
        $partyB = env('B2BpartyB');
        $accountReference = "ref";
        $remarks = "transfer funds";
        $queuetimeOutURL = env('B2BTimeOutUrl');
        $resultUrl = env('B2BResultUrl');
        $endUrl = 'mpesa/b2b/v1/paymentrequest';


        $requestBody = [
            'Initiator' => $initiator,
            'SecurityCredential' => $securityCredential,
            'CommandID' => $commandId,
            'SenderIdentifierType' => $senderIdentifierType,
            'RecieverIdentifierType' => $receiverIdentifierType,
            'Amount' =>$amount,
            'PartyA' => $partyA,
            'PartyB' => $partyB,
            'AccountReference' => $accountReference,
            'Remarks' =>$remarks,
            'QueueTimeOutURL' => $queuetimeOutURL,
            'ResultURL' =>$resultUrl
        ];

        $response = Mpesa::post($endUrl,$requestBody);
        return $response;

    }

    //callback for b2b timeout request
    public function recordb2bTimeOut(Request $request){

    }

    //call back implementation for b2b payments
    public function processB2Bpayments(Request $request){
        Log::info(json_encode($request->all()));

        $b2b_content = $request->all();
        $log = new B2BLog;
        $log->content = json_encode($b2b_content);
        $log->save();

        //process the request received

        $resultType = $b2b_content['Result']['ResultType'];
        $resultCode = $b2b_content['Result']['ResultCode'];
        $resultDesc = $b2b_content['Result']['ResultDesc'];
        $transactionId = $b2b_content['Result']['TransactionID'];
        if($resultCode == 0){
            //successful transaction
            $payment_details = $b2b_content['Result']['ResultParameters']['ResultParameter'];

            $InitiatorAccountCurrentBalance =$payment_details[0]['Value'];
            $amount = $payment_details[2]['Value'];
//            print_r(explode("=",$InitiatorAccountCurrentBalance)[4]);exit();
            $DebitPartyAffectedAccountBalance = explode("|",$payment_details['3']['Value'])[2];
            $DebitPartyCharges = explode("KES|",$payment_details['5']['Value'])[1];
            $ReceiverPartyPublicName =$payment_details['6']['Value'];

            $b2b_payment = new B2BPayment;
            $b2b_payment->resulttype = $resultType;
            $b2b_payment->resultcode = $resultCode;
            $b2b_payment->transactionId = $transactionId;
            $b2b_payment->ResultDesc = $resultDesc;
            $b2b_payment->amount =$amount;
            $b2b_payment->DebitPartyCharges =$DebitPartyCharges;
            $b2b_payment->receiverName =$ReceiverPartyPublicName;
            $b2b_payment->transaction_status =0;
            $b2b_payment->save();
            //send an sms in case the transaction is successful
        } else{
            $b2b_payment = new B2BPayment;
            $b2b_payment->resulttype = $b2b_content['Result']['ResultCode'];
            $b2b_payment->transactionId = $b2b_content['Result']['TransactionID'];
            $b2b_payment->resultcode = $b2b_content['Result']['ResultCode'];
            $b2b_payment->ResultDesc = $b2b_content['Result']['ResultDesc'];
            $b2b_payment->transaction_status = 1;
            $b2b_payment->save();
            //send an sms in case there is an issue
        }
    }

}
