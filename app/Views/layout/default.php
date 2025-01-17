<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Inventory System' ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= base_url() ?>"><?= lang('Inventory System') ?></a>
        <?php if(session()->get('isLoggedIn')): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>"><?= lang('dashboard') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('products') ?>"><?= lang('products') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('categories') ?>"><?= lang('categories') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('transactions') ?>"><?= lang('transactions') ?></a>
                    </li>
                    <?php if(session()->get('role') === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('users') ?>"><?= lang('users') ?></a>
                        </li>
                    <?php endif; ?>

                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link"><?= lang('welcome') ?>, <?= session()->get('username') ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('auth/logout') ?>"><?= lang('logout') ?></a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</nav>

<div class="container mt-4">
    <?php if(session()->getFlashdata('message')): ?>
        <div class="alert alert-success">
            <?= lang(is_string(session()->getFlashdata('message')) ? session()->getFlashdata('message') : '') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= lang(is_string(session()->getFlashdata('error')) ? session()->getFlashdata('error') : '') ?>
        </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- WebSocket Connection -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Connect to WebSocket server
    const wsProtocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
    const ws = new WebSocket(`${wsProtocol}//${window.location.hostname}:8090`);

    ws.onopen = function() {
        console.log('Connected to WebSocket server');
    };

    ws.onmessage = function(e) {
        const data = JSON.parse(e.data);
        if (data.type === 'stock_update') {
            updateProductStock(data.product);
        }
    };

    ws.onerror = function(e) {
        console.error('WebSocket error:', e);
    };

    ws.onclose = function() {
        console.log('Disconnected from WebSocket server');
        // Attempt to reconnect after 5 seconds
        setTimeout(function() {
            location.reload();
        }, 5000);
    };

    function updateProductStock(product) {
        // Update stock display in products table
        const stockCell = document.querySelector(`tr[data-product-id="${product.id}"] .stock-value`);
        if (stockCell) {
            stockCell.textContent = product.stock;
            stockCell.classList.add('stock-updated');
            setTimeout(() => {
                stockCell.classList.remove('stock-updated');
            }, 2000);
        }

        // Update stock display in dashboard cards if they exist
        const stockCard = document.querySelector(`[data-product-stock-id="${product.id}"]`);
        if (stockCard) {
            stockCard.textContent = product.stock;
        }
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let ws = null;
    let reconnectAttempts = 0;
    const maxReconnectAttempts = 5;

    function connectWebSocket() {
        try {
            ws = new WebSocket('ws://127.0.0.1:8090');

            ws.onopen = function() {
                console.log('Connected to WebSocket server');
                reconnectAttempts = 0;
            };

            ws.onmessage = function(e) {
                try {
                    const data = JSON.parse(e.data);
                    if (data.type === 'stock_update') {
                        updateProductStock(data.product);
                    }
                } catch (error) {
                    console.error('Error processing message:', error);
                }
            };

            ws.onerror = function(e) {
                console.log('WebSocket error:', e);
            };

            ws.onclose = function() {
                console.log('Disconnected from WebSocket server');
                if (reconnectAttempts < maxReconnectAttempts) {
                    reconnectAttempts++;
                    console.log(`Attempting to reconnect (${reconnectAttempts}/${maxReconnectAttempts})...`);
                    setTimeout(connectWebSocket, 5000);
                }
            };
        } catch (error) {
            console.error('WebSocket connection error:', error);
        }
    }

    function updateProductStock(product) {
        const stockCell = document.querySelector(`tr[data-product-id="${product.id}"] .stock-value`);
        if (stockCell) {
            stockCell.textContent = product.stock;
            stockCell.classList.add('stock-updated');
            setTimeout(() => {
                stockCell.classList.remove('stock-updated');
            }, 2000);
        }
    }

    // Don't auto-reconnect if connection fails
    try {
        connectWebSocket();
    } catch (error) {
        console.error('Initial WebSocket connection failed:', error);
    }
});
</script>

<style>
.stock-updated {
    animation: highlight 2s;
}

@keyframes highlight {
    0% { background-color: #ffd700; }
    100% { background-color: transparent; }
}
</style>
</body>
</html>