<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Point de Vente - Dar Zerrouk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-50">

    <!-- Top Bar -->
    <div class="bg-teal-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">📚 Dar Zerrouk - Point de Vente</h1>
                <p class="text-sm opacity-90">Caissier: {{ auth()->user()->name }}</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm opacity-90">{{ now()->format('d/m/Y') }}</p>
                    <p class="text-lg font-semibold" id="current-time"></p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-white text-teal-600 px-4 py-2 rounded-lg hover:bg-gray-100 font-semibold">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="container mx-auto p-6">
        <div class="grid grid-cols-12 gap-6">
            
            <!-- Left: Search & Cart -->
            <div class="col-span-8 space-y-6">
                
                <!-- Search Box -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">🔍 Rechercher un livre</h2>
                    
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <input 
                                type="text" 
                                id="book-search"
                                placeholder="Titre, ISBN, ou Code-barres..."
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent text-lg"
                                autofocus
                            />
                            <div id="search-results" class="absolute z-10 bg-white border rounded-lg mt-1 shadow-lg hidden max-h-64 overflow-y-auto w-full max-w-2xl"></div>
                        </div>
                        <button 
                            id="scan-barcode-btn"
                            class="bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 font-semibold flex items-center gap-2"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            Scanner
                        </button>
                    </div>
                </div>

                <!-- Cart -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">🛒 Panier</h2>
                    
                    <div id="cart-items" class="space-y-3 mb-4 min-h-[200px]">
                        <p class="text-gray-400 text-center py-8">Le panier est vide</p>
                    </div>

                    <div class="border-t pt-4">
                        <button 
                            id="clear-cart-btn"
                            class="text-red-600 hover:text-red-800 font-semibold"
                        >
                            🗑️ Vider le panier
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right: Summary & Payment -->
            <div class="col-span-4 space-y-6">
                
                <!-- Summary -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">💰 Résumé</h2>
                    
                    <div class="space-y-3 text-lg">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sous-total:</span>
                            <span id="subtotal" class="font-semibold">0.00 DH</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">TVA (20%):</span>
                            <span id="tax" class="font-semibold">0.00 DH</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between text-2xl font-bold text-teal-600">
                            <span>Total:</span>
                            <span id="total">0.00 DH</span>
                        </div>
                    </div>
                </div>

                <!-- Payment -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">💳 Paiement</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Méthode de paiement</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button class="payment-method-btn active" data-method="cash">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Espèces
                                </button>
                                <button class="payment-method-btn" data-method="card">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    Carte
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Montant reçu (DH)</label>
                            <input 
                                type="number" 
                                id="amount-paid"
                                step="0.01"
                                placeholder="0.00"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent text-lg"
                            />
                        </div>

                        <div class="bg-gray-100 p-4 rounded-lg">
                            <div class="flex justify-between text-lg">
                                <span class="font-medium">Monnaie à rendre:</span>
                                <span id="change" class="font-bold text-green-600">0.00 DH</span>
                            </div>
                        </div>

                        <button 
                            id="complete-sale-btn"
                            class="w-full bg-green-600 text-white py-4 rounded-lg hover:bg-green-700 font-bold text-xl shadow-lg disabled:bg-gray-300 disabled:cursor-not-allowed"
                            disabled
                        >
                            ✓ Valider la vente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .payment-method-btn {
            @apply px-4 py-3 border-2 border-gray-300 rounded-lg hover:border-teal-500 transition font-semibold;
        }
        .payment-method-btn.active {
            @apply bg-teal-600 text-white border-teal-600;
        }
        .cart-item {
            @apply flex justify-between items-center p-3 bg-gray-50 rounded-lg;
        }
    </style>

    <script>
        // Cart state
        let cart = [];
        let paymentMethod = 'cash';

        // Update time
        setInterval(() => {
            document.getElementById('current-time').textContent = new Date().toLocaleTimeString('fr-FR');
        }, 1000);

        // CSRF token setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Book search with autocomplete
        let searchTimeout;
        $('#book-search').on('input', function() {
            clearTimeout(searchTimeout);
            const term = $(this).val();
            
            if (term.length < 2) {
                $('#search-results').addClass('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                $.get(`/pos/search?term=${encodeURIComponent(term)}`, function(books) {
                    displaySearchResults(books);
                });
            }, 300);
        });

        // Display search results
        function displaySearchResults(books) {
            const $results = $('#search-results');
            
            if (books.length === 0) {
                $results.addClass('hidden');
                return;
            }

            $results.html(books.map(book => `
                <div class="search-result-item p-3 hover:bg-gray-100 cursor-pointer border-b" data-book='${JSON.stringify(book)}'>
                    <div class="font-semibold">${book.title}</div>
                    <div class="text-sm text-gray-600">${book.author} • ${book.price} DH • Stock: ${book.stock}</div>
                </div>
            `).join('')).removeClass('hidden');
        }

        // Add book to cart from search results
        $(document).on('click', '.search-result-item', function() {
            const book = JSON.parse($(this).attr('data-book'));
            addToCart(book);
            $('#book-search').val('').focus();
            $('#search-results').addClass('hidden');
        });

        // Hide search results when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#book-search, #search-results').length) {
                $('#search-results').addClass('hidden');
            }
        });

        // Add book to cart
        function addToCart(book) {
            const existing = cart.find(item => item.id === book.id);
            
            if (existing) {
                if (existing.quantity < book.stock) {
                    existing.quantity++;
                } else {
                    alert('Stock insuffisant!');
                    return;
                }
            } else {
                cart.push({
                    id: book.id,
                    title: book.title,
                    author: book.author,
                    price: parseFloat(book.price),
                    quantity: 1,
                    stock: book.stock
                });
            }
            
            updateCart();
        }

        // Update cart display
        function updateCart() {
            const $cartItems = $('#cart-items');
            
            if (cart.length === 0) {
                $cartItems.html('<p class="text-gray-400 text-center py-8">Le panier est vide</p>');
                updateTotals();
                return;
            }

            $cartItems.html(cart.map((item, index) => `
                <div class="cart-item">
                    <div class="flex-1">
                        <div class="font-semibold">${item.title}</div>
                        <div class="text-sm text-gray-600">${item.author} • ${item.price} DH</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="qty-btn" onclick="changeQuantity(${index}, -1)">-</button>
                        <span class="font-semibold w-8 text-center">${item.quantity}</span>
                        <button class="qty-btn" onclick="changeQuantity(${index}, 1)">+</button>
                        <span class="font-bold text-teal-600 w-24 text-right">${(item.price * item.quantity).toFixed(2)} DH</span>
                        <button class="text-red-600 hover:text-red-800" onclick="removeFromCart(${index})">✕</button>
                    </div>
                </div>
            `).join(''));

            updateTotals();
        }

        // Change quantity
        function changeQuantity(index, delta) {
            const item = cart[index];
            const newQty = item.quantity + delta;
            
            if (newQty <= 0) {
                removeFromCart(index);
            } else if (newQty <= item.stock) {
                item.quantity = newQty;
                updateCart();
            } else {
                alert('Stock insuffisant!');
            }
        }

        // Remove from cart
        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCart();
        }

        // Clear cart
        $('#clear-cart-btn').on('click', function() {
            if (cart.length > 0 && confirm('Vider le panier?')) {
                cart = [];
                updateCart();
            }
        });

        // Update totals
        function updateTotals() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const tax = subtotal * 0.20;
            const total = subtotal + tax;

            $('#subtotal').text(subtotal.toFixed(2) + ' DH');
            $('#tax').text(tax.toFixed(2) + ' DH');
            $('#total').text(total.toFixed(2) + ' DH');

            // Enable/disable complete button
            $('#complete-sale-btn').prop('disabled', cart.length === 0);

            updateChange();
        }

        // Payment method selection
        $('.payment-method-btn').on('click', function() {
            $('.payment-method-btn').removeClass('active');
            $(this).addClass('active');
            paymentMethod = $(this).data('method');
            
            // For card payment, set amount automatically
            if (paymentMethod === 'card') {
                const total = parseFloat($('#total').text().replace(' DH', ''));
                $('#amount-paid').val(total.toFixed(2));
                updateChange();
            }
        });

        // Update change
        $('#amount-paid').on('input', updateChange);

        function updateChange() {
            const total = parseFloat($('#total').text().replace(' DH', ''));
            const paid = parseFloat($('#amount-paid').val()) || 0;
            const change = Math.max(0, paid - total);
            
            $('#change').text(change.toFixed(2) + ' DH');
            
            // Update button state
            $('#complete-sale-btn').prop('disabled', cart.length === 0 || paid < total);
        }

        // Complete sale
        $('#complete-sale-btn').on('click', function() {
            if (cart.length === 0) return;

            const total = parseFloat($('#total').text().replace(' DH', ''));
            const paid = parseFloat($('#amount-paid').val()) || 0;

            if (paid < total) {
                alert('Montant insuffisant!');
                return;
            }

            const saleData = {
                items: cart.map(item => ({
                    book_id: item.id,
                    quantity: item.quantity,
                    price: item.price
                })),
                payment_method: paymentMethod,
                amount_paid: paid
            };

            $(this).prop('disabled', true).text('Traitement...');

            $.post('/pos/sale', saleData)
                .done(function(response) {
                    alert(`Vente réussie!\nFacture: ${response.invoice_number}\nTotal: ${response.total.toFixed(2)} DH\nMonnaie: ${response.change.toFixed(2)} DH`);
                    
                    // Reset
                    cart = [];
                    updateCart();
                    $('#amount-paid').val('');
                    $('#book-search').focus();
                })
                .fail(function(xhr) {
                    alert('Erreur: ' + (xhr.responseJSON?.error || 'Erreur inconnue'));
                })
                .always(function() {
                    $('#complete-sale-btn').prop('disabled', false).text('✓ Valider la vente');
                });
        });

        // ============================================
