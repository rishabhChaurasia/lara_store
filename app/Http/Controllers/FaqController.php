<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display the FAQ page.
     */
    public function index()
    {
        $faqItems = [
            [
                'question' => 'How do I track my order?',
                'answer' => 'Once your order is shipped, you\'ll receive a confirmation email with a tracking number. You can also track your order by logging into your account and visiting the "Orders" section. Click on any order to view its current status and tracking information.'
            ],
            [
                'question' => 'What is your return policy?',
                'answer' => 'We offer a 30-day return policy for most items. Products must be unused, in original packaging, and with all tags attached. Simply initiate a return from your account dashboard, print the prepaid shipping label, and send it back. Refunds are processed within 5-7 business days of receiving the return.'
            ],
            [
                'question' => 'Do you ship internationally?',
                'answer' => 'Yes! We ship to over 100 countries worldwide. International shipping costs and delivery times vary by destination. You can see the exact shipping cost and estimated delivery time at checkout before completing your purchase.'
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept all major credit cards (Visa, Mastercard, American Express), PayPal, Apple Pay, and Google Pay. All transactions are secured with industry-standard SSL encryption to protect your payment information.'
            ],
            [
                'question' => 'How long does shipping take?',
                'answer' => 'Standard shipping typically takes 3-5 business days within the US. Express shipping (1-2 business days) is also available at checkout. International orders usually arrive within 7-14 business days depending on the destination and customs processing.'
            ],
            [
                'question' => 'Can I modify or cancel my order?',
                'answer' => 'You can modify or cancel your order within 2 hours of placing it. After that, orders are processed and shipped quickly, so changes may not be possible. Please contact our customer service team immediately if you need to make changes.'
            ],
            [
                'question' => 'Do you offer gift wrapping?',
                'answer' => 'Yes! We offer premium gift wrapping for $5 per item. You can add this option during checkout and include a personalized message. Gift receipts (without prices) are included automatically with gift-wrapped orders.'
            ],
            [
                'question' => 'How can I contact customer support?',
                'answer' => 'Our customer support team is available 24/7 to help you. You can reach us through:',
                'list' => [
                    'Email: support@' . strtolower(config('app.name')) . '.com',
                    'Live chat available on our website',
                    'Phone: 1-800-LARA-STORE (Mon-Fri, 9AM-6PM EST)'
                ]
            ],
        ];
        
        return view('faq', compact('faqItems'));
    }
}
