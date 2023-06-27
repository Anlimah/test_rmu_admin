<?php

namespace Src\Controller;

use Src\Controller\VoucherPurchase;

class PaymentController
{
    private $voucher;

    public function __construct()
    {
        $this->voucher = new VoucherPurchase();
    }

    public function vendorPaymentProcess($data)
    {
        $trans_id = time();
        if (!$trans_id) return array("success" => false, "message" => "Transaction ID generation failed!");
        return $this->voucher->SaveFormPurchaseData($data, $trans_id);
    }

    /*public function verifyVendorPurchase(int $vendor_id, int $transaction_id)
    {
    }*/
}
