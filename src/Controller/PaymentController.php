<?php 
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Charge;


class PaymentController extends AbstractController
{
    /**
     * @Route("/payment", name="payment")
     */
    public function payment(): Response
    {
        $stripePublicKey = $_ENV["STRIPE_PUBLIC_KEY"];

        return $this->render('payment.html.twig', [
            'stripePublicKey' => $stripePublicKey
        ]);
    }

    /**
     * @Route("/handle-payment", name="handle_payment", methods={"POST"})
     */
    public function handlePayment(Request $request): Response
    {
        Stripe::setApiKey($_ENV["STRIPE_SECRET_KEY"]);

        $token = $request->request->get('stripeToken');

        try {
            $charge = Charge::create([
                'amount' => 2000, // en centimes
                'currency' => 'eur',
                'description' => 'Paiement de 20€',
                'source' => $token,
            ]);

            $this->addFlash('success', 'Paiement réussi !');

            return $this->redirectToRoute('payment_success');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors du paiement : ' . $e->getMessage());

            return $this->redirectToRoute('payment_error');
        }
    }

    /**
     * @Route("/payment-success", name="payment_success")
     */
    public function paymentSuccess(): Response
    {
        return $this->render('payment/success.html.twig');
    }

    /**
     * @Route("/payment-error", name="payment_error")
     */
    public function paymentError(): Response
    {
        return $this->render('payment/error.html.twig');
    }
}
