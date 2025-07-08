<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Include additional styles if needed -->
    @yield('top')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    /* Style for the suggestions box */
    .suggestions-box {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: white;
        border: 1px solid #ccc;
        max-height: 200px; /* Limits the box height */
        overflow-y: auto; /* Enables vertical scrolling */
        z-index: 1000;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
    }

    /* Style for each suggestion item */
    .suggestion-item {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #f1f1f1;
    }

    /* Hover effect */
    .suggestion-item:hover {
        background-color: #f0f0f0;
    }

    /* Active effect */
    .suggestion-item:active {
        background-color: #e0e0e0;
    }

    /* If no results, show this message */
    .suggestions-box .no-results {
        padding: 10px;
        color: #999;
        font-style: italic;
        text-align: center;
    }
</style>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @if(auth()->check())
                <div class="container mb-3">
                    <div class="alert alert-info" role="alert">
                        Anda login sebagai: <strong>{{ ucfirst(auth()->user()->role) }}</strong>
                    </div>
                </div>
            @endif

    @yield('content')
</main>
    </div>

    <!-- Include scripts at the end of the body -->
    @yield('bot')

    <!-- produk -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productSearch = document.getElementById('product_search');
            const suggestionsBox = document.getElementById('product-suggestions');

            // Listen for input changes to trigger product search
            productSearch.addEventListener('input', function () {
                const query = productSearch.value;

                if (query.length > 2) {
                    fetch(`/search-products?query=${query}`)
                        .then(response => response.json())
                        .then(data => {
                            // Clear previous suggestions
                            suggestionsBox.innerHTML = '';

                            // Show 5 product suggestions
                            data.products.forEach(product => {
                                const suggestionDiv = document.createElement('div');
                                suggestionDiv.classList.add('suggestion-item');
                                suggestionDiv.textContent = product.nama;
                                suggestionDiv.dataset.productId = product.id;

                                // Append the suggestion to the suggestions box
                                suggestionsBox.appendChild(suggestionDiv);
                            });

                            if (data.products.length === 0) {
                                suggestionsBox.innerHTML = '<div class="no-results">No products found</div>';
                            }
                        });
                } else {
                    suggestionsBox.innerHTML = ''; // Clear suggestions if the input is short
                }
            });

            // Listen for clicking a suggestion to autofill the input
            suggestionsBox.addEventListener('click', function (event) {
                if (event.target.classList.contains('suggestion-item')) {
                    const selectedProduct = event.target;
                    productSearch.value = selectedProduct.textContent;
                    document.getElementById('product_id').value = selectedProduct.dataset.productId;
                    document.getElementById('product_id_hidden').value = selectedProduct.dataset.productId; // <-- tambahkan ini

                    // Clear suggestions after selecting one
                    suggestionsBox.innerHTML = '';
                }
            });
        });
    </script>
<!-- customer -->
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const customerSearch = document.getElementById('customer_search');
            const suggestionsBox = document.getElementById('customer-suggestions');

            // Listen for input changes to trigger customer search
            customerSearch.addEventListener('input', function () {
                const query = customerSearch.value;

                if (query.length > 2) {
                    fetch(`/search-customers?query=${query}`)
                        .then(response => response.json())
                        .then(data => {
                            // Clear previous suggestions
                            suggestionsBox.innerHTML = '';

                            // Show 5 customer suggestions
                            data.customers.forEach(customer => {
                                const suggestionDiv = document.createElement('div');
                                suggestionDiv.classList.add('suggestion-item');
                                suggestionDiv.textContent = customer.nama;
                                suggestionDiv.dataset.customerId = customer.id;

                                // Append the suggestion to the suggestions box
                                suggestionsBox.appendChild(suggestionDiv);
                            });

                            if (data.customers.length === 0) {
                                suggestionsBox.innerHTML = '<div class="no-results">No customers found</div>';
                            }
                        });
                } else {
                    suggestionsBox.innerHTML = ''; // Clear suggestions if the input is short
                }
            });

            // Listen for clicking a suggestion to autofill the input
            suggestionsBox.addEventListener('click', function (event) {
                if (event.target.classList.contains('suggestion-item')) {
                    const selectedCustomer = event.target;
                    customerSearch.value = selectedCustomer.textContent;
                    document.getElementById('customer_id').value = selectedCustomer.dataset.customerId;
                    document.getElementById('customer_id_hidden').value = selectedCustomer.dataset.customerId; // <-- tambahkan ini

                    // Clear suggestions after selecting one
                    suggestionsBox.innerHTML = '';
                }
            });
        });
    </script>
    <!-- supplier -->
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const supplierSearch = document.getElementById('supplier_search');
            const suggestionsBox = document.getElementById('supplier-suggestions');

            // Listen for input changes to trigger supplier search
            supplierSearch.addEventListener('input', function () {
                const query = supplierSearch.value;

                if (query.length > 2) {
                    fetch(`/search-suppliers?query=${query}`)
                        .then(response => response.json())
                        .then(data => {
                            // Clear previous suggestions
                            suggestionsBox.innerHTML = '';

                            // Show 5 supplier suggestions
                            data.suppliers.forEach(supplier => {
                                const suggestionDiv = document.createElement('div');
                                suggestionDiv.classList.add('suggestion-item');
                                suggestionDiv.textContent = supplier.nama;
                                suggestionDiv.dataset.supplierId = supplier.id;

                                // Append the suggestion to the suggestions box
                                suggestionsBox.appendChild(suggestionDiv);
                            });

                            if (data.suppliers.length === 0) {
                                suggestionsBox.innerHTML = '<div class="no-results">No suppliers found</div>';
                            }
                        });
                } else {
                    suggestionsBox.innerHTML = ''; // Clear suggestions if the input is short
                }
            });

            // Listen for clicking a suggestion to autofill the input
            suggestionsBox.addEventListener('click', function (event) {
                if (event.target.classList.contains('suggestion-item')) {
                    const selectedSupplier = event.target;
                    supplierSearch.value = selectedSupplier.textContent;
                    document.getElementById('supplier_id').value = selectedSupplier.dataset.supplierId;
                    document.getElementById('supplier_id_hidden').value = selectedSupplier.dataset.supplierId; // <-- tambahkan ini

                    // Clear suggestions after selecting one
                    suggestionsBox.innerHTML = '';
                }
            });
        });
    </script>
</body>
</html>
