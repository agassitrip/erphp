<?php

declare(strict_types=1);

namespace App\Helpers;

class MobileComponent
{
    public static function mobileNavigation(array $items, string $currentPage = ''): string
    {
        $html = '<nav class="mobile-nav d-flex d-md-none">';
        
        foreach ($items as $item) {
            $activeClass = ($currentPage === $item['page']) ? ' active' : '';
            $html .= sprintf(
                '<a href="%s" class="mobile-nav-item%s">
                    <i class="%s"></i>
                    <span>%s</span>
                </a>',
                $item['url'],
                $activeClass,
                $item['icon'],
                $item['label']
            );
        }
        
        $html .= '</nav>';
        return $html;
    }

    public static function mobileProductCard(array $product): string
    {
        $price = ViewHelper::formatCurrency((float)$product['price']);
        $stock = $product['stock_quantity'] ?? 0;
        $isOutOfStock = $stock <= 0;
        
        return sprintf(
            '<div class="mobile-product-card">
                <div class="mobile-product-image">
                    <i class="bi bi-box"></i>
                </div>
                <div class="mobile-product-info">
                    <div class="mobile-product-title">%s</div>
                    <div class="mobile-product-stock">Estoque: %d un.</div>
                    <div class="mobile-product-price">%s</div>
                    <form method="POST" action="/shop/add-to-cart" class="mt-2">
                        <input type="hidden" name="product_id" value="%d">
                        <div class="d-flex gap-2 mb-2">
                            <input type="number" class="form-control form-control-sm" name="quantity" 
                                   value="1" min="1" max="%d" style="max-width:60px" %s>
                            <button type="submit" class="mobile-btn-add flex-fill" %s>
                                <i class="bi bi-cart-plus"></i> Adicionar
                            </button>
                        </div>
                    </form>
                </div>
            </div>',
            htmlspecialchars($product['name']),
            $stock,
            $price,
            $product['id'],
            $stock,
            $isOutOfStock ? 'disabled' : '',
            $isOutOfStock ? 'disabled' : ''
        );
    }