// BARCODE SCANNER FUNCTIONALITY
// ============================================

let scannerBuffer = '';
let scannerTimeout;
const SCANNER_INPUT_DELAY = 50; // ms between characters from scanner
const SCANNER_MIN_LENGTH = 3;

// Hardware barcode scanner detection
// Scanners type very fast, we detect that pattern
$(document).on('keypress', function(e) {
    // Ignore if user is typing in an input field (except search)
    if ($(e.target).is('input, textarea') && !$(e.target).is('#book-search')) {
        return;
    }

    // Clear previous timeout
    clearTimeout(scannerTimeout);

    // Add character to buffer
    if (e.key && e.key.length === 1) {
        scannerBuffer += e.key;
    }

    // If Enter is pressed, process the scanned code
    if (e.key === 'Enter' && scannerBuffer.length >= SCANNER_MIN_LENGTH) {
        e.preventDefault();
        processScannedCode(scannerBuffer.trim());
        scannerBuffer = '';
        return;
    }

    // Auto-process after delay (scanner finished typing)
    scannerTimeout = setTimeout(() => {
        if (scannerBuffer.length >= SCANNER_MIN_LENGTH) {
            processScannedCode(scannerBuffer.trim());
        }
        scannerBuffer = '';
    }, SCANNER_INPUT_DELAY);
});

