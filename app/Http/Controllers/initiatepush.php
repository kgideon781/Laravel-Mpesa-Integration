<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Log;
class initiatepush extends Controller
{
    public function pay(Request $request){
        $Amount =$request->input('amount');

        $phoneNumber = "254".substr($request['phonenumber'], -9);

        $CallBackURL = 'https://peternjeru.co.ke/safdaraja/api/callback.php';

        Log::error('INITIATION PHONE RECEIVED: '.$phoneNumber);

        if($Amount != 0){

            $mpesa= new \Safaricom\Mpesa\Mpesa();

            $stkPushSimulation=$mpesa->STKPushSimulation(174379, 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919', 'CustomerPayBillOnline', $Amount, $phoneNumber, 174379, $phoneNumber, $CallBackURL, 'lozadasuplies', 'lozada', 'Payment');


            if (!empty($stkPushSimulation)){

                $state = json_decode($stkPushSimulation);

                if (property_exists($state,"ResponseCode")){

                    $ResponseCode = $state->ResponseCode;

                    $CustomerMessage = $state->CustomerMessage;

                    if ($ResponseCode == '0'){

                        $MerchantRequestID = $state->MerchantRequestID;

                        $CheckoutRequestID = $state->CheckoutRequestID;

                        $TransactionType = "sandbox";
                        $TransID = '12251';
                        $TransTime = "jhjuigui";
                        $TransAmount = "0";
                        $BusinessShortCode = "17374";
                        $BillRefNumber = "";
                        $InvoiceNumber = "";
                        $MSISDN = "";
                        $FirstName = "";
                        $LastName = "";
                        $OrgAccountBalance = "";
                        DB::insert('INSERT INTO payments
                                             ( 
                                             TransactionType,
                                             TransID,
                                             MSISDN,
                                             OrgAccountBalance,
                                             InvoiceNumber,
                                             BusinessShortCode,
                                             TransAmount,
                                             TransTime,
                                             MerchantRequestID,
                                             BillRefNumber,
                                             FirstName,
                                             LastName,
                                             CheckoutRequestID
                                             
                                             )   values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                            [
                                $TransactionType,
                                $TransID,
                                $InvoiceNumber,
                                $BusinessShortCode,
                                $BillRefNumber,
                                $TransAmount,
                                $LastName,
                                $TransTime,
                                $FirstName,
                                $MerchantRequestID,
                                $MSISDN,
                                $OrgAccountBalance,
                                $CheckoutRequestID
                            ] );
                        return view('waiting', ['CheckoutRequestID' => $CheckoutRequestID,'CustomerMessage' =>$CustomerMessage,'complete'=>false]);
                    }
                    else
                    {

                        return view('waiting', ['CustomerMessage' =>$CustomerMessage,'complete'=>true]);
                        return view('waiting', ['CheckoutRequestID' =>0, 'CustomerMessage' =>$CustomerMessage,'complete'=>true]);
                    }
                }
                elseif(property_exists($state,"errorCode")){

                    if(($state->errorCode) == "500.001.1001"){
                        $CustomerMessage = "Looks like you provided an invalid phone number";
                        return view('waiting', ['CustomerMessage' =>$CustomerMessage,'complete'=>true]);
                        return view('waiting', ['CheckoutRequestID' =>0,'CustomerMessage' =>$CustomerMessage,'complete'=>true]);
                    }else{
                        $CustomerMessage = "Bad payment request, please contact support";
                        return view('waiting', ['CustomerMessage' =>$CustomerMessage,'complete'=>true]);
                        return view('waiting', ['CheckoutRequestID' =>0,'CustomerMessage' =>$CustomerMessage,'complete'=>true]);
                    }

                }else{
                    $CustomerMessage = "Bad payment request, please contact support";
                    return view('waiting', ['CustomerMessage' =>$CustomerMessage,'complete'=>true]);
                    return view('waiting', ['CheckoutRequestID' =>0,'CustomerMessage' =>$CustomerMessage,'complete'=>true]);
                }
            }

        }else
        {
            echo "Go buy some tea with that amount";
        }

    }
}