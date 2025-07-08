<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">Fitur</li>

            <li class="active">
                <a href="{{ url('/home') }}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="{{ route('products.index') }}">
                    <i class="fa fa-cubes"></i> <span>Produk</span>
                </a>
            </li>

            <li>
                <a href="{{ route('suppliers.index') }}">
                    <i class="fa fa-truck"></i> <span>Supplier</span>
                </a>
            </li>

            <li>
                <a href="{{ route('customers.index') }}">
                    <i class="fa fa-users"></i> <span>Pelanggan</span>
                </a>
            </li>

            <li>
                <a href="{{ route('productsIn.index') }}">
                    <i class="fa fa-plus"></i> <span>Produk Masuk</span>
                </a>
            </li>

            <li>
                <a href="{{ route('productsOut.index') }}">
                    <i class="fa fa-minus"></i> <span>Produk Keluar</span>
                </a>
            </li>
        </ul>
    </section>
</aside>
