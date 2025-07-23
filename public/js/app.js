$(document).ready(function() {
    $('.table').each(function() {
        if ($(this).hasClass('no-datatable')) return;
        
        $(this).DataTable({
            language: {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                "sInfoThousands": ".",
                "sLengthMenu": "_MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum registro encontrado",
                "sSearch": "Pesquisar",
                "oPaginate": {
                    "sNext": "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                },
                "select": {
                    "rows": {
                        "_": "Selecionado %d linhas",
                        "0": "Nenhuma linha selecionada",
                        "1": "Selecionado 1 linha"
                    }
                }
            },
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']]
        });
    });

    $('[data-mask="phone"]').mask('(00) 00000-0000');
    $('[data-mask="cpf"]').mask('000.000.000-00');
    $('[data-mask="cnpj"]').mask('00.000.000/0000-00');
    $('[data-mask="cep"]').mask('00000-000');
    $('[data-mask="currency"]').mask('#.##0,00', {reverse: true});

    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Processando...');
        
        setTimeout(function() {
            submitBtn.prop('disabled', false);
            submitBtn.html(submitBtn.data('original-text') || 'Salvar');
        }, 5000);
    });

    $('.btn[data-confirm]').on('click', function(e) {
        e.preventDefault();
        const message = $(this).data('confirm');
        const href = $(this).attr('href');
        
        Swal.fire({
            title: 'Confirmação',
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });

    $('[data-toggle="tooltip"]').tooltip();

    $('.alert').each(function() {
        const alert = $(this);
        setTimeout(function() {
            alert.fadeOut();
        }, 5000);
    });
});

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('pt-BR');
}

function showLoading(element) {
    $(element).addClass('loading');
}

function hideLoading(element) {
    $(element).removeClass('loading');
}

function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Sucesso!',
        text: message,
        timer: 3000,
        showConfirmButton: false
    });
}

function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Erro!',
        text: message
    });
}

function showWarning(message) {
    Swal.fire({
        icon: 'warning',
        title: 'Atenção!',
        text: message
    });
}

function confirmDelete(message, callback) {
    Swal.fire({
        title: 'Tem certeza?',
        text: message || 'Esta ação não pode ser desfeita!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed && callback) {
            callback();
        }
    });
}

function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction() {
        const context = this;
        const args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

if (typeof searchProducts === 'undefined') {
    window.searchProducts = debounce(function(term) {
        if (term.length < 2) return;
        
        $.ajax({
            url: '/api/products/search',
            method: 'GET',
            data: { term: term },
            success: function(data) {
                updateProductResults(data);
            },
            error: function() {
                showError('Erro ao buscar produtos');
            }
        });
    }, 300);
}

function updateProductResults(products) {
    const resultsContainer = $('#product-results');
    resultsContainer.empty();
    
    products.forEach(function(product) {
        const item = $(`
            <div class="list-group-item list-group-item-action" data-product-id="${product.id}">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${product.name}</h6>
                    <small>${formatCurrency(product.price)}</small>
                </div>
                <p class="mb-1">Código: ${product.code}</p>
                <small>Estoque: ${product.stock}</small>
            </div>
        `);
        
        item.on('click', function() {
            selectProduct(product);
        });
        
        resultsContainer.append(item);
    });
}

function selectProduct(product) {
    console.log('Produto selecionado:', product);
}

function increaseQty(button) {
    const input = button.parentElement.querySelector('.mobile-quantity-input');
    const max = parseInt(input.getAttribute('max')) || 99;
    const current = parseInt(input.value) || 1;
    if (current < max) {
        input.value = current + 1;
        input.dispatchEvent(new Event('change'));
    }
}

function decreaseQty(button) {
    const input = button.parentElement.querySelector('.mobile-quantity-input');
    const current = parseInt(input.value) || 1;
    if (current > 1) {
        input.value = current - 1;
        input.dispatchEvent(new Event('change'));
    }
}

function initMobileEnhancements() {
    if (window.innerWidth <= 768) {
        const searchInputs = document.querySelectorAll('.mobile-search-input');
        searchInputs.forEach(input => {
            input.addEventListener('input', debounce(function() {
                if (this.value.length >= 2) {
                    this.form.submit();
                }
            }, 500));
        });

        const mobileNavItems = document.querySelectorAll('.mobile-nav-item');
        mobileNavItems.forEach(item => {
            item.addEventListener('touchstart', function() {
                this.style.transform = 'translateY(-4px)';
            });
            
            item.addEventListener('touchend', function() {
                setTimeout(() => {
                    this.style.transform = 'translateY(-2px)';
                }, 100);
            });
        });

        const productCards = document.querySelectorAll('.mobile-product-card');
        productCards.forEach(card => {
            let touchStartY = 0;
            
            card.addEventListener('touchstart', function(e) {
                touchStartY = e.touches[0].clientY;
                this.style.transform = 'translateY(-2px)';
            });
            
            card.addEventListener('touchmove', function(e) {
                const touchY = e.touches[0].clientY;
                const deltaY = touchStartY - touchY;
                
                if (Math.abs(deltaY) > 10) {
                    this.style.transform = 'translateY(0)';
                }
            });
            
            card.addEventListener('touchend', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        const quantityInputs = document.querySelectorAll('.mobile-quantity-input');
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.form) {
                    const submitBtn = this.form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.style.opacity = '0.7';
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Atualizando...';
                    }
                    
                    setTimeout(() => {
                        this.form.submit();
                    }, 300);
                }
            });
        });

        const fabButton = document.querySelector('.mobile-fab');
        if (fabButton) {
            let lastScrollTop = 0;
            let isScrollingDown = false;
            
            window.addEventListener('scroll', function() {
                const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                if (currentScrollTop > lastScrollTop && currentScrollTop > 100) {
                    if (!isScrollingDown) {
                        fabButton.style.transform = 'translateY(100px)';
                        isScrollingDown = true;
                    }
                } else {
                    if (isScrollingDown) {
                        fabButton.style.transform = 'translateY(0)';
                        isScrollingDown = false;
                    }
                }
                
                lastScrollTop = currentScrollTop;
            });
        }
    }
}

window.addEventListener('DOMContentLoaded', initMobileEnhancements);
window.addEventListener('resize', initMobileEnhancements);
