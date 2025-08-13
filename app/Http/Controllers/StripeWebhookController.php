<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentTransaction;
use App\Models\UserEnrollment;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhook events.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            // Verify webhook signature
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            // For localhost testing, allow test webhook secret
            if ($webhookSecret === 'whsec_test_localhost') {
                $event = json_decode($payload);
                Log::info('Using test webhook secret for localhost development');
            } else {
                Log::error('Stripe webhook signature verification failed: ' . $e->getMessage());
                return response('Invalid signature', 400);
            }
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;
            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
            case 'charge.refunded':
                $this->handleChargeRefunded($event->data->object);
                break;
            case 'charge.dispute.created':
                $this->handleDisputeCreated($event->data->object);
                break;
            default:
                Log::info('Unhandled Stripe event: ' . $event->type);
        }

        return response('Webhook handled', 200);
    }

    /**
     * Handle successful payment.
     */
    protected function handlePaymentSucceeded($paymentIntent)
    {
        try {
            DB::beginTransaction();

            // Find the transaction by Stripe payment intent ID
            $transaction = PaymentTransaction::where('stripe_payment_intent_id', $paymentIntent->id)->first();

            if (!$transaction) {
                Log::error('Transaction not found for payment intent: ' . $paymentIntent->id);
                return;
            }

            // Update transaction status
            $transaction->update([
                'transaction_status' => 'completed',
                'stripe_charge_id' => $paymentIntent->latest_charge,
            ]);

            // Update enrollment status
            $enrollment = UserEnrollment::where('payment_transaction_id', $transaction->id)->first();
            if ($enrollment) {
                $enrollment->update([
                    'payment_status' => 'paid',
                    'enrollment_status' => 'active',
                ]);

                // If this is a session booking, update the session status
                if ($enrollment->enrollable_type === 'App\Models\SessionBooking') {
                    $session = $enrollment->enrollable;
                    if ($session) {
                        $session->update([
                            'status' => 'booked',
                            'user_id' => $enrollment->user_id,
                            'booked_at' => now(),
                        ]);
                    }
                }
            }

            // Log successful payment
            Log::info('Payment succeeded for transaction: ' . $transaction->transaction_id);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error handling payment succeeded webhook: ' . $e->getMessage());
        }
    }

    /**
     * Handle failed payment.
     */
    protected function handlePaymentFailed($paymentIntent)
    {
        try {
            DB::beginTransaction();

            // Find the transaction by Stripe payment intent ID
            $transaction = PaymentTransaction::where('stripe_payment_intent_id', $paymentIntent->id)->first();

            if (!$transaction) {
                Log::error('Transaction not found for payment intent: ' . $paymentIntent->id);
                return;
            }

            // Update transaction status
            $transaction->update([
                'transaction_status' => 'failed',
                'error_message' => $paymentIntent->last_payment_error->message ?? 'Payment failed',
                'error_code' => $paymentIntent->last_payment_error->code ?? 'unknown',
            ]);

            // Update enrollment status
            $enrollment = UserEnrollment::where('payment_transaction_id', $transaction->id)->first();
            if ($enrollment) {
                $enrollment->update([
                    'payment_status' => 'failed',
                    'enrollment_status' => 'cancelled',
                ]);
            }

            // Log failed payment
            Log::error('Payment failed for transaction: ' . $transaction->transaction_id);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error handling payment failed webhook: ' . $e->getMessage());
        }
    }

    /**
     * Handle charge refunded.
     */
    protected function handleChargeRefunded($charge)
    {
        try {
            DB::beginTransaction();

            // Find the transaction by Stripe charge ID
            $transaction = PaymentTransaction::where('stripe_charge_id', $charge->id)->first();

            if (!$transaction) {
                Log::error('Transaction not found for charge: ' . $charge->id);
                return;
            }

            // Update transaction status
            $transaction->update([
                'transaction_status' => 'refunded',
                'stripe_refund_id' => $charge->refunds->data[0]->id ?? null,
            ]);

            // Update enrollment status
            $enrollment = UserEnrollment::where('payment_transaction_id', $transaction->id)->first();
            if ($enrollment) {
                $enrollment->update([
                    'payment_status' => 'refunded',
                    'enrollment_status' => 'cancelled',
                ]);
            }

            // Log refund
            Log::info('Refund processed for transaction: ' . $transaction->transaction_id);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error handling charge refunded webhook: ' . $e->getMessage());
        }
    }

    /**
     * Handle dispute created.
     */
    protected function handleDisputeCreated($dispute)
    {
        try {
            DB::beginTransaction();

            // Find the transaction by Stripe charge ID
            $transaction = PaymentTransaction::where('stripe_charge_id', $dispute->charge)->first();

            if (!$transaction) {
                Log::error('Transaction not found for dispute charge: ' . $dispute->charge);
                return;
            }

            // Update transaction status
            $transaction->update([
                'transaction_status' => 'disputed',
                'error_message' => 'Dispute created: ' . $dispute->reason,
                'error_code' => 'dispute',
            ]);

            // Log dispute
            Log::warning('Dispute created for transaction: ' . $transaction->transaction_id);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error handling dispute created webhook: ' . $e->getMessage());
        }
    }

    /**
     * Test webhook endpoint for development.
     */
    public function testWebhook()
    {
        return response()->json([
            'message' => 'Webhook endpoint is working!',
            'timestamp' => now(),
            'endpoint' => route('stripe.webhook'),
        ]);
    }
}
