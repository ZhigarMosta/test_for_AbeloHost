<?php

class SyncCommand

{
    /**
     * @param $invoiceName
     * @param $paymentPurpose
     * @return bool
     */
    protected static function invoiceNumberInPurpose($invoiceName, $paymentPurpose): bool
    {
        $prepareStr = preg_replace('/\D/', ' ', $paymentPurpose);
        $prepareStr = preg_replace('/\s+/', ' ', $prepareStr);

        $ppAr = explode(' ', $prepareStr);
        foreach ($ppAr as $piece) {
            if ($piece == $invoiceName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Ищет номер счёта среди числовых последовательностей в назначении платежа.
     */
    private function isInvoiceNumberInPurpose(ah $invoice, array $payment): bool
    {
        $invoiceName = $invoice['name'];
        $paymentPurpose = $payment['paymentPurpose'];

        if (strpos($paymentPurpose, $invoiceName) === false
            && ((int)$invoiceName === 0 || strpos($paymentPurpose, (string)(int)$invoiceName) === false)) {
            return false;
        }

        return self::invoiceNumberInPurpose($invoiceName, $paymentPurpose);
    }

    /**
     * Ищет дату выставления счёта в назначении платежа.
     */
    private function isInvoiceDateInPurpose(ah $invoice, array $payment): bool
    {
        if ($invoice['sum'] != $payment['sum']) {
            return false;
        }

        $prepareDate = date('d.m.Y', strtotime($invoice['moment']));
        return strpos($payment['paymentPurpose'], $prepareDate) !== false;
    }

    /**
     * @param ah          $paymentsIn
     * @param MoyskladApp $msApp
     *
     * @return void
     * @throws Exception
     */
    protected function attachToInvoiceOut(ah $paymentsIn, MoyskladApp $msApp)
    {
        $attributes = $this->user->get('settings.' . AttributeModel::TABLE_NAME, new ah());
        $isAttachedToInvoiceAttr = $attributes->get('paymentin.isAttachedToInvoice')->getAll();


        $msApi = $msApp->getJsonApi();
        $invoicesOut = $msApi->getEntityRows('invoiceout', [
            'expand' => 'organizationAccount, agent'
        ]);

        $invoicesOut = (new ah($invoicesOut))->filter(function ($item) {
            return (int)$item['sum'] !== (int)$item['payedSum'] * 100;
        })->getAll();

        $updatePayment = [];
        $updateInvoiceOut = [];
        $paymentsIn->each(function($payment) use (
            &$invoicesOut,
            &$updatePayment,
            &$updateInvoiceOut,
            &$isAttachedToInvoiceAttr
        ) {
            if (empty($payment['organizationAccount']['meta']['href']) || empty($payment['paymentPurpose'])) {
                return;
            }

            foreach ($invoicesOut as &$invoiceOut) {
                $invoice = new ah($invoiceOut);
                if (empty($invoice['organizationAccount']['meta']['href'])) {
                    continue;
                }

                $notEqualAgent = !TextHelper::isEqual($invoice['agent']['meta']['href'], $payment['agent']['meta']['href']);
                $notEqualAccount = !TextHelper::isEqual($invoice['organizationAccount']['meta']['href'], $payment['organizationAccount']['meta']['href']);
                $notEqualOrganization = !TextHelper::isEqual($invoice['organization']['meta']['href'], $payment['organization']['meta']['href']);

                if ($notEqualAgent || $notEqualAccount || $notEqualOrganization) {
                    continue;
                }

                if (!$this->isInvoiceNumberInPurpose($invoice, $payment)
                    && !$this->isInvoiceDateInPurpose($invoice, $payment)) {
                    continue;
                }

                $isAttachedToInvoiceAttr['value'] = true;
                $payment['attributes'] = [$isAttachedToInvoiceAttr];
                $payment['operations'] = [['meta' => $invoiceOut['meta']]];
                $updatePayment[] = $payment;

                $invoiceOut['payments'] = [['meta' => $payment['meta']]];
                $updateInvoiceOut[] = $invoiceOut;

                return;
            }
        });

        if (!empty($updatePayment)) {
            $msApi->sendEntity('paymentin', $updatePayment);
        }

        if (!empty($updateInvoiceOut)) {
            $msApi->sendEntity('invoiceout', $updateInvoiceOut);
        }
    }
}