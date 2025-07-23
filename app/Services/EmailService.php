<?php

declare(strict_types=1);

namespace App\Services;

class EmailService
{
    private string $fromEmail = 'noreply@erphp.com';
    private string $fromName = 'Teste Montink';

    public function sendOrderConfirmation(array $orderData, array $items): bool
    {
        $to = $orderData['customer_email'];
        $subject = "Confirmação de Pedido #{$orderData['id']} - Teste Montink";
        
        $body = $this->generateOrderEmailTemplate($orderData, $items);
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];

        return mail($to, $subject, $body, implode("\r\n", $headers));
    }

    private function generateOrderEmailTemplate(array $order, array $items): string
    {
        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= "
                <tr>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>
                        {$item['product_name']}<br>
                        <small style='color: #666;'>Código: {$item['product_code']}</small>
                    </td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: center;'>
                        {$item['quantity']}
                    </td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>
                        " . $this->formatCurrency($item['price']) . "
                    </td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>
                        " . $this->formatCurrency($item['total']) . "
                    </td>
                </tr>
            ";
        }

        $couponHtml = '';
        if ($order['coupon_code']) {
            $couponHtml = "
                <tr>
                    <td colspan='3' style='padding: 10px; text-align: right; color: #28a745;'>
                        <strong>Desconto (Cupom {$order['coupon_code']}):</strong>
                    </td>
                    <td style='padding: 10px; text-align: right; color: #28a745;'>
                        <strong>-" . $this->formatCurrency($order['coupon_discount']) . "</strong>
                    </td>
                </tr>
            ";
        }

        $shippingText = $order['shipping_cost'] > 0 
            ? $this->formatCurrency($order['shipping_cost'])
            : '<span style="color: #28a745;">Grátis</span>';

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Confirmação de Pedido</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='text-align: center; margin-bottom: 30px;'>
                    <h1 style='color: #007bff; margin-bottom: 10px;'>Teste Montink</h1>
                    <h2 style='color: #28a745; margin: 0;'>Pedido Confirmado!</h2>
                </div>

                <div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;'>
                    <h3 style='margin-top: 0;'>Olá, {$order['customer_name']}!</h3>
                    <p>Seu pedido <strong>#{$order['id']}</strong> foi confirmado com sucesso!</p>
                    <p><strong>Data do Pedido:</strong> " . date('d/m/Y H:i', strtotime($order['created_at'])) . "</p>
                </div>

                <h3>Itens do Pedido</h3>
                <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                    <thead>
                        <tr style='background: #007bff; color: white;'>
                            <th style='padding: 10px; text-align: left;'>Produto</th>
                            <th style='padding: 10px; text-align: center;'>Qtd</th>
                            <th style='padding: 10px; text-align: right;'>Preço</th>
                            <th style='padding: 10px; text-align: right;'>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHtml}
                        <tr>
                            <td colspan='3' style='padding: 10px; text-align: right;'>
                                <strong>Subtotal:</strong>
                            </td>
                            <td style='padding: 10px; text-align: right;'>
                                <strong>" . $this->formatCurrency($order['subtotal']) . "</strong>
                            </td>
                        </tr>
                        {$couponHtml}
                        <tr>
                            <td colspan='3' style='padding: 10px; text-align: right;'>
                                <strong>Frete:</strong>
                            </td>
                            <td style='padding: 10px; text-align: right;'>
                                <strong>{$shippingText}</strong>
                            </td>
                        </tr>
                        <tr style='background: #f8f9fa;'>
                            <td colspan='3' style='padding: 15px; text-align: right; font-size: 18px;'>
                                <strong>TOTAL:</strong>
                            </td>
                            <td style='padding: 15px; text-align: right; font-size: 18px; color: #28a745;'>
                                <strong>" . $this->formatCurrency($order['total']) . "</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h3>Dados de Entrega</h3>
                <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>
                    <p style='margin: 5px 0;'><strong>Nome:</strong> {$order['customer_name']}</p>
                    <p style='margin: 5px 0;'><strong>E-mail:</strong> {$order['customer_email']}</p>
                    " . ($order['customer_phone'] ? "<p style='margin: 5px 0;'><strong>Telefone:</strong> {$order['customer_phone']}</p>" : "") . "
                    " . ($order['customer_address'] ? "
                    <p style='margin: 5px 0;'><strong>Endereço:</strong><br>
                    {$order['customer_address']}<br>
                    {$order['customer_city']} - {$order['customer_state']}<br>
                    CEP: {$order['customer_cep']}</p>
                    " : "") . "
                </div>

                <div style='background: #e9ecef; padding: 15px; border-radius: 5px; margin-top: 30px;'>
                    <p style='margin: 0; text-align: center;'>
                        <strong>Obrigado por escolher a Teste Montink!</strong><br>
                        Em caso de dúvidas, entre em contato conosco.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function formatCurrency(float $value): string
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}