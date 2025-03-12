<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EasyMoneyService;
use App\Services\SuperWalletzService;
use App\Models\Transaction;


class TransactionController extends Controller
{
    private $arr_status_message = ['pending' => 'Pago en proceso', 'success' => 'Pago realizado con exito', 'error' => 'Pago rechazado'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        echo "Procesar Depositos";
    }

    /**
     * Procesar Depósito
     */
    public function deposit(Request $request, EasyMoneyService $easyMoneyService, SuperWalletzService $superWalletzService)
    {
        $pay_method = $request->input('pay-method');

        switch ($pay_method) {
            case 'easymoney':
                $response = $this->payByEasyMoney($request, $easyMoneyService);
                return view('welcome', ['message' => $this->arr_status_message[$response]]);
                break;
            case 'superwalletz':
                $response = $this->payBySuperWalltez($request, $superWalletzService);
                return view('welcome', ['message' => $this->arr_status_message[$response]]);
                break;
            default:
                throw new \Exception("Método no dispobile");
                break;
        }
    }


    /**
     * Pagar por Easy Money
     */
    private function payByEasyMoney(Request $request, EasyMoneyService $easyMoneyService)
    {
        $amount = $request->input('amount');
        $currency = $request->input('currency');
        $pay_method = $request->input('pay-method');

        try {

            if (empty($currency)) {
                throw new \Exception("La moneda no puede ser nulo");
            }

            if (empty($amount)) {
                throw new \Exception("El monto no puede ser nulo");
            }

            if (!is_numeric($amount)) {
                throw new \Exception("El monto debe ser numérico");
            }

            if (floor($amount) != $amount) {
                throw new \Exception("El monto no puede ser decimal.");
            }

            // creo la transacción
            $transaction = $this->createTransaction($pay_method, intval($amount), $currency);

            // Procesar el pago
            $response = $easyMoneyService->pay(intval($amount), $currency);

            $new_status = 'success';
            if ($response == 'error') $new_status = 'error';

            // Actualizar el estado de la transacción
            $this->updateTransaction($transaction->id, $new_status);

            return $new_status;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return 'error';
        }
    }

    /**
     * Pagar por SuperWalltez
     */
    private function payBySuperWalltez(Request $request, SuperWalletzService $superWalletzService)
    {
        $amount = $request->input('amount');
        $currency = $request->input('currency');
        $pay_method = $request->input('pay-method');

        try {

            if (empty($currency)) {
                throw new \Exception("La moneda no puede ser nulo");
            }

            if (empty($amount)) {
                throw new \Exception("El monto no puede ser nulo");
            }

            if (!is_numeric($amount)) {
                throw new \Exception("El monto debe ser numérico");
            }

            // creo la transacción
            $transaction = $this->createTransaction($pay_method, $amount, $currency);

            // Procesar el pago
            $response = $superWalletzService->pay($amount, $currency);

            // Actualizar el estado de la transacción
            $this->updateTransaction($transaction->id, '', $response['transaction_id']);

            return 'pending';
        } catch (\Exception $e) {
            echo $e->getMessage();
            return 'error';
        }
    }

    /**
     * Actualizar webhook
     */
    public function webhookSuperWalltez(Request $request)
    {
        $transaction_id = $request->input('transaction_id');
        $status = $request->input('status');

        try {
            $transaction = Transaction::where('transaction_id', $transaction_id)->orderBy('id', 'desc')->first();
            $this->updateTransaction($transaction->id, $status);

            echo 'OK';
        } catch (\Exception $e) {
            // Manejo de errores
            echo $e->getMessage();
        }
    }


    /**
     * Crear un registro de Transacción
     */
    private function createTransaction($payType, $amount, $currency)
    {
        try {

            $transaction = Transaction::create([
                'pay_type' => $payType,
                'date' => date('Y-m-d'),
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'pending',
            ]);

            return $transaction;
        } catch (\Exception $e) {
            // Manejo de errores
            echo $e->getMessage();
            return (object)["id" => 0];
        }
    }

    /**
     * Actualizar un registro de Transacción
     */
    private function updateTransaction($id, $status = '', $transaction_id = '')
    {
        try {
            $success = false;
            if ($status != '' || $transaction_id != '') {
                $updatedFields = [];
                if ($status != '') $updatedFields['status'] = $status;
                if ($transaction_id != '') $updatedFields['transaction_id'] = $transaction_id;

                $updatedTransaction = Transaction::findOrFail($id);
                $updatedTransaction->update($updatedFields);

                $success = true;
            }

            return $success;
        } catch (\Exception $e) {
            // Manejo de errores
            echo $e->getMessage();
            return false;
        }
    }
}
