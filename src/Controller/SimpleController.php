<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @author Lukas Levickas
 */
class SimpleController extends AbstractController
{
    /**
     * render index page
     *
     * @return string
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }

    /**
     * Execute stripe charge
     *
     * @return string
     */
    public function charge(Request $r)
    {
        $secretKey = 'sk_test_slezupO7CjZyXfjAFmEY8L0H';
        $token = $r->get('stripeToken');

        if (!$token) {
            return $this->renderChargeStatus(false);
        }

        try {
            \Stripe\Stripe::setApiKey($secretKey);
            $charge = \Stripe\Charge::create([
                'amount' => 100,
                'currency' => 'eur',
                'description' => 'Stripe test charge',
                'source' => $token,
            ]);

            return $this->renderChargeStatus($charge->paid, $charge->outcome->seller_message);
        } catch (\Stripe\Error\InvalidRequest $e) {
            return $this->renderChargeStatus(false, $e->getMessage());
        }
    }

    /**
     * Render charge status page
     *
     * @return void
     */
    private function renderChargeStatus(bool $status, string $outcomeMessage = 'Empty seller message')
    {
        return $this->render('charge.html.twig', [
            'status' => $status,
            'outcomeMessage' => $outcomeMessage,
        ]);
    }
}