    public static function mobileTableCard(array $items, array $config): string
    {
        $html = '<div class="mobile-table-card d-block d-md-none">';
        
        foreach ($items as $item) {
            $html .= '<div class="mobile-table-item">';
            $html .= '<div class="mobile-table-main">';
            
            if (isset($config['title_field'])) {
                $html .= sprintf(
                    '<div class="mobile-table-title">%s</div>',
                    htmlspecialchars($item[$config['title_field']] ?? '')
                );
            }
            
            if (isset($config['subtitle_field'])) {
                $html .= sprintf(
                    '<div class="mobile-table-subtitle">%s</div>',
                    htmlspecialchars($item[$config['subtitle_field']] ?? '')
                );
            }
            
            if (isset($config['extra_fields'])) {
                foreach ($config['extra_fields'] as $field => $label) {
                    $value = $item[$field] ?? '';
                    if ($value) {
                        $html .= sprintf(
                            '<div class="mobile-table-subtitle">%s: %s</div>',
                            $label,
                            htmlspecialchars($value)
                        );
                    }
                }
            }
            
            $html .= '</div>';
            
            if (isset($config['actions'])) {
                $html .= '<div class="mobile-table-actions">';
                foreach ($config['actions'] as $action) {
                    $url = str_replace('{id}', (string)$item['id'], $action['url']);
                    $html .= sprintf(
                        '<a href="%s" class="btn btn-%s mobile-btn-sm">
                            <i class="%s"></i>
                        </a>',
                        $url,
                        $action['type'] ?? 'primary',
                        $action['icon']
                    );
                }
                $html .= '</div>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</div>';
        return $html;
    }

    public static function mobileSearchBar(string $placeholder = 'Pesquisar...', string $action = ''): string
    {
        return sprintf(
            '<div class="mobile-search-container d-block d-md-none">
                <form method="GET" action="%s">
                    <input type="search" name="search" class="mobile-search-input" 
                           placeholder="%s" value="%s">
                </form>
            </div>',
            $action,
            $placeholder,
            htmlspecialchars($_GET['search'] ?? '')
        );
    }

    public static function mobileFloatingForm(array $fields): string
    {
        $html = '';
        
        foreach ($fields as $field) {
            $html .= '<div class="mobile-form-floating">';
            
            switch ($field['type']) {
                case 'text':
                case 'email':
                case 'password':
                case 'number':
                    $html .= sprintf(
                        '<input type="%s" id="%s" name="%s" class="mobile-form-control" 
                               placeholder=" " value="%s" %s>
                         <label for="%s" class="mobile-form-label">%s</label>',
                        $field['type'],
                        $field['name'],
                        $field['name'],
                        htmlspecialchars($field['value'] ?? ''),
                        $field['required'] ?? false ? 'required' : '',
                        $field['name'],
                        $field['label']
                    );
                    break;
                    
                case 'select':
                    $html .= sprintf(
                        '<select id="%s" name="%s" class="mobile-form-control" %s>',
                        $field['name'],
                        $field['name'],
                        $field['required'] ?? false ? 'required' : ''
                    );
                    
                    foreach ($field['options'] as $value => $label) {
                        $selected = ($field['value'] ?? '') == $value ? 'selected' : '';
                        $html .= sprintf(
                            '<option value="%s" %s>%s</option>',
                            $value,
                            $selected,
                            htmlspecialchars($label)
                        );
                    }
                    
                    $html .= '</select>';
                    $html .= sprintf('<label for="%s" class="mobile-form-label">%s</label>', 
                                   $field['name'], $field['label']);
                    break;
            }
            
            $html .= '</div>';
        }
        
        return $html;
    }

    public static function mobileCartItem(array $item): string
    {
        $price = ViewHelper::formatCurrency((float)$item['final_price']);
        $total = ViewHelper::formatCurrency((float)$item['total']);
        
        return sprintf(
            '<div class="mobile-cart-item">
                <div class="mobile-cart-image">
                    <i class="bi bi-box"></i>
                </div>
                <div class="mobile-cart-info">
                    <div class="mobile-cart-title">%s</div>
                    <div class="mobile-cart-price">%s</div>
                    <div class="text-muted small">Total: %s</div>
                </div>
                <div class="mobile-quantity-control">
                    <form method="POST" action="/shop/update-cart" class="d-inline">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="key" value="%s">
                        <button type="button" class="mobile-quantity-btn" onclick="decreaseQty(this)">-</button>
                        <input type="number" name="quantity" class="mobile-quantity-input" 
                               value="%d" min="1" max="%d" onchange="this.form.submit()">
                        <button type="button" class="mobile-quantity-btn" onclick="increaseQty(this)">+</button>
                    </form>
                </div>
            </div>',
            htmlspecialchars($item['name']),
            $price,
            $total,
            $item['key'] ?? '',
            $item['quantity'],
            ($item['available_stock'] ?? 0) + $item['quantity']
        );
    }

    public static function mobileFab(string $url, string $icon = 'bi-plus', string $tooltip = ''): string
    {
        return sprintf(
            '<a href="%s" class="mobile-fab d-block d-md-none" %s>
                <i class="%s"></i>
            </a>',
            $url,
            $tooltip ? "title=\"$tooltip\"" : '',
            $icon
        );
    }

    public static function mobileAlert(string $message, string $type = 'info'): string
    {
        $icons = [
            'success' => 'bi-check-circle',
            'danger' => 'bi-exclamation-triangle',
            'warning' => 'bi-exclamation-triangle',
            'info' => 'bi-info-circle'
        ];
        
        return sprintf(
            '<div class="mobile-alert alert alert-%s">
                <i class="%s me-2"></i>%s
            </div>',
            $type,
            $icons[$type] ?? 'bi-info-circle',
            $message
        );
    }

    public static function mobileAvatar(string $name, string $color = ''): string
    {
        $initials = strtoupper(substr($name, 0, 1));
        $bgColor = $color ?: 'var(--primary-color)';
        
        return sprintf(
            '<div class="mobile-avatar" style="background: %s">%s</div>',
            $bgColor,
            $initials
        );
    }

    public static function mobileBottomSheet(string $id, string $title, string $content): string
    {
        return sprintf(
            '<div class="modal fade" id="%s" tabindex="-1">
                <div class="modal-dialog modal-dialog-slideup">
                    <div class="modal-content" style="border-radius: 1rem 1rem 0 0;">
                        <div class="modal-header">
                            <h5 class="modal-title">%s</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">%s</div>
                    </div>
                </div>
            </div>',
            $id,
            $title,
            $content
        );
    }
}