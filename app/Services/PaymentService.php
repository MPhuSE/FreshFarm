<?php

namespace App\Services;

class PaymentService
{
    public function createVnPayUrl($order)
    {
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_Url = config('services.vnpay.url');
        $vnp_Returnurl = config('services.vnpay.return_url');
        if (request()->hasHeader('host')) {
            // Keep customer on their current website host (127.0.0.1, localhost, or production domain)
            $vnp_Returnurl = request()->schemeAndHttpHost() . '/payment/vnpay/return';
        } elseif (empty($vnp_Returnurl)) {
            $vnp_Returnurl = url('/payment/vnpay/return');
        } elseif (str_contains($vnp_Returnurl, '/api/v1/payment/vnpay/ipn')) {
            $vnp_Returnurl = str_replace('/api/v1/payment/vnpay/ipn', '/payment/vnpay/return', $vnp_Returnurl);
        }

        $vnp_TxnRef = $order->order_code;
        $vnp_OrderInfo = 'Thanh toan don hang '.$order->order_code;
        $vnp_Amount = $order->grand_total * 100; // quy định số tiền nhân 100
        $vnp_IpAddr = request()->ip();

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => 'billpayment',
            'vnp_ReturnUrl' => $vnp_Returnurl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        ksort($inputData);
        $query = '';
        $i = 0;
        $hashdata = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&'.urlencode($key).'='.urlencode($value);
            } else {
                $hashdata .= urlencode($key).'='.urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key).'='.urlencode($value).'&';
        }

        $vnp_Url = $vnp_Url.'?'.$query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash='.$vnpSecureHash;
        }

        return $vnp_Url;
    }

    public function verifyIpn($requestData)
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_SecureHash = $requestData['vnp_SecureHash'] ?? '';

        $vnpData = [];
        foreach ($requestData as $key => $value) {
            if (str_starts_with($key, 'vnp_') && $key !== 'vnp_SecureHash' && $key !== 'vnp_SecureHashType') {
                $vnpData[$key] = $value;
            }
        }

        ksort($vnpData);
        $hashData = '';
        $i = 0;
        foreach ($vnpData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData.'&'.urlencode($key).'='.urlencode($value);
            } else {
                $hashData = $hashData.urlencode($key).'='.urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        return hash_equals(strtolower($secureHash), strtolower($vnp_SecureHash));
    }
}