// Process scanned barcode/ISBN
function processScannedCode(code) {
    console.log('Scanned code:', code);
    
    // Show loading indicator
    $('#book-search').val('Recherche...').prop('disabled', true);
    
    $.get(`/pos/book/${encodeURIComponent(code)}`)
        .done(function(book) {
            addToCart(book);
            // Visual feedback
            showNotification('✓ Livre ajouté: ' + book.title, 'success');
            $('#book-search').val('').prop('disabled', false).focus();
        })
        .fail(function() {
            showNotification('✗ Livre non trouvé: ' + code, 'error');
            $('#book-search').val(code).prop('disabled', false).select();
        });
}

// Manual scanner button click
$('#scan-barcode-btn').on('click', function() {
    // Create custom modal for manual entry
    const code = prompt('📷 Entrez le code-barres ou ISBN:');
    if (code && code.trim()) {
        processScannedCode(code.trim());
    }
});

// Notification system
function showNotification(message, type = 'success') {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const notification = $(`
        <div class="fixed top-20 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 notification-toast">
            ${message}
        </div>
    `);
    
    $('body').append(notification);
    
    // Fade out after 3 seconds
    setTimeout(() => {
        notification.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

// Clear scanner buffer on focus change
$('#book-search, #amount-paid').on('focus', function() {
    scannerBuffer = '';
    clearTimeout(scannerTimeout);
});

        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            // F1: Focus search
            if (e.key === 'F1') {
                e.preventDefault();
                $('#book-search').focus();
            }
            // F2: Focus amount
            if (e.key === 'F2') {
                e.preventDefault();
                $('#amount-paid').focus();
            }
            // F9: Complete sale
            if (e.key === 'F9') {
                e.preventDefault();
                if (!$('#complete-sale-btn').prop('disabled')) {
                    $('#complete-sale-btn').click();
                }
            }
        });
    </script>

</body>
</html>
