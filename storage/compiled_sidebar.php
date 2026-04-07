<?php if(! Route::is(['pos','pos-2','pos-3','pos-4','pos-5'])): ?>
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
                <!-- Logo -->
                <div class="sidebar-logo active">
                        <a href="<?php echo e(url('index')); ?>" class="logo logo-normal">
                                <img src="<?php echo e(URL::asset('build/img/logo.svg')); ?>" alt="Img">
                        </a>
                        <a href="<?php echo e(url('index')); ?>" class="logo logo-white">
                                <img src="<?php echo e(URL::asset('build/img/logo-white.svg')); ?>" alt="Img">
                        </a>
                        <a href="<?php echo e(url('index')); ?>" class="logo-small">
                                <img src="<?php echo e(URL::asset('build/img/logo-small.png')); ?>" alt="Img">
                        </a>
                        <a id="toggle_btn" href="javascript:void(0);">
                                <i data-feather="chevrons-left" class="feather-16"></i>
                        </a>
                </div>
                <!-- /Logo -->
                <div class="modern-profile p-3 pb-0">
                        <div class="text-center rounded bg-light p-3 mb-4 user-profile">
                                <div class="avatar avatar-lg online mb-3">
                                        <img src="<?php echo e(URL::asset('build/img/customer/customer15.jpg')); ?>" alt="Img" class="img-fluid rounded-circle">
                                </div>
                                <h6 class="fs-12 fw-normal mb-1"><?php echo e(Auth::check() ? Auth::user()->name : 'Guest'); ?></h6>
                                <p class="fs-10 mb-0">Mess Member</p>
                        </div>
                        <div class="sidebar-nav mb-3">
                                <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent" role="tablist">
                                        <li class="nav-item"><a class="nav-link active border-0" href="#">Menu</a></li>
                                        <li class="nav-item"><a class="nav-link border-0" href="<?php echo e(url('chat')); ?>">Chats</a></li>
                                        <li class="nav-item"><a class="nav-link border-0" href="<?php echo e(url('email')); ?>">Inbox</a></li>
                                </ul>
                        </div>
                </div>
                <div class="sidebar-header p-3 pb-0 pt-2">
                        <div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
                                <div class="avatar avatar-md onlin">
                                        <img src="<?php echo e(URL::asset('build/img/customer/customer15.jpg')); ?>" alt="Img" class="img-fluid rounded-circle">
                                </div>
                                <div class="text-start sidebar-profile-info ms-2">
                                        <h6 class="fs-12 fw-normal mb-1"><?php echo e(Auth::check() ? Auth::user()->name : 'Guest'); ?></h6>
                                        <p class="fs-10"><?php echo e(Auth::check() ? (Auth::user()->getActiveMess() ? Auth::user()->getActiveMess()->name : 'No Mess') : 'Guest'); ?></p>
                                </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between menu-item mb-3">
                                <div>
                                        <a href="<?php echo e(url('index')); ?>" class="btn btn-sm btn-icon bg-light">
                                                <i class="ti ti-layout-grid-remove"></i>
                                        </a>
                                </div>
                                <div>
                                        <a href="<?php echo e(url('chat')); ?>" class="btn btn-sm btn-icon bg-light">
                                                <i class="ti ti-brand-hipchat"></i>
                                        </a>
                                </div>
                                <div>
                                        <a href="<?php echo e(url('email')); ?>" class="btn btn-sm btn-icon bg-light position-relative">
                                                <i class="ti ti-message"></i>
                                        </a>
                                </div>
                                <div class="notification-item">
                                        <a href="<?php echo e(url('activities')); ?>" class="btn btn-sm btn-icon bg-light position-relative">
                                                <i class="ti ti-bell"></i>
                                                <span class="notification-status-dot"></span>
                                        </a>
                                </div>
                                <div class="me-0">
                                        <a href="<?php echo e(url('general-settings')); ?>" class="btn btn-sm btn-icon bg-light">
                                                <i class="ti ti-settings"></i>
                                        </a>
                                </div>
                        </div>
                </div>
                <div class="sidebar-inner slimscroll">
                        <div id="sidebar-menu" class="sidebar-menu">
                                <ul>
                                        <?php echo $__env->make('layout.partials.mess-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Main</h6>
                                                <ul>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"  class="<?php echo e(Request::is('index', '/', 'sales-dashboard','admin-dashboard') ? 'active subdrop' : ''); ?>"><i class="ti ti-layout-grid fs-16 me-2"></i><span>Dashboard</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('index')); ?>"
                                                                                class="<?php echo e(Request::is('index', '/') ? 'active' : ''); ?>">Admin Dashboard</a></li>
                                                                        <li><a href="<?php echo e(url('admin-dashboard')); ?>"  class="<?php echo e(Request::is('admin-dashboard') ? 'active' : ''); ?>">Admin Dashboard 2</a></li>
                                                                        <li><a href="<?php echo e(url('sales-dashboard')); ?>"
                                                                                class="<?php echo e(Request::is('sales-dashboard') ? 'active' : ''); ?>">Sales Dashboard</a>
                                                                        </li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('dashboard', 'companies', 'subscription','packages','domain','purchase-transaction') ? 'active subdrop' : ''); ?>"><i class="ti ti-user-edit fs-16 me-2"></i><span>Super Admin</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('dashboard')); ?>" class="<?php echo e(Request::is('dashboard') ? 'active' : ''); ?>">Dashboard</a></li>
                                                                        <li><a href="<?php echo e(url('companies')); ?>" class="<?php echo e(Request::is('companies') ? 'active' : ''); ?>">Companies</a></li>
                                                                        <li><a href="<?php echo e(url('subscription')); ?>" class="<?php echo e(Request::is('subscription') ? 'active' : ''); ?>">Subscriptions</a></li>
                                                                        <li><a href="<?php echo e(url('packages')); ?>" class="<?php echo e(Request::is('packages') ? 'active' : ''); ?>">Packages</a></li>
                                                                        <li><a href="<?php echo e(url('domain')); ?>" class="<?php echo e(Request::is('domain') ? 'active' : ''); ?>">Domain</a></li>
                                                                        <li><a href="<?php echo e(url('purchase-transaction')); ?>" class="<?php echo e(Request::is('purchase-transaction') ? 'active' : ''); ?>">Purchase Transaction</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('chat', 'video-call', 'audio-call','call-history','calendar','contacts','email','todo','notes','file-manager'
                                                                ,'projects','products','orders','cart','checkout','wishlist','reviews','social-feed','search-list') ? 'active subdrop' : ''); ?>"><i class="ti ti-brand-apple-arcade fs-16 me-2"></i><span>Application</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li class="<?php echo e(Request::is('chat') ? 'active' : ''); ?>"><a href="<?php echo e(url('chat')); ?>">Chat</a></li>
                                                                        <li class="submenu submenu-two" ><a href="javascript:void(0);" class="<?php echo e(Request::is('video-call', 'audio-call', 'call-history') ? 'active subdrop' : ''); ?>">Call<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('video-call')); ?>" class="<?php echo e(Request::is('video-call') ? 'active' : ''); ?>">Video Call</a></li>
                                                                                        <li><a href="<?php echo e(url('audio-call')); ?>" class="<?php echo e(Request::is('audio-call') ? 'active' : ''); ?>">Audio Call</a></li>
                                                                                        <li><a href="<?php echo e(url('call-history')); ?>" class="<?php echo e(Request::is('call-history') ? 'active' : ''); ?>">Call History</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li><a href="<?php echo e(url('calendar')); ?>" class="<?php echo e(Request::is('calendar') ? 'active' : ''); ?>">Calendar</a></li>
                                                                        <li><a href="<?php echo e(url('contacts')); ?>" class="<?php echo e(Request::is('contacts') ? 'active' : ''); ?>">Contacts</a></li>
                                                                        <li><a href="<?php echo e(url('email')); ?>" class="<?php echo e(Request::is('email') ? 'active' : ''); ?>">Email</a></li>
                                                                        <li><a href="<?php echo e(url('todo')); ?>" class="<?php echo e(Request::is('todo') ? 'active' : ''); ?>">To Do</a></li>
                                                                        <li><a href="<?php echo e(url('notes')); ?>" class="<?php echo e(Request::is('notes') ? 'active' : ''); ?>">Notes</a></li>
                                                                        <li><a href="<?php echo e(url('file-manager')); ?>" class="<?php echo e(Request::is('file-manager') ? 'active' : ''); ?>">File Manager</a></li>
                                                                        <li><a href="<?php echo e(url('projects')); ?>" class="<?php echo e(Request::is('projects') ? 'active' : ''); ?>">Projects</a></li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);" class="<?php echo e(Request::is('products','orders','customers','cart','checkout','wishlist','reviews') ? 'active' : ''); ?>">Ecommerce<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('products')); ?>" class="<?php echo e(Request::is('products') ? 'active' : ''); ?>">Products</a></li>
                                                                                        <li><a href="<?php echo e(url('orders')); ?>" class="<?php echo e(Request::is('orders') ? 'active' : ''); ?>">Orders</a></li>
                                                                                        <li><a href="<?php echo e(url('customers')); ?>" class="<?php echo e(Request::is('customers') ? 'active' : ''); ?>">Customers</a></li>
                                                                                        <li><a href="<?php echo e(url('cart')); ?>" class="<?php echo e(Request::is('cart') ? 'active' : ''); ?>">Cart</a></li>
                                                                                        <li><a href="<?php echo e(url('checkout')); ?>" class="<?php echo e(Request::is('checkout') ? 'active' : ''); ?>">Checkout</a></li>
                                                                                        <li><a href="<?php echo e(url('wishlist')); ?>" class="<?php echo e(Request::is('wishlist') ? 'active' : ''); ?>">Wishlist</a></li>
                                                                                        <li><a href="<?php echo e(url('reviews')); ?>" class="<?php echo e(Request::is('reviews') ? 'active' : ''); ?>">Reviews</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li><a href="<?php echo e(url('social-feed')); ?>" class="<?php echo e(Request::is('social-feed') ? 'active' : ''); ?>">Social Feed</a></li>
                                                                        <li><a href="<?php echo e(url('search-list')); ?>" class="<?php echo e(Request::is('search-list') ? 'active' : ''); ?>">Search List</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('layout-horizontal','layout-detached','layout-modern','layout-two-column','layout-hovered','layout-boxed','layout-rtl','layout-dark') ? 'active' : ''); ?>"><i class="ti ti-layout-sidebar-right-collapse fs-16 me-2"></i><span>Layouts</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('layout-horizontal')); ?>" class="<?php echo e(Request::is('layout-horizontal') ? 'active' : ''); ?>">Horizontal</a></li>
                                                                        <li><a href="<?php echo e(url('layout-detached')); ?>" class="<?php echo e(Request::is('layout-detached') ? 'active' : ''); ?>">Detached</a></li>
                                                                        <li><a href="<?php echo e(url('layout-modern')); ?>" class="<?php echo e(Request::is('layout-modern') ? 'active' : ''); ?>">Modern</a></li>
                                                                        <li><a href="<?php echo e(url('layout-two-column')); ?>" class="<?php echo e(Request::is('layout-two-column') ? 'active' : ''); ?>">Two Column</a></li>
                                                                        <li><a href="<?php echo e(url('layout-hovered')); ?>" class="<?php echo e(Request::is('layout-hovered') ? 'active' : ''); ?>">Hovered</a></li>
                                                                        <li><a href="<?php echo e(url('layout-boxed')); ?>" class="<?php echo e(Request::is('layout-boxed') ? 'active' : ''); ?>">Boxed</a></li>
                                                                        <li><a href="<?php echo e(url('layout-rtl')); ?>" class="<?php echo e(Request::is('layout-rtl') ? 'active' : ''); ?>">RTL</a></li>
                                                                        <li><a href="<?php echo e(url('layout-dark')); ?>" class="<?php echo e(Request::is('layout-dark') ? 'active' : ''); ?>">Dark</a></li>
                                                                </ul>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Inventory</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('product-list','product-details') ? 'active' : ''); ?>"><a href="<?php echo e(url('product-list')); ?>"><i data-feather="box"></i><span>Products</span></a></li>
                                                        <li class="<?php echo e(Request::is('add-product') ? 'active' : ''); ?>"><a href="<?php echo e(url('add-product')); ?>" ><i class="ti ti-table-plus fs-16 me-2"></i><span>Create Product</span></a></li>
                                                        <li class="<?php echo e(Request::is('expired-products') ? 'active' : ''); ?>"><a href="<?php echo e(url('expired-products')); ?>" ><i class="ti ti-progress-alert fs-16 me-2"></i><span>Expired Products</span></a></li>
                                                        <li class="<?php echo e(Request::is('low-stocks') ? 'active' : ''); ?>"><a href="<?php echo e(url('low-stocks')); ?>" ><i class="ti ti-trending-up-2 fs-16 me-2"></i><span>Low Stocks</span></a></li>
                                                        <li class="<?php echo e(Request::is('category-list') ? 'active' : ''); ?>"><a href="<?php echo e(url('category-list')); ?>" ><i class="ti ti-list-details fs-16 me-2"></i><span>Category</span></a></li>
                                                        <li class="<?php echo e(Request::is('sub-categories') ? 'active' : ''); ?>"><a href="<?php echo e(url('sub-categories')); ?>" ><i class="ti ti-carousel-vertical fs-16 me-2"></i><span>Sub Category</span></a></li>
                                                        <li class="<?php echo e(Request::is('brand-list') ? 'active' : ''); ?>"><a href="<?php echo e(url('brand-list')); ?>"><i class="ti ti-triangles fs-16 me-2"></i><span>Brands</span></a></li>
                                                        <li class="<?php echo e(Request::is('units') ? 'active' : ''); ?>"><a href="<?php echo e(url('units')); ?>"><i class="ti ti-brand-unity fs-16 me-2"></i><span>Units</span></a></li>
                                                        <li class="<?php echo e(Request::is('varriant-attributes') ? 'active' : ''); ?>"><a href="<?php echo e(url('varriant-attributes')); ?>"><i class="ti ti-checklist fs-16 me-2"></i><span>Variant Attributes</span></a></li>
                                                        <li class="<?php echo e(Request::is('warranty') ? 'active' : ''); ?>"><a href="<?php echo e(url('warranty')); ?>"><i class="ti ti-certificate fs-16 me-2"></i><span>Warranties</span></a></li>
                                                        <li class="<?php echo e(Request::is('barcode') ? 'active' : ''); ?>"><a href="<?php echo e(url('barcode')); ?>"><i class="ti ti-barcode fs-16 me-2"></i><span>Print Barcode</span></a></li>
                                                        <li class="<?php echo e(Request::is('qrcode') ? 'active' : ''); ?>"><a href="<?php echo e(url('qrcode')); ?>"><i class="ti ti-qrcode fs-16 me-2"></i><span>Print QR Code</span></a></li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Stock</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('manage-stocks') ? 'active' : ''); ?>"><a href="<?php echo e(url('manage-stocks')); ?>" ><i class="ti ti-stack-3 fs-16 me-2"></i><span>Manage Stock</span></a></li>
                                                        <li class="<?php echo e(Request::is('stock-adjustment') ? 'active' : ''); ?>"><a href="<?php echo e(url('stock-adjustment')); ?>"><i class="ti ti-stairs-up fs-16 me-2"></i><span>Stock Adjustment</span></a></li>
                                                        <li  class="<?php echo e(Request::is('stock-transfer') ? 'active' : ''); ?>"><a href="<?php echo e(url('stock-transfer')); ?>"><i class="ti ti-stack-pop fs-16 me-2"></i><span>Stock Transfer</span></a></li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Sales</h6>
                                                <ul>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('online-orders','pos-orders') ? 'active' : ''); ?>"><i class="ti ti-layout-grid fs-16 me-2"></i><span>Sales</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('online-orders')); ?>" class="<?php echo e(Request::is('online-orders') ? 'active' : ''); ?>">Online Orders</a></li>
                                                                        <li><a href="<?php echo e(url('pos-orders')); ?>" class="<?php echo e(Request::is('pos-orders') ? 'active' : ''); ?>">POS Orders</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('invoice') ? 'active' : ''); ?>"><a href="<?php echo e(url('invoice')); ?>"><i class="ti ti-file-invoice fs-16 me-2"></i><span>Invoices</span></a></li>
                                                        <li class="<?php echo e(Request::is('sales-returns') ? 'active' : ''); ?>"><a href="<?php echo e(url('sales-returns')); ?>"><i class="ti ti-receipt-refund fs-16 me-2"></i><span>Sales Return</span></a></li>
                                                        <li class="<?php echo e(Request::is('quotation-list') ? 'active' : ''); ?>"><a href="<?php echo e(url('quotation-list')); ?>"><i class="ti ti-files fs-16 me-2"></i><span>Quotation</span></a></li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"><i class="ti ti-device-laptop fs-16 me-2"></i><span>POS</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('pos')); ?>" class="<?php echo e(Request::is('pos') ? 'active' : ''); ?>">POS 1</a></li>
                                                                        <li><a href="<?php echo e(url('pos-2')); ?>" class="<?php echo e(Request::is('pos-2') ? 'active' : ''); ?>">POS 2</a></li>
                                                                        <li><a href="<?php echo e(url('pos-3')); ?>" class="<?php echo e(Request::is('pos-3') ? 'active' : ''); ?>">POS 3</a></li>
                                                                        <li><a href="<?php echo e(url('pos-4')); ?>" class="<?php echo e(Request::is('pos-4') ? 'active' : ''); ?>">POS 4</a></li>
                                                                        <li><a href="<?php echo e(url('pos-5')); ?>" class="<?php echo e(Request::is('pos-5') ? 'active' : ''); ?>">POS 5</a></li>
                                                                </ul>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Promo</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('coupons') ? 'active' : ''); ?>"><a href="<?php echo e(url('coupons')); ?>" ><i class="ti ti-ticket fs-16 me-2"></i><span>Coupons</span></a></li>
                                                        <li class="<?php echo e(Request::is('gift-cards') ? 'active' : ''); ?>"><a href="<?php echo e(url('gift-cards')); ?>" ><i class="ti ti-cards fs-16 me-2"></i><span>Gift Cards</span></a></li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('discount-plan','discount') ? 'active' : ''); ?>"><i class="ti ti-file-percent fs-16 me-2"></i><span>Discount</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('discount-plan')); ?>" class="<?php echo e(Request::is('discount-plan') ? 'active' : ''); ?>">Discount Plan</a></li>
                                                                        <li><a href="<?php echo e(url('discount')); ?>" class="<?php echo e(Request::is('discount') ? 'active' : ''); ?>">Discount</a></li>
                                                                </ul>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Purchases</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('purchase-list') ? 'active' : ''); ?>"><a href="<?php echo e(url('purchase-list')); ?>" ><i class="ti ti-shopping-bag fs-16 me-2"></i><span>Purchases</span></a></li>
                                                        <li class="<?php echo e(Request::is('purchase-order-report') ? 'active' : ''); ?>"><a href="<?php echo e(url('purchase-order-report')); ?>" ><i class="ti ti-file-unknown fs-16 me-2"></i><span>Purchase Order</span></a></li>
                                                        <li class="<?php echo e(Request::is('purchase-returns') ? 'active' : ''); ?>"><a href="<?php echo e(url('purchase-returns')); ?>" ><i class="ti ti-file-upload fs-16 me-2"></i><span>Purchase Return</span></a></li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Finance & Accounts</h6>
                                                <ul>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('expense-list','expense-category') ? 'active' : ''); ?>"><i class="ti ti-file-stack fs-16 me-2"></i><span>Expenses</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('expense-list')); ?>" class="<?php echo e(Request::is('expense-list') ? 'active' : ''); ?>">Expenses</a></li>
                                                                        <li><a href="<?php echo e(url('expense-category')); ?>" class="<?php echo e(Request::is('expense-category') ? 'active' : ''); ?>">Expense Category</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('income','income-category') ? 'active' : ''); ?>"><i class="ti ti-file-pencil fs-16 me-2"></i><span>Income</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('income')); ?>" class="<?php echo e(Request::is('income') ? 'active' : ''); ?>">Income</a></li>
                                                                        <li><a href="<?php echo e(url('income-category')); ?>" class="<?php echo e(Request::is('income-category') ? 'active' : ''); ?>">Income Category</a></li>
                                                                </ul>
                                                        </li>
                                                        <li  class="<?php echo e(Request::is('account-list') ? 'active' : ''); ?>"><a href="<?php echo e(url('account-list')); ?>"><i class="ti ti-building-bank fs-16 me-2"></i><span>Bank Accounts</span></a></li>
                                                        <li class="<?php echo e(Request::is('money-transfer') ? 'active' : ''); ?>"><a href="<?php echo e(url('money-transfer')); ?>"><i class="ti ti-moneybag fs-16 me-2"></i><span>Money Transfer</span></a></li>
                                                        <li class="<?php echo e(Request::is('balance-sheet','balance-sheet-v2') ? 'active' : ''); ?>"><a href="<?php echo e(url('balance-sheet')); ?>" ><i class="ti ti-report-money fs-16 me-2"></i><span>Balance Sheet</span></a></li>
                                                        <li class="<?php echo e(Request::is('trial-balance') ? 'active' : ''); ?>"><a href="<?php echo e(url('trial-balance')); ?>" ><i class="ti ti-alert-circle fs-16 me-2"></i><span>Trial Balance</span></a></li>
                                                        <li class="<?php echo e(Request::is('cash-flow') ? 'active' : ''); ?>"><a href="<?php echo e(url('cash-flow')); ?>"><i class="ti ti-zoom-money fs-16 me-2"></i><span>Cash Flow</span></a></li>
                                                        <li class="<?php echo e(Request::is('account-statement') ? 'active' : ''); ?>"><a href="<?php echo e(url('account-statement')); ?>" ><i class="ti ti-file-infinity fs-16 me-2"></i><span>Account Statement</span></a></li>

                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Peoples</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('customers') ? 'active' : ''); ?>"><a href="<?php echo e(url('customers')); ?>"><i class="ti ti-users-group fs-16 me-2"></i><span>Customers</span></a></li>
                                                        <li class="<?php echo e(Request::is('billers') ? 'active' : ''); ?>"><a href="<?php echo e(url('billers')); ?>"><i class="ti ti-user-up fs-16 me-2"></i><span>Billers</span></a></li>
                                                        <li class="<?php echo e(Request::is('suppliers') ? 'active' : ''); ?>"><a href="<?php echo e(url('suppliers')); ?>"><i class="ti ti-user-dollar fs-16 me-2"></i><span>Suppliers</span></a></li>
                                                        <li class="<?php echo e(Request::is('store-list') ? 'active' : ''); ?>"><a href="<?php echo e(url('store-list')); ?>"><i class="ti ti-home-bolt fs-16 me-2"></i><span>Stores</span></a></li>
                                                        <li class="<?php echo e(Request::is('warehouse') ? 'active' : ''); ?>"><a href="<?php echo e(url('warehouse')); ?>"><i class="ti ti-archive fs-16 me-2"></i><span>Warehouses</span></a>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">HRM</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('employees-grid','employees-list','add-employee','edit-employee','employee-details') ? 'active' : ''); ?>"><a href="<?php echo e(url('employees-grid')); ?>"><i class="ti ti-user fs-16 me-2"></i><span>Employees</span></a></li>
                                                        <li class="<?php echo e(Request::is('department-grid') ? 'active' : ''); ?>"><a href="<?php echo e(url('department-grid')); ?>"><i class="ti ti-compass fs-16 me-2"></i><span>Departments</span></a></li>
                                                        <li class="<?php echo e(Request::is('designation') ? 'active' : ''); ?>"><a href="<?php echo e(url('designation')); ?>"><i class="ti ti-git-merge fs-16 me-2"></i><span>Designation</span></a></li>
                                                        <li class="<?php echo e(Request::is('shift') ? 'active' : ''); ?>"><a href="<?php echo e(url('shift')); ?>"><i class="ti ti-arrows-shuffle fs-16 me-2"></i><span>Shifts</span></a></li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('attendance-employee','attendance-admin') ? 'active' : ''); ?>"><i class="ti ti-user-cog fs-16 me-2"></i><span>Attendence</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('attendance-employee')); ?>" class="<?php echo e(Request::is('attendance-employee') ? 'active' : ''); ?>">Employee</a></li>
                                                                        <li><a href="<?php echo e(url('attendance-admin')); ?>" class="<?php echo e(Request::is('attendance-admin') ? 'active' : ''); ?>">Admin</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('leaves-admin','leaves-employee','leave-types') ? 'active' : ''); ?>"><i class="ti ti-calendar fs-16 me-2"></i><span>Leaves</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('leaves-admin')); ?>" class="<?php echo e(Request::is('leaves-admin') ? 'active' : ''); ?>">Admin Leaves</a></li>
                                                                        <li><a href="<?php echo e(url('leaves-employee')); ?>" class="<?php echo e(Request::is('leaves-employee') ? 'active' : ''); ?>">Employee Leaves</a></li>
                                                                        <li><a href="<?php echo e(url('leave-types')); ?>" class="<?php echo e(Request::is('leave-types') ? 'active' : ''); ?>">Leave Types</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('holidays') ? 'active' : ''); ?>"><a href="<?php echo e(url('holidays')); ?>" ><i class="ti ti-calendar-share fs-16 me-2"></i><span>Holidays</span></a>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="<?php echo e(url('employee-salary')); ?>" class="<?php echo e(Request::is('employee-salary','payslip') ? 'active' : ''); ?>"><i class="ti ti-file-dollar fs-16 me-2"></i><span>Payroll</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('employee-salary')); ?>" class="<?php echo e(Request::is('employee-salary') ? 'active' : ''); ?>">Employee Salary</a></li>
                                                                        <li><a href="<?php echo e(url('payslip')); ?>" class="<?php echo e(Request::is('payslip') ? 'active' : ''); ?>">Payslip</a></li>
                                                                </ul>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Reports</h6>
                                                <ul>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('sales-report','best-seller') ? 'active' : ''); ?>"><i class="ti ti-chart-bar fs-16 me-2"></i><span>Sales Report</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('sales-report')); ?>" class="<?php echo e(Request::is('sales-report') ? 'active' : ''); ?>">Sales Report</a></li>
                                                                        <li><a href="<?php echo e(url('best-seller')); ?>" class="<?php echo e(Request::is('best-seller') ? 'active' : ''); ?>">Best Seller</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('purchase-report') ? 'active' : ''); ?>"><a href="<?php echo e(url('purchase-report')); ?>" ><i class="ti ti-chart-pie-2 fs-16 me-2"></i><span>Purchase report</span></a></li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('inventory-report','stock-history','sold-stock') ? 'active' : ''); ?>"><i class="ti ti-triangle-inverted fs-16 me-2"></i><span>Inventory Report</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('inventory-report')); ?>" class="<?php echo e(Request::is('inventory-report') ? 'active' : ''); ?>">Inventory Report</a></li>
                                                                        <li><a href="<?php echo e(url('stock-history')); ?>" class="<?php echo e(Request::is('stock-history') ? 'active' : ''); ?>">Stock History</a></li>
                                                                        <li><a href="<?php echo e(url('sold-stock')); ?>" class="<?php echo e(Request::is('sold-stock') ? 'active' : ''); ?>">Sold Stock</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('invoice-report') ? 'active' : ''); ?>"><a href="<?php echo e(url('invoice-report')); ?>"><i class="ti ti-businessplan fs-16 me-2"></i><span>Invoice Report</span></a></li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('supplier-report','supplier-due-report') ? 'active' : ''); ?>"><i class="ti ti-user-star fs-16 me-2"></i><span>Supplier Report</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('supplier-report')); ?>" class="<?php echo e(Request::is('supplier-report') ? 'active' : ''); ?>">Supplier Report</a></li>
                                                                        <li><a href="<?php echo e(url('supplier-due-report')); ?>" class="<?php echo e(Request::is('supplier-due-report') ? 'active' : ''); ?>">Supplier Due Report</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"class="<?php echo e(Request::is('customer-report','customer-due-report') ? 'active' : ''); ?>" ><i class="ti ti-report fs-16 me-2"></i><span>Customer Report</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('customer-report')); ?>" class="<?php echo e(Request::is('customer-report') ? 'active' : ''); ?>">Customer Report</a></li>
                                                                        <li><a href="<?php echo e(url('customer-due-report')); ?>" class="<?php echo e(Request::is('customer-due-report') ? 'active' : ''); ?>">Customer Due Report</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"  class="<?php echo e(Request::is('product-report','product-expiry-report','product-quantity-alert') ? 'active' : ''); ?>"><i class="ti ti-report-analytics fs-16 me-2"></i><span>Product Report</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('product-report')); ?>" class="<?php echo e(Request::is('product-report') ? 'active' : ''); ?>">Product Report</a></li>
                                                                        <li><a href="<?php echo e(url('product-expiry-report')); ?>" class="<?php echo e(Request::is('product-expiry-report') ? 'active' : ''); ?>">Product Expiry Report</a></li>
                                                                        <li><a href="<?php echo e(url('product-quantity-alert')); ?>" class="<?php echo e(Request::is('product-quantity-alert') ? 'active' : ''); ?>">Product Quantity Alert</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('expense-report') ? 'active' : ''); ?>"><a href="<?php echo e(url('expense-report')); ?>"><i class="ti ti-file-vector fs-16 me-2"></i><span>Expense Report</span></a></li>
                                                        <li class="<?php echo e(Request::is('income-report') ? 'active' : ''); ?>"><a href="<?php echo e(url('income-report')); ?>"><i class="ti ti-chart-ppf fs-16 me-2"></i><span>Income Report</span></a></li>
                                                        <li class="<?php echo e(Request::is('tax-reports') ? 'active' : ''); ?>"><a href="<?php echo e(url('tax-reports')); ?>" ><i class="ti ti-chart-dots-2 fs-16 me-2"></i><span>Tax Report</span></a></li>
                                                        <li class="<?php echo e(Request::is('profit-and-loss') ? 'active' : ''); ?>"><a href="<?php echo e(url('profit-and-loss')); ?>"><i class="ti ti-chart-donut fs-16 me-2"></i><span>Profit & Loss</span></a></li>
                                                        <li class="<?php echo e(Request::is('annual-report') ? 'active' : ''); ?>"><a href="<?php echo e(url('annual-report')); ?>"><i class="ti ti-report-search fs-16 me-2"></i><span>Annual Report</span></a></li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Content (CMS)</h6>
                                                <ul>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"><i class="ti ti-page-break fs-16 me-2"></i><span>Pages</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('pages')); ?>" class="<?php echo e(Request::is('pages') ? 'active' : ''); ?>">Pages</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('all-blog','blog-details','blog-tag','blog-categories','blog-comments') ? 'active' : ''); ?>"><i class="ti ti-wallpaper fs-16 me-2"></i><span>Blog</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('all-blog')); ?>" class="<?php echo e(Request::is('all-blog','blog-details') ? 'active' : ''); ?>">All Blog</a></li>
                                                                        <li><a href="<?php echo e(url('blog-tag')); ?>" class="<?php echo e(Request::is('blog-tag') ? 'active' : ''); ?>">Blog Tags</a></li>
                                                                        <li><a href="<?php echo e(url('blog-categories')); ?>" class="<?php echo e(Request::is('blog-categories') ? 'active' : ''); ?>">Categories</a></li>
                                                                        <li><a href="<?php echo e(url('blog-comments')); ?>" class="<?php echo e(Request::is('blog-comments') ? 'active' : ''); ?>">Blog Comments</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('countries','states','cities') ? 'active' : ''); ?>"><i class="ti ti-map-pin fs-16 me-2"></i><span>Location</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('countries')); ?>" class="<?php echo e(Request::is('countries') ? 'active' : ''); ?>">Countries</a></li>
                                                                        <li><a href="<?php echo e(url('states')); ?>" class="<?php echo e(Request::is('states') ? 'active' : ''); ?>">States</a></li>
                                                                        <li><a href="<?php echo e(url('cities')); ?>" class="<?php echo e(Request::is('cities') ? 'active' : ''); ?>">Cities</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('testimonials') ? 'active' : ''); ?>"><a href="<?php echo e(url('testimonials')); ?>" ><i class="ti ti-star fs-16 me-2"></i><span>Testimonials</span></a></li>
                                                        <li class="<?php echo e(Request::is('faq') ? 'active' : ''); ?>"><a href="<?php echo e(url('faq')); ?>" ><i class="ti ti-help-circle fs-16 me-2"></i><span>FAQ</span></a></li>

                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">User Management</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('users') ? 'active' : ''); ?>"><a href="<?php echo e(url('users')); ?>" ><i class="ti ti-shield-up fs-16 me-2"></i><span>Users</span></a></li>
                                                        <li class="<?php echo e(Request::is('roles-permissions') ? 'active' : ''); ?>"><a href="<?php echo e(url('roles-permissions')); ?>"><i class="ti ti-jump-rope fs-16 me-2"></i><span>Roles & Permissions</span></a></li>
                                                        <li class="<?php echo e(Request::is('delete-account') ? 'active' : ''); ?>"><a href="<?php echo e(url('delete-account')); ?>"><i class="ti ti-trash-x fs-16 me-2"></i><span>Delete Account Request</span></a></li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Pages</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('profile') ? 'active' : ''); ?>"><a href="<?php echo e(url('profile')); ?>"><i class="ti ti-user-circle fs-16 me-2"></i><span>Profile</span></a></li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"><i class="ti ti-shield fs-16 me-2"></i><span>Authentication</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Login<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('signin')); ?>" class="<?php echo e(Request::is('signin') ? 'active' : ''); ?>">Cover</a></li>
                                                                                        <li><a href="<?php echo e(url('signin-2')); ?>" class="<?php echo e(Request::is('signin-2') ? 'active' : ''); ?>">Illustration</a></li>
                                                                                        <li><a href="<?php echo e(url('signin-3')); ?>" class="<?php echo e(Request::is('signin-3') ? 'active' : ''); ?>">Basic</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Register<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('register')); ?>" class="<?php echo e(Request::is('register') ? 'active' : ''); ?>">Cover</a></li>
                                                                                        <li><a href="<?php echo e(url('register-2')); ?>" class="<?php echo e(Request::is('register-2') ? 'active' : ''); ?>">Illustration</a></li>
                                                                                        <li><a href="<?php echo e(url('register-3')); ?>" class="<?php echo e(Request::is('register-3') ? 'active' : ''); ?>">Basic</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Forgot Password<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('forgot-password')); ?>" class="<?php echo e(Request::is('forgot-password') ? 'active' : ''); ?>">Cover</a></li>
                                                                                        <li><a href="<?php echo e(url('forgot-password-2')); ?>" class="<?php echo e(Request::is('forgot-password-2') ? 'active' : ''); ?>">Illustration</a></li>
                                                                                        <li><a href="<?php echo e(url('forgot-password-3')); ?>" class="<?php echo e(Request::is('forgot-password-3') ? 'active' : ''); ?>">Basic</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Reset Password<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('reset-password')); ?>" class="<?php echo e(Request::is('reset-password') ? 'active' : ''); ?>">Cover</a></li>
                                                                                        <li><a href="<?php echo e(url('reset-password-2')); ?>" class="<?php echo e(Request::is('reset-password-2') ? 'active' : ''); ?>">Illustration</a></li>
                                                                                        <li><a href="<?php echo e(url('reset-password-3')); ?>" class="<?php echo e(Request::is('reset-password-3') ? 'active' : ''); ?>">Basic</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Email Verification<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('email-verification')); ?>" class="<?php echo e(Request::is('email-verification') ? 'active' : ''); ?>">Cover</a></li>
                                                                                        <li><a href="<?php echo e(url('email-verification-2')); ?>" class="<?php echo e(Request::is('email-verification-2') ? 'active' : ''); ?>">Illustration</a></li>
                                                                                        <li><a href="<?php echo e(url('email-verification-3')); ?>" class="<?php echo e(Request::is('email-verification-3') ? 'active' : ''); ?>">Basic</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">2 Step Verification<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('two-step-verification')); ?>" class="<?php echo e(Request::is('two-step-verification') ? 'active' : ''); ?>">Cover</a></li>
                                                                                        <li><a href="<?php echo e(url('two-step-verification-2')); ?>" class="<?php echo e(Request::is('two-step-verification-2') ? 'active' : ''); ?>">Illustration</a></li>
                                                                                        <li><a href="<?php echo e(url('two-step-verification-3')); ?>" class="<?php echo e(Request::is('two-step-verification-3') ? 'active' : ''); ?>">Basic</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="<?php echo e(Request::is('lock-screen') ? 'active' : ''); ?>"><a href="<?php echo e(url('lock-screen')); ?>">Lock Screen</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"><i class="ti ti-file-x fs-16 me-2"></i><span>Error Pages</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('error-404')); ?>" class="<?php echo e(Request::is('error-404') ? 'active' : ''); ?>">404 Error </a></li>
                                                                        <li><a href="<?php echo e(url('error-500')); ?>" class="<?php echo e(Request::is('error-500') ? 'active' : ''); ?>">500 Error </a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('blank-page') ? 'active' : ''); ?>">
                                                                <a href="<?php echo e(url('blank-page')); ?>" ><i class="ti ti-file fs-16 me-2"></i><span>Blank Page</span> </a>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('pricing') ? 'active' : ''); ?>">
                                                                <a href="<?php echo e(url('pricing')); ?>" ><i class="ti ti-currency-dollar fs-16 me-2"></i><span>Pricing</span> </a>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('coming-soon') ? 'active' : ''); ?>">
                                                                <a href="<?php echo e(url('coming-soon')); ?>" ><i class="ti ti-send fs-16 me-2"></i><span>Coming Soon</span> </a>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('under-maintenance') ? 'active' : ''); ?>">
                                                                <a href="<?php echo e(url('under-maintenance')); ?>"><i class="ti ti-alert-triangle fs-16 me-2"></i><span>Under Maintenance</span> </a>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Settings</h6>
                                                <ul>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('general-settings','security-settings','notification','activities','connected-apps') ? 'active' : ''); ?>"><i class="ti ti-settings fs-16 me-2"></i><span>General Settings</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('general-settings')); ?>" class="<?php echo e(Request::is('general-settings') ? 'active' : ''); ?>">Profile</a></li>
                                                                        <li><a href="<?php echo e(url('security-settings')); ?>" class="<?php echo e(Request::is('security-settings') ? 'active' : ''); ?>">Security</a></li>
                                                                        <li><a href="<?php echo e(url('notification')); ?>" class="<?php echo e(Request::is('notification','activities') ? 'active' : ''); ?>">Notifications</a></li>
                                                                        <li><a href="<?php echo e(url('connected-apps')); ?>" class="<?php echo e(Request::is('connected-apps') ? 'active' : ''); ?>">Connected Apps</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('system-settings','company-settings','localization-settings','prefixes','preference','appearance','social-authentication','language-settings') ? 'active' : ''); ?>"><i class="ti ti-world fs-16 me-2"></i><span>Website Settings</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('system-settings')); ?>" class="<?php echo e(Request::is('system-settings') ? 'active' : ''); ?>">System Settings</a></li>
                                                                        <li><a href="<?php echo e(url('company-settings')); ?>" class="<?php echo e(Request::is('company-settings') ? 'active' : ''); ?>">Company Settings </a></li>
                                                                        <li><a href="<?php echo e(url('localization-settings')); ?>" class="<?php echo e(Request::is('localization-settings') ? 'active' : ''); ?>">Localization</a></li>
                                                                        <li><a href="<?php echo e(url('prefixes')); ?>" class="<?php echo e(Request::is('prefixes') ? 'active' : ''); ?>">Prefixes</a></li>
                                                                        <li><a href="<?php echo e(url('preference')); ?>" class="<?php echo e(Request::is('preference') ? 'active' : ''); ?>">Preference</a></li>
                                                                        <li><a href="<?php echo e(url('appearance')); ?>" class="<?php echo e(Request::is('appearance') ? 'active' : ''); ?>">Appearance</a></li>
                                                                        <li><a href="<?php echo e(url('social-authentication')); ?>" class="<?php echo e(Request::is('social-authentication') ? 'active' : ''); ?>">Social Authentication</a></li>
                                                                        <li><a href="<?php echo e(url('language-settings')); ?>" class="<?php echo e(Request::is('language-settings') ? 'active' : ''); ?>">Language</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('invoice-settings','invoice-template','printer-settings','pos-settings','custom-fields') ? 'active' : ''); ?>"><i class="ti ti-device-mobile fs-16 me-2"></i>
                                                                        <span>App Settings</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Invoice<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('invoice-settings')); ?>" class="<?php echo e(Request::is('invoice-settings') ? 'active' : ''); ?>">Invoice Settings</a></li>
                                                                                        <li><a href="<?php echo e(url('invoice-template')); ?>" class="<?php echo e(Request::is('invoice-template') ? 'active' : ''); ?>">Invoice Template</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li><a href="<?php echo e(url('printer-settings')); ?>" class="<?php echo e(Request::is('printer-settings') ? 'active' : ''); ?>">Printer</a></li>
                                                                        <li><a href="<?php echo e(url('pos-settings')); ?>" class="<?php echo e(Request::is('pos-settings') ? 'active' : ''); ?>">POS</a></li>
                                                                        <li><a href="<?php echo e(url('custom-fields')); ?>" class="<?php echo e(Request::is('custom-fields') ? 'active' : ''); ?>">Custom Fields</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('email-settings','email-template','sms-settings','sms-template','otp-settings','gdpr-settings','payment-gateway-settings','bank-settings-grid','tax-rates','currency-settings') ? 'active' : ''); ?>"><i class="ti ti-device-desktop fs-16 me-2"></i>
                                                                        <span>System Settings</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);" class="<?php echo e(Request::is('email-settings','email-template') ? 'active' : ''); ?>">Email<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('email-settings')); ?>" class="<?php echo e(Request::is('email-settings') ? 'active' : ''); ?>">Email Settings</a></li>
                                                                                        <li><a href="<?php echo e(url('email-template')); ?>" class="<?php echo e(Request::is('email-template') ? 'active' : ''); ?>">Email Template</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);" class="<?php echo e(Request::is('sms-settings','sms-template') ? 'active' : ''); ?>">SMS<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('sms-settings')); ?>" class="<?php echo e(Request::is('sms-settings') ? 'active' : ''); ?>">SMS Settings</a></li>
                                                                                        <li><a href="<?php echo e(url('sms-template')); ?>" class="<?php echo e(Request::is('sms-template') ? 'active' : ''); ?>">SMS Template</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="<?php echo e(Request::is('otp-settings') ? 'active' : ''); ?>"><a href="<?php echo e(url('otp-settings')); ?>">OTP</a></li>
                                                                        <li class="<?php echo e(Request::is('gdpr-settings') ? 'active' : ''); ?>"><a href="<?php echo e(url('gdpr-settings')); ?>">GDPR Cookies</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('payment-gateway-settings','bank-settings-grid','tax-rates','currency-settings') ? 'active' : ''); ?>"><i class="ti ti-settings-dollar fs-16 me-2"></i>
                                                                        <span>Financial Settings</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('payment-gateway-settings')); ?>" class="<?php echo e(Request::is('payment-gateway-settings') ? 'active' : ''); ?>">Payment Gateway</a></li>
                                                                        <li><a href="<?php echo e(url('bank-settings-grid')); ?>" class="<?php echo e(Request::is('bank-settings-grid') ? 'active' : ''); ?>">Bank Accounts</a></li>
                                                                        <li><a href="<?php echo e(url('tax-rates')); ?>" class="<?php echo e(Request::is('tax-rates') ? 'active' : ''); ?>">Tax Rates</a></li>
                                                                        <li><a href="<?php echo e(url('currency-settings')); ?>" class="<?php echo e(Request::is('currency-settings') ? 'active' : ''); ?>">Currencies</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('storage-settings','ban-ip-address') ? 'active' : ''); ?>"><i class="ti ti-settings-2 fs-16 me-2"></i>
                                                                        <span>Other Settings</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('storage-settings')); ?>" class="<?php echo e(Request::is('storage-settings') ? 'active' : ''); ?>">Storage</a></li>
                                                                        <li><a href="<?php echo e(url('ban-ip-address')); ?>" class="<?php echo e(Request::is('ban-ip-address') ? 'active' : ''); ?>">Ban IP Address</a></li>
                                                                </ul>
                                                        </li>
                                                        <li>
                                                                <a href="<?php echo e(url('signin')); ?>" class="<?php echo e(Request::is('signin') ? 'active' : ''); ?>"><i class="ti ti-logout fs-16 me-2"></i><span>Logout</span> </a>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">UI Interface</h6>
                                                <ul>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('ui-alerts', 'ui-sortable', 'ui-swiperjs', 'ui-accordion', 'ui-avatar', 'ui-badges', 'ui-borders', 'ui-buttons', 'ui-buttons-group', 'ui-breadcrumb', 'ui-cards', 'ui-carousel', 'ui-colors', 'ui-dropdowns', 'ui-grid', 'ui-images', 'ui-lightbox', 'ui-modals', 'ui-media', 'ui-offcanvas', 'ui-pagination', 'ui-popovers', 'ui-progress', 'ui-placeholders', 'ui-rangeslider', 'ui-spinner', 'ui-sweetalerts', 'ui-nav-tabs', 'ui-toasts', 'ui-tooltips', 'ui-typography', 'ui-video') ? 'active subdrop' : ''); ?>">
                                                                        <i class="ti ti-vector-bezier fs-16 me-2"></i><span>Base UI</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                        <li><a href="<?php echo e(url('ui-alerts')); ?>"
                                                class="<?php echo e(Request::is('ui-alerts') ? 'active' : ''); ?>">Alerts</a></li>
                                        <li><a href="<?php echo e(url('ui-accordion')); ?>"
                                                class="<?php echo e(Request::is('ui-accordion') ? 'active' : ''); ?>">Accordion</a></li>
                                        <li><a href="<?php echo e(url('ui-avatar')); ?>"
                                                class="<?php echo e(Request::is('ui-avatar') ? 'active' : ''); ?>">Avatar</a></li>
                                        <li><a href="<?php echo e(url('ui-badges')); ?>"
                                                class="<?php echo e(Request::is('ui-badges') ? 'active' : ''); ?>">Badges</a></li>
                                        <li><a href="<?php echo e(url('ui-borders')); ?>"
                                                class="<?php echo e(Request::is('ui-borders') ? 'active' : ''); ?>">Border</a></li>
                                        <li><a href="<?php echo e(url('ui-buttons')); ?>"
                                                class="<?php echo e(Request::is('ui-buttons') ? 'active' : ''); ?>">Buttons</a></li>
                                        <li><a href="<?php echo e(url('ui-buttons-group')); ?>"
                                                class="<?php echo e(Request::is('ui-buttons-group') ? 'active' : ''); ?>">Button
                                                Group</a></li>
                                        <li><a href="<?php echo e(url('ui-breadcrumb')); ?>"
                                                class="<?php echo e(Request::is('ui-breadcrumb') ? 'active' : ''); ?>">Breadcrumb</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-cards')); ?>"
                                                class="<?php echo e(Request::is('ui-cards') ? 'active' : ''); ?>">Card</a></li>
                                        <li><a href="<?php echo e(url('ui-carousel')); ?>"
                                                class="<?php echo e(Request::is('ui-carousel') ? 'active' : ''); ?>">Carousel</a></li>
                                        <li><a href="<?php echo e(url('ui-colors')); ?>"
                                                class="<?php echo e(Request::is('ui-colors') ? 'active' : ''); ?>">Colors</a></li>
                                        <li><a href="<?php echo e(url('ui-dropdowns')); ?>"
                                                class="<?php echo e(Request::is('ui-dropdowns') ? 'active' : ''); ?>">Dropdowns</a></li>
                                        <li><a href="<?php echo e(url('ui-grid')); ?>"
                                                class="<?php echo e(Request::is('ui-grid') ? 'active' : ''); ?>">Grid</a></li>
                                        <li><a href="<?php echo e(url('ui-images')); ?>"
                                                class="<?php echo e(Request::is('ui-images') ? 'active' : ''); ?>">Images</a></li>
                                        <li><a href="<?php echo e(url('ui-lightbox')); ?>"
                                                class="<?php echo e(Request::is('ui-lightbox') ? 'active' : ''); ?>">Lightbox</a></li>
                                        <li><a href="<?php echo e(url('ui-media')); ?>"
                                                class="<?php echo e(Request::is('ui-media') ? 'active' : ''); ?>">Media</a></li>
                                        <li><a href="<?php echo e(url('ui-modals')); ?>"
                                                class="<?php echo e(Request::is('ui-modals') ? 'active' : ''); ?>">Modals</a></li>
                                        <li><a href="<?php echo e(url('ui-offcanvas')); ?>"
                                                class="<?php echo e(Request::is('ui-offcanvas') ? 'active' : ''); ?>">Offcanvas</a></li>
                                        <li><a href="<?php echo e(url('ui-pagination')); ?>"
                                                class="<?php echo e(Request::is('ui-pagination') ? 'active' : ''); ?>">Pagination</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-popovers')); ?>"
                                                class="<?php echo e(Request::is('ui-popovers') ? 'active' : ''); ?>">Popovers</a></li>
                                        <li><a href="<?php echo e(url('ui-progress')); ?>"
                                                class="<?php echo e(Request::is('ui-progress') ? 'active' : ''); ?>">Progress</a></li>
                                        <li><a href="<?php echo e(url('ui-placeholders')); ?>"
                                                class="<?php echo e(Request::is('ui-placeholders') ? 'active' : ''); ?>">Placeholders</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-rangeslider')); ?>"
                                                class="<?php echo e(Request::is('ui-rangeslider') ? 'active' : ''); ?>">Range Slider</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-spinner')); ?>"
                                                class="<?php echo e(Request::is('ui-spinner') ? 'active' : ''); ?>">Spinner</a></li>
                                        <li><a href="<?php echo e(url('ui-sweetalerts')); ?>"
                                                class="<?php echo e(Request::is('ui-sweetalerts') ? 'active' : ''); ?>">Sweet Alerts</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-nav-tabs')); ?>"
                                                class="<?php echo e(Request::is('ui-nav-tabs') ? 'active' : ''); ?>">Tabs</a></li>
                                        <li><a href="<?php echo e(url('ui-toasts')); ?>"
                                                class="<?php echo e(Request::is('ui-toasts') ? 'active' : ''); ?>">Toasts</a></li>
                                        <li><a href="<?php echo e(url('ui-tooltips')); ?>"
                                                class="<?php echo e(Request::is('ui-tooltips') ? 'active' : ''); ?>">Tooltips</a></li>
                                        <li><a href="<?php echo e(url('ui-typography')); ?>"
                                                class="<?php echo e(Request::is('ui-typography') ? 'active' : ''); ?>">Typography</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-video')); ?>"
                                                class="<?php echo e(Request::is('ui-video') ? 'active' : ''); ?>">Video</a></li>
                                        <li><a href="<?php echo e(url('ui-sortable')); ?>" class="<?php echo e(Request::is('ui-sortable') ? 'active' : ''); ?>">Sortable</a></li>
                                        <li><a href="<?php echo e(url('ui-swiperjs')); ?>" class="<?php echo e(Request::is('ui-swiperjs') ? 'active' : ''); ?>">Swiperjs</a></li>
                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"   class="<?php echo e(Request::is('ui-ribbon', 'ui-clipboard', 'ui-drag-drop', 'ui-rating', 'ui-text-editor', 'ui-counter', 'ui-scrollbar', 'ui-stickynote', 'ui-timeline') ? 'active subdrop' : ''); ?>">
                                                                        <i data-feather="layers"></i><span>Advanced UI</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                        <li><a href="<?php echo e(url('ui-ribbon')); ?>"
                                                class="<?php echo e(Request::is('ui-ribbon') ? 'active' : ''); ?>">Ribbon</a></li>
                                        <li><a href="<?php echo e(url('ui-clipboard')); ?>"
                                                class="<?php echo e(Request::is('ui-clipboard') ? 'active' : ''); ?>">Clipboard</a></li>
                                        <li><a href="<?php echo e(url('ui-drag-drop')); ?>"
                                                class="<?php echo e(Request::is('ui-drag-drop') ? 'active' : ''); ?>">Drag & Drop</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-rating')); ?>"
                                                class="<?php echo e(Request::is('ui-rating') ? 'active' : ''); ?>">Rating</a></li>
                                        <li><a href="<?php echo e(url('ui-text-editor')); ?>"
                                                class="<?php echo e(Request::is('ui-text-editor') ? 'active' : ''); ?>">Text Editor</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-counter')); ?>"
                                                class="<?php echo e(Request::is('ui-counter') ? 'active' : ''); ?>">Counter</a></li>
                                        <li><a href="<?php echo e(url('ui-scrollbar')); ?>"
                                                class="<?php echo e(Request::is('ui-scrollbar') ? 'active' : ''); ?>">Scrollbar</a></li>
                                        <li><a href="<?php echo e(url('ui-stickynote')); ?>"
                                                class="<?php echo e(Request::is('ui-stickynote') ? 'active' : ''); ?>">Sticky Note</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-timeline')); ?>"
                                                class="<?php echo e(Request::is('ui-timeline') ? 'active' : ''); ?>">Timeline</a></li>
                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"   class="<?php echo e(Request::is('chart-apex', 'chart-c3', 'chart-js', 'chart-morris', 'chart-flot', 'chart-peity') ? 'active subdrop' : ''); ?>"><i class="ti ti-chart-infographic fs-16 me-2"></i>
                                                                        <span>Charts</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                        <li><a href="<?php echo e(url('chart-apex')); ?>"
                                                class="<?php echo e(Request::is('chart-apex') ? 'active' : ''); ?>">Apex Charts</a></li>
                                        <li><a href="<?php echo e(url('chart-c3')); ?>"
                                                class="<?php echo e(Request::is('chart-c3') ? 'active' : ''); ?>">Chart C3</a></li>
                                        <li><a href="<?php echo e(url('chart-js')); ?>"
                                                class="<?php echo e(Request::is('chart-js') ? 'active' : ''); ?>">Chart Js</a></li>
                                        <li><a href="<?php echo e(url('chart-morris')); ?>"
                                                class="<?php echo e(Request::is('chart-morris') ? 'active' : ''); ?>">Morris Charts</a>
                                        </li>
                                        <li><a href="<?php echo e(url('chart-flot')); ?>"
                                                class="<?php echo e(Request::is('chart-flot') ? 'active' : ''); ?>">Flot Charts</a></li>
                                        <li><a href="<?php echo e(url('chart-peity')); ?>"
                                                class="<?php echo e(Request::is('chart-peity') ? 'active' : ''); ?>">Peity Charts</a>
                                        </li>
                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"  class="<?php echo e(Request::is('icon-fontawesome', 'icon-tabler', 'icon-bootstrap', 'icon-remix',  'icon-feather', 'icon-ionic', 'icon-material', 'icon-pe7', 'icon-simpleline', 'icon-themify', 'icon-weather', 'icon-typicon', 'icon-flag') ? 'active subdrop' : ''); ?>"><i class="ti ti-icons fs-16 me-2"></i>
                                                                        <span>Icons</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                        <li><a href="<?php echo e(url('icon-fontawesome')); ?>"
                                                class="<?php echo e(Request::is('icon-fontawesome') ? 'active' : ''); ?>">Fontawesome
                                                Icons</a></li>
                                        <li><a href="<?php echo e(url('icon-feather')); ?>"
                                                class="<?php echo e(Request::is('icon-feather') ? 'active' : ''); ?>">Feather Icons</a>
                                        </li>
                                        <li><a href="<?php echo e(url('icon-ionic')); ?>"
                                                class="<?php echo e(Request::is('icon-ionic') ? 'active' : ''); ?>">Ionic Icons</a></li>
                                        <li><a href="<?php echo e(url('icon-material')); ?>"
                                                class="<?php echo e(Request::is('icon-material') ? 'active' : ''); ?>">Material Icons</a>
                                        </li>
                                        <li><a href="<?php echo e(url('icon-pe7')); ?>"
                                                class="<?php echo e(Request::is('icon-pe7') ? 'active' : ''); ?>">Pe7 Icons</a></li>
                                        <li><a href="<?php echo e(url('icon-simpleline')); ?>"
                                                class="<?php echo e(Request::is('icon-simpleline') ? 'active' : ''); ?>">Simpleline
                                                Icons</a></li>
                                        <li><a href="<?php echo e(url('icon-themify')); ?>"
                                                class="<?php echo e(Request::is('icon-themify') ? 'active' : ''); ?>">Themify Icons</a>
                                        </li>
                                        <li><a href="<?php echo e(url('icon-weather')); ?>"
                                                class="<?php echo e(Request::is('icon-weather') ? 'active' : ''); ?>">Weather Icons</a>
                                        </li>
                                        <li><a href="<?php echo e(url('icon-typicon')); ?>"
                                                class="<?php echo e(Request::is('icon-typicon') ? 'active' : ''); ?>">Typicon Icons</a>
                                        </li>
                                        <li><a href="<?php echo e(url('icon-flag')); ?>"
                                                class="<?php echo e(Request::is('icon-flag') ? 'active' : ''); ?>">Flag Icons</a></li>
                                        <li><a href="<?php echo e(url('icon-tabler')); ?>" class="<?php echo e(Request::is('icon-tabler') ? 'active' : ''); ?>">Tabler Icons</a></li>
                                        <li><a href="<?php echo e(url('icon-bootstrap')); ?>" class="<?php echo e(Request::is('icon-bootstrap') ? 'active' : ''); ?>">Bootstrap Icons</a></li>
                                        <li><a href="<?php echo e(url('icon-remix')); ?>" class="<?php echo e(Request::is('icon-remix') ? 'active' : ''); ?>">Remix Icons</a></li>
                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('form-wizard', 'form-select2', 'form-validation', 'form-floating-labels', 'form-vertical', 'form-horizontal', 'form-basic-inputs', 'form-checkbox-radios', 'form-input-groups', 'form-grid-gutters', 'form-select', 'form-mask', 'form-fileupload') ? 'active subdrop' : ''); ?>">
                                                                        <i class="ti ti-input-search fs-16 me-2"></i><span>Forms</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                                                        <li class="submenu submenu-two">
                                                                                <a href="javascript:void(0);">Form Elements<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                <li><a href="<?php echo e(url('form-basic-inputs')); ?>"
                                                        class="<?php echo e(Request::is('form-basic-inputs') ? 'active' : ''); ?>">Basic
                                                        Inputs</a></li>
                                                <li><a href="<?php echo e(url('form-checkbox-radios')); ?>"
                                                        class="<?php echo e(Request::is('form-checkbox-radios') ? 'active' : ''); ?>">Checkbox
                                                        & Radios</a></li>
                                                <li><a href="<?php echo e(url('form-input-groups')); ?>"
                                                        class="<?php echo e(Request::is('form-input-groups') ? 'active' : ''); ?>">Input
                                                        Groups</a></li>
                                                <li><a href="<?php echo e(url('form-grid-gutters')); ?>"
                                                        class="<?php echo e(Request::is('form-grid-gutters') ? 'active' : ''); ?>">Grid &
                                                        Gutters</a></li>
                                                <li><a href="<?php echo e(url('form-select')); ?>"
                                                        class="<?php echo e(Request::is('form-select') ? 'active' : ''); ?>">Form
                                                        Select</a></li>
                                                <li><a href="<?php echo e(url('form-mask')); ?>"
                                                        class="<?php echo e(Request::is('form-mask') ? 'active' : ''); ?>">Input
                                                        Masks</a></li>
                                                <li><a href="<?php echo e(url('form-fileupload')); ?>"
                                                        class="<?php echo e(Request::is('form-fileupload') ? 'active' : ''); ?>">File
                                                        Uploads</a></li>
                                        </ul>

                                                                        </li>
                                                                        <li class="submenu submenu-two">
                                                                                <a href="javascript:void(0);">Layouts<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                <li><a href="<?php echo e(url('form-horizontal')); ?>"
                                                        class="<?php echo e(Request::is('form-horizontal') ? 'active' : ''); ?>">Horizontal
                                                        Form</a></li>
                                                <li><a href="<?php echo e(url('form-vertical')); ?>"
                                                        class="<?php echo e(Request::is('form-vertical') ? 'active' : ''); ?>">Vertical
                                                        Form</a></li>
                                                <li><a href="<?php echo e(url('form-floating-labels')); ?>"
                                                        class="<?php echo e(Request::is('form-floating-labels') ? 'active' : ''); ?>">Floating
                                                        Labels</a></li>
                                        </ul>
                                                                        </li>
                                                                        <li><a href="<?php echo e(url('form-validation')); ?>"
                                                class="<?php echo e(Request::is('form-validation') ? 'active' : ''); ?>">Form
                                                Validation</a></li>
                                        <li><a href="<?php echo e(url('form-select2')); ?>"
                                                class="<?php echo e(Request::is('form-select2') ? 'active' : ''); ?>">Select2</a></li>
                                        <li><a href="<?php echo e(url('form-wizard')); ?>"
                                                class="<?php echo e(Request::is('form-wizard') ? 'active' : ''); ?>">Form Wizard</a></li>
                                        <li><a href="<?php echo e(url('form-pickers')); ?>" class="<?php echo e(Request::is('form-pickers') ? 'active' : ''); ?>">Form Picker</a></li>
                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('tables-basic','data-tables') ? 'active' : ''); ?>"><i class="ti ti-table fs-16 me-2"></i><span>Tables</span><span class="menu-arrow"></span></a>
                                                        <ul>
                                        <li><a href="<?php echo e(url('tables-basic')); ?>"
                                                class="<?php echo e(Request::is('tables-basic') ? 'active' : ''); ?>">Basic Tables </a>
                                        </li>
                                        <li><a href="<?php echo e(url('data-tables')); ?>"
                                                class="<?php echo e(Request::is('data-tables') ? 'active' : ''); ?>">Data Table </a></li>
                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"><i class="ti ti-map-pin-pin fs-16 me-2"></i><span>Maps</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                        <li><a href="<?php echo e(url('maps-vector')); ?>">Vector</a></li>
                                        <li><a href="<?php echo e(url('maps-leaflet')); ?>">Leaflet</a></li>
                                </ul>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Help</h6>
                                                <ul>
                                                        <li><a href="javascript:void(0);"><i class="ti ti-file-text fs-16 me-2"></i><span>Documentation</span></a></li>
                                                        <li><a href="javascript:void(0);"><i class="ti ti-exchange fs-16 me-2"></i><span>Changelog </span><span class="badge bg-primary badge-xs text-white fs-10 ms-2">v2.1.9</span></a></li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"><i class="ti ti-menu-2 fs-16 me-2"></i><span>Multi Level</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="javascript:void(0);">Level 1.1</a></li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Level 1.2<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="javascript:void(0);">Level 2.1</a></li>
                                                                                        <li class="submenu submenu-two submenu-three"><a href="javascript:void(0);">Level 2.2<span class="menu-arrow inside-submenu inside-submenu-two"></span></a>
                                                                                                <ul>
                                                                                                        <li><a href="javascript:void(0);">Level 3.1</a></li>
                                                                                                        <li><a href="javascript:void(0);">Level 3.2</a></li>
                                                                                                </ul>
                                                                                        </li>
                                                                                </ul>
                                                                        </li>
                                                                </ul>
                                                        </li>
                                                </ul>
                                        </li>
                                </ul>
                        </div>
                </div>
        </div>
        <!-- /Sidebar -->
<?php endif; ?>

<?php if(Route::is(['pos','pos-2','pos-3','pos-4','pos-5'])): ?>
        <!-- Sidebar -->
        <div class="sidebar d-none" id="sidebar">
                <!-- Logo -->
                <div class="sidebar-logo active">
                        <a href="<?php echo e(url('index')); ?>" class="logo logo-normal">
                                <img src="<?php echo e(URL::asset('build/img/logo.svg')); ?>" alt="Img">
                        </a>
                        <a href="<?php echo e(url('index')); ?>" class="logo logo-white">
                                <img src="<?php echo e(URL::asset('build/img/logo-white.svg')); ?>" alt="Img">
                        </a>
                        <a href="<?php echo e(url('index')); ?>" class="logo-small">
                                <img src="<?php echo e(URL::asset('build/img/logo-small.png')); ?>" alt="Img">
                        </a>
                        <a id="toggle_btn" href="javascript:void(0);">
                                <i data-feather="chevrons-left" class="feather-16"></i>
                        </a>
                </div>
                <!-- /Logo -->
                <div class="modern-profile p-3 pb-0">
                        <div class="text-center rounded bg-light p-3 mb-4 user-profile">
                                <div class="avatar avatar-lg online mb-3">
                                        <img src="<?php echo e(URL::asset('build/img/customer/customer15.jpg')); ?>" alt="Img" class="img-fluid rounded-circle">
                                </div>
                                <h6 class="fs-12 fw-normal mb-1"><?php echo e(Auth::check() ? Auth::user()->name : 'Guest'); ?></h6>
                                <p class="fs-10 mb-0">Mess Member</p>
                        </div>
                        <div class="sidebar-nav mb-3">
                                <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent" role="tablist">
                                        <li class="nav-item"><a class="nav-link active border-0" href="#">Menu</a></li>
                                        <li class="nav-item"><a class="nav-link border-0" href="<?php echo e(url('chat')); ?>">Chats</a></li>
                                        <li class="nav-item"><a class="nav-link border-0" href="<?php echo e(url('email')); ?>">Inbox</a></li>
                                </ul>
                        </div>
                </div>
                <div class="sidebar-header p-3 pb-0 pt-2">
                        <div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
                                <div class="avatar avatar-md onlin">
                                        <img src="<?php echo e(URL::asset('build/img/customer/customer15.jpg')); ?>" alt="Img" class="img-fluid rounded-circle">
                                </div>
                                <div class="text-start sidebar-profile-info ms-2">
                                        <h6 class="fs-12 fw-normal mb-1"><?php echo e(Auth::check() ? Auth::user()->name : 'Guest'); ?></h6>
                                        <p class="fs-10"><?php echo e(Auth::check() ? (Auth::user()->getActiveMess() ? Auth::user()->getActiveMess()->name : 'No Mess') : 'Guest'); ?></p>
                                </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between menu-item mb-3">
                                <div>
                                        <a href="<?php echo e(url('index')); ?>" class="btn btn-sm btn-icon bg-light">
                                                <i class="ti ti-layout-grid-remove"></i>
                                        </a>
                                </div>
                                <div>
                                        <a href="<?php echo e(url('chat')); ?>" class="btn btn-sm btn-icon bg-light">
                                                <i class="ti ti-brand-hipchat"></i>
                                        </a>
                                </div>
                                <div>
                                        <a href="<?php echo e(url('email')); ?>" class="btn btn-sm btn-icon bg-light position-relative">
                                                <i class="ti ti-message"></i>
                                        </a>
                                </div>
                                <div class="notification-item">
                                        <a href="<?php echo e(url('activities')); ?>" class="btn btn-sm btn-icon bg-light position-relative">
                                                <i class="ti ti-bell"></i>
                                                <span class="notification-status-dot"></span>
                                        </a>
                                </div>
                                <div class="me-0">
                                        <a href="<?php echo e(url('general-settings')); ?>" class="btn btn-sm btn-icon bg-light">
                                                <i class="ti ti-settings"></i>
                                        </a>
                                </div>
                        </div>
                </div>
                <div class="sidebar-inner slimscroll">
                        <div id="sidebar-menu" class="sidebar-menu">
                                <ul>
                                        <?php echo $__env->make('layout.partials.mess-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Main</h6>
                                                <ul>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"  class="<?php echo e(Request::is('index', '/', 'sales-dashboard','admin-dashboard') ? 'active subdrop' : ''); ?>"><i class="ti ti-layout-grid fs-16 me-2"></i><span>Dashboard</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('index')); ?>"
                                                                                class="<?php echo e(Request::is('index', '/') ? 'active' : ''); ?>">Admin Dashboard</a></li>
                                                                        <li><a href="<?php echo e(url('admin-dashboard')); ?>"  class="<?php echo e(Request::is('admin-dashboard') ? 'active' : ''); ?>">Admin Dashboard 2</a></li>
                                                                        <li><a href="<?php echo e(url('sales-dashboard')); ?>"
                                                                                class="<?php echo e(Request::is('sales-dashboard') ? 'active' : ''); ?>">Sales Dashboard</a>
                                                                        </li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('dashboard', 'companies', 'subscription','packages','domain','purchase-transaction') ? 'active subdrop' : ''); ?>"><i class="ti ti-user-edit fs-16 me-2"></i><span>Super Admin</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('dashboard')); ?>" class="<?php echo e(Request::is('dashboard') ? 'active' : ''); ?>">Dashboard</a></li>
                                                                        <li><a href="<?php echo e(url('companies')); ?>" class="<?php echo e(Request::is('companies') ? 'active' : ''); ?>">Companies</a></li>
                                                                        <li><a href="<?php echo e(url('subscription')); ?>" class="<?php echo e(Request::is('subscription') ? 'active' : ''); ?>">Subscriptions</a></li>
                                                                        <li><a href="<?php echo e(url('packages')); ?>" class="<?php echo e(Request::is('packages') ? 'active' : ''); ?>">Packages</a></li>
                                                                        <li><a href="<?php echo e(url('domain')); ?>" class="<?php echo e(Request::is('domain') ? 'active' : ''); ?>">Domain</a></li>
                                                                        <li><a href="<?php echo e(url('purchase-transaction')); ?>" class="<?php echo e(Request::is('purchase-transaction') ? 'active' : ''); ?>">Purchase Transaction</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('chat', 'video-call', 'audio-call','call-history','calendar','contacts','email','todo','notes','file-manager'
                                                                ,'projects','products','orders','cart','checkout','wishlist','reviews','social-feed','search-list') ? 'active subdrop' : ''); ?>"><i class="ti ti-brand-apple-arcade fs-16 me-2"></i><span>Application</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li class="<?php echo e(Request::is('chat') ? 'active' : ''); ?>"><a href="<?php echo e(url('chat')); ?>">Chat</a></li>
                                                                        <li class="submenu submenu-two" ><a href="javascript:void(0);" class="<?php echo e(Request::is('video-call', 'audio-call', 'call-history') ? 'active subdrop' : ''); ?>">Call<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('video-call')); ?>" class="<?php echo e(Request::is('video-call') ? 'active' : ''); ?>">Video Call</a></li>
                                                                                        <li><a href="<?php echo e(url('audio-call')); ?>" class="<?php echo e(Request::is('audio-call') ? 'active' : ''); ?>">Audio Call</a></li>
                                                                                        <li><a href="<?php echo e(url('call-history')); ?>" class="<?php echo e(Request::is('call-history') ? 'active' : ''); ?>">Call History</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li><a href="<?php echo e(url('calendar')); ?>" class="<?php echo e(Request::is('calendar') ? 'active' : ''); ?>">Calendar</a></li>
                                                                        <li><a href="<?php echo e(url('contacts')); ?>" class="<?php echo e(Request::is('contacts') ? 'active' : ''); ?>">Contacts</a></li>
                                                                        <li><a href="<?php echo e(url('email')); ?>" class="<?php echo e(Request::is('email') ? 'active' : ''); ?>">Email</a></li>
                                                                        <li><a href="<?php echo e(url('todo')); ?>" class="<?php echo e(Request::is('todo') ? 'active' : ''); ?>">To Do</a></li>
                                                                        <li><a href="<?php echo e(url('notes')); ?>" class="<?php echo e(Request::is('notes') ? 'active' : ''); ?>">Notes</a></li>
                                                                        <li><a href="<?php echo e(url('file-manager')); ?>" class="<?php echo e(Request::is('file-manager') ? 'active' : ''); ?>">File Manager</a></li>
                                                                        <li><a href="<?php echo e(url('projects')); ?>" class="<?php echo e(Request::is('projects') ? 'active' : ''); ?>">Projects</a></li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);" class="<?php echo e(Request::is('products','orders','customers','cart','checkout','wishlist','reviews') ? 'active' : ''); ?>">Ecommerce<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('products')); ?>" class="<?php echo e(Request::is('products') ? 'active' : ''); ?>">Products</a></li>
                                                                                        <li><a href="<?php echo e(url('orders')); ?>" class="<?php echo e(Request::is('orders') ? 'active' : ''); ?>">Orders</a></li>
                                                                                        <li><a href="<?php echo e(url('customers')); ?>" class="<?php echo e(Request::is('customers') ? 'active' : ''); ?>">Customers</a></li>
                                                                                        <li><a href="<?php echo e(url('cart')); ?>" class="<?php echo e(Request::is('cart') ? 'active' : ''); ?>">Cart</a></li>
                                                                                        <li><a href="<?php echo e(url('checkout')); ?>" class="<?php echo e(Request::is('checkout') ? 'active' : ''); ?>">Checkout</a></li>
                                                                                        <li><a href="<?php echo e(url('wishlist')); ?>" class="<?php echo e(Request::is('wishlist') ? 'active' : ''); ?>">Wishlist</a></li>
                                                                                        <li><a href="<?php echo e(url('reviews')); ?>" class="<?php echo e(Request::is('reviews') ? 'active' : ''); ?>">Reviews</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li><a href="<?php echo e(url('social-feed')); ?>" class="<?php echo e(Request::is('social-feed') ? 'active' : ''); ?>">Social Feed</a></li>
                                                                        <li><a href="<?php echo e(url('search-list')); ?>" class="<?php echo e(Request::is('search-list') ? 'active' : ''); ?>">Search List</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('layout-horizontal','layout-detached','layout-modern','layout-two-column','layout-hovered','layout-boxed','layout-rtl','layout-dark') ? 'active' : ''); ?>"><i class="ti ti-layout-sidebar-right-collapse fs-16 me-2"></i><span>Layouts</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('layout-horizontal')); ?>" class="<?php echo e(Request::is('layout-horizontal') ? 'active' : ''); ?>">Horizontal</a></li>
                                                                        <li><a href="<?php echo e(url('layout-detached')); ?>" class="<?php echo e(Request::is('layout-detached') ? 'active' : ''); ?>">Detached</a></li>
                                                                        <li><a href="<?php echo e(url('layout-two-column')); ?>" class="<?php echo e(Request::is('layout-two-column') ? 'active' : ''); ?>">Two Column</a></li>
                                                                        <li><a href="<?php echo e(url('layout-hovered')); ?>" class="<?php echo e(Request::is('layout-hovered') ? 'active' : ''); ?>">Hovered</a></li>
                                                                        <li><a href="<?php echo e(url('layout-boxed')); ?>" class="<?php echo e(Request::is('layout-boxed') ? 'active' : ''); ?>">Boxed</a></li>
                                                                        <li><a href="<?php echo e(url('layout-rtl')); ?>" class="<?php echo e(Request::is('layout-rtl') ? 'active' : ''); ?>">RTL</a></li>
                                                                        <li><a href="<?php echo e(url('layout-dark')); ?>" class="<?php echo e(Request::is('layout-dark') ? 'active' : ''); ?>">Dark</a></li>
                                                                </ul>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Inventory</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('product-list','product-details') ? 'active' : ''); ?>"><a href="<?php echo e(url('product-list')); ?>"><i data-feather="box"></i><span>Products</span></a></li>
                                                        <li class="<?php echo e(Request::is('add-product') ? 'active' : ''); ?>"><a href="<?php echo e(url('add-product')); ?>" ><i class="ti ti-table-plus fs-16 me-2"></i><span>Create Product</span></a></li>
                                                        <li class="<?php echo e(Request::is('expired-products') ? 'active' : ''); ?>"><a href="<?php echo e(url('expired-products')); ?>" ><i class="ti ti-progress-alert fs-16 me-2"></i><span>Expired Products</span></a></li>
                                                        <li class="<?php echo e(Request::is('low-stocks') ? 'active' : ''); ?>"><a href="<?php echo e(url('low-stocks')); ?>" ><i class="ti ti-trending-up-2 fs-16 me-2"></i><span>Low Stocks</span></a></li>
                                                        <li class="<?php echo e(Request::is('category-list') ? 'active' : ''); ?>"><a href="<?php echo e(url('category-list')); ?>" ><i class="ti ti-list-details fs-16 me-2"></i><span>Category</span></a></li>
                                                        <li class="<?php echo e(Request::is('sub-categories') ? 'active' : ''); ?>"><a href="<?php echo e(url('sub-categories')); ?>" ><i class="ti ti-carousel-vertical fs-16 me-2"></i><span>Sub Category</span></a></li>
                                                        <li class="<?php echo e(Request::is('brand-list') ? 'active' : ''); ?>"><a href="<?php echo e(url('brand-list')); ?>"><i class="ti ti-triangles fs-16 me-2"></i><span>Brands</span></a></li>
                                                        <li class="<?php echo e(Request::is('units') ? 'active' : ''); ?>"><a href="<?php echo e(url('units')); ?>"><i class="ti ti-brand-unity fs-16 me-2"></i><span>Units</span></a></li>
                                                        <li class="<?php echo e(Request::is('varriant-attributes') ? 'active' : ''); ?>"><a href="<?php echo e(url('varriant-attributes')); ?>"><i class="ti ti-checklist fs-16 me-2"></i><span>Variant Attributes</span></a></li>
                                                        <li class="<?php echo e(Request::is('warranty') ? 'active' : ''); ?>"><a href="<?php echo e(url('warranty')); ?>"><i class="ti ti-certificate fs-16 me-2"></i><span>Warranties</span></a></li>
                                                        <li class="<?php echo e(Request::is('barcode') ? 'active' : ''); ?>"><a href="<?php echo e(url('barcode')); ?>"><i class="ti ti-barcode fs-16 me-2"></i><span>Print Barcode</span></a></li>
                                                        <li class="<?php echo e(Request::is('qrcode') ? 'active' : ''); ?>"><a href="<?php echo e(url('qrcode')); ?>"><i class="ti ti-qrcode fs-16 me-2"></i><span>Print QR Code</span></a></li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Stock</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('manage-stocks') ? 'active' : ''); ?>"><a href="<?php echo e(url('manage-stocks')); ?>" ><i class="ti ti-stack-3 fs-16 me-2"></i><span>Manage Stock</span></a></li>
                                                        <li class="<?php echo e(Request::is('stock-adjustment') ? 'active' : ''); ?>"><a href="<?php echo e(url('stock-adjustment')); ?>"><i class="ti ti-stairs-up fs-16 me-2"></i><span>Stock Adjustment</span></a></li>
                                                        <li  class="<?php echo e(Request::is('stock-transfer') ? 'active' : ''); ?>"><a href="<?php echo e(url('stock-transfer')); ?>"><i class="ti ti-stack-pop fs-16 me-2"></i><span>Stock Transfer</span></a></li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Sales</h6>
                                                <ul>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('online-orders','pos-orders') ? 'active' : ''); ?>"><i class="ti ti-layout-grid fs-16 me-2"></i><span>Sales</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('online-orders')); ?>" class="<?php echo e(Request::is('online-orders') ? 'active' : ''); ?>">Online Orders</a></li>
                                                                        <li><a href="<?php echo e(url('pos-orders')); ?>" class="<?php echo e(Request::is('pos-orders') ? 'active' : ''); ?>">POS Orders</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('invoice') ? 'active' : ''); ?>"><a href="<?php echo e(url('invoice')); ?>"><i class="ti ti-file-invoice fs-16 me-2"></i><span>Invoices</span></a></li>
                                                        <li class="<?php echo e(Request::is('sales-returns') ? 'active' : ''); ?>"><a href="<?php echo e(url('sales-returns')); ?>"><i class="ti ti-receipt-refund fs-16 me-2"></i><span>Sales Return</span></a></li>
                                                        <li class="<?php echo e(Request::is('quotation-list') ? 'active' : ''); ?>"><a href="<?php echo e(url('quotation-list')); ?>"><i class="ti ti-files fs-16 me-2"></i><span>Quotation</span></a></li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"><i class="ti ti-device-laptop fs-16 me-2"></i><span>POS</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('pos')); ?>" class="<?php echo e(Request::is('pos') ? 'active' : ''); ?>">POS 1</a></li>
                                                                        <li><a href="<?php echo e(url('pos-2')); ?>" class="<?php echo e(Request::is('pos-2') ? 'active' : ''); ?>">POS 2</a></li>
                                                                        <li><a href="<?php echo e(url('pos-3')); ?>" class="<?php echo e(Request::is('pos-3') ? 'active' : ''); ?>">POS 3</a></li>
                                                                        <li><a href="<?php echo e(url('pos-4')); ?>" class="<?php echo e(Request::is('pos-4') ? 'active' : ''); ?>">POS 4</a></li>
                                                                        <li><a href="<?php echo e(url('pos-5')); ?>" class="<?php echo e(Request::is('pos-5') ? 'active' : ''); ?>">POS 5</a></li>
                                                                </ul>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Promo</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('coupons') ? 'active' : ''); ?>"><a href="<?php echo e(url('coupons')); ?>" ><i class="ti ti-ticket fs-16 me-2"></i><span>Coupons</span></a></li>
                                                        <li class="<?php echo e(Request::is('gift-cards') ? 'active' : ''); ?>"><a href="<?php echo e(url('gift-cards')); ?>" ><i class="ti ti-cards fs-16 me-2"></i><span>Gift Cards</span></a></li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('discount-plan','discount') ? 'active' : ''); ?>"><i class="ti ti-file-percent fs-16 me-2"></i><span>Discount</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('discount-plan')); ?>" class="<?php echo e(Request::is('discount-plan') ? 'active' : ''); ?>">Discount Plan</a></li>
                                                                        <li><a href="<?php echo e(url('discount')); ?>" class="<?php echo e(Request::is('discount') ? 'active' : ''); ?>">Discount</a></li>
                                                                </ul>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Purchases</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('purchase-list') ? 'active' : ''); ?>"><a href="<?php echo e(url('purchase-list')); ?>" ><i class="ti ti-shopping-bag fs-16 me-2"></i><span>Purchases</span></a></li>
                                                        <li class="<?php echo e(Request::is('purchase-order-report') ? 'active' : ''); ?>"><a href="<?php echo e(url('purchase-order-report')); ?>" ><i class="ti ti-file-unknown fs-16 me-2"></i><span>Purchase Order</span></a></li>
                                                        <li class="<?php echo e(Request::is('purchase-returns') ? 'active' : ''); ?>"><a href="<?php echo e(url('purchase-returns')); ?>" ><i class="ti ti-file-upload fs-16 me-2"></i><span>Purchase Return</span></a></li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Finance & Accounts</h6>
                                                <ul>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('expense-list','expense-category') ? 'active' : ''); ?>"><i class="ti ti-file-stack fs-16 me-2"></i><span>Expenses</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('expense-list')); ?>" class="<?php echo e(Request::is('expense-list') ? 'active' : ''); ?>">Expenses</a></li>
                                                                        <li><a href="<?php echo e(url('expense-category')); ?>" class="<?php echo e(Request::is('expense-category') ? 'active' : ''); ?>">Expense Category</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('income','income-category') ? 'active' : ''); ?>"><i class="ti ti-file-pencil fs-16 me-2"></i><span>Income</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('income')); ?>" class="<?php echo e(Request::is('income') ? 'active' : ''); ?>">Income</a></li>
                                                                        <li><a href="<?php echo e(url('income-category')); ?>" class="<?php echo e(Request::is('income-category') ? 'active' : ''); ?>">Income Category</a></li>
                                                                </ul>
                                                        </li>
                                                        <li  class="<?php echo e(Request::is('account-list') ? 'active' : ''); ?>"><a href="<?php echo e(url('account-list')); ?>"><i class="ti ti-building-bank fs-16 me-2"></i><span>Bank Accounts</span></a></li>
                                                        <li class="<?php echo e(Request::is('money-transfer') ? 'active' : ''); ?>"><a href="<?php echo e(url('money-transfer')); ?>"><i class="ti ti-moneybag fs-16 me-2"></i><span>Money Transfer</span></a></li>
                                                        <li class="<?php echo e(Request::is('balance-sheet') ? 'active' : ''); ?>"><a href="<?php echo e(url('balance-sheet')); ?>" ><i class="ti ti-report-money fs-16 me-2"></i><span>Balance Sheet</span></a></li>
                                                        <li class="<?php echo e(Request::is('trial-balance') ? 'active' : ''); ?>"><a href="<?php echo e(url('trial-balance')); ?>" ><i class="ti ti-alert-circle fs-16 me-2"></i><span>Trial Balance</span></a></li>
                                                        <li class="<?php echo e(Request::is('cash-flow') ? 'active' : ''); ?>"><a href="<?php echo e(url('cash-flow')); ?>"><i class="ti ti-zoom-money fs-16 me-2"></i><span>Cash Flow</span></a></li>
                                                        <li class="<?php echo e(Request::is('account-statement') ? 'active' : ''); ?>"><a href="<?php echo e(url('account-statement')); ?>" ><i class="ti ti-file-infinity fs-16 me-2"></i><span>Account Statement</span></a></li>

                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Peoples</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('customers') ? 'active' : ''); ?>"><a href="<?php echo e(url('customers')); ?>"><i class="ti ti-users-group fs-16 me-2"></i><span>Customers</span></a></li>
                                                        <li class="<?php echo e(Request::is('billers') ? 'active' : ''); ?>"><a href="<?php echo e(url('billers')); ?>"><i class="ti ti-user-up fs-16 me-2"></i><span>Billers</span></a></li>
                                                        <li class="<?php echo e(Request::is('suppliers') ? 'active' : ''); ?>"><a href="<?php echo e(url('suppliers')); ?>"><i class="ti ti-user-dollar fs-16 me-2"></i><span>Suppliers</span></a></li>
                                                        <li class="<?php echo e(Request::is('store-list') ? 'active' : ''); ?>"><a href="<?php echo e(url('store-list')); ?>"><i class="ti ti-home-bolt fs-16 me-2"></i><span>Stores</span></a></li>
                                                        <li class="<?php echo e(Request::is('warehouse') ? 'active' : ''); ?>"><a href="<?php echo e(url('warehouse')); ?>"><i class="ti ti-archive fs-16 me-2"></i><span>Warehouses</span></a>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">HRM</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('employees-grid') ? 'active' : ''); ?>"><a href="<?php echo e(url('employees-grid')); ?>"><i class="ti ti-user fs-16 me-2"></i><span>Employees</span></a></li>
                                                        <li class="<?php echo e(Request::is('department-grid') ? 'active' : ''); ?>"><a href="<?php echo e(url('department-grid')); ?>"><i class="ti ti-compass fs-16 me-2"></i><span>Departments</span></a></li>
                                                        <li class="<?php echo e(Request::is('designation') ? 'active' : ''); ?>"><a href="<?php echo e(url('designation')); ?>"><i class="ti ti-git-merge fs-16 me-2"></i><span>Designation</span></a></li>
                                                        <li class="<?php echo e(Request::is('shift') ? 'active' : ''); ?>"><a href="<?php echo e(url('shift')); ?>"><i class="ti ti-arrows-shuffle fs-16 me-2"></i><span>Shifts</span></a></li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('attendance-employee','attendance-admin') ? 'active' : ''); ?>"><i class="ti ti-user-cog fs-16 me-2"></i><span>Attendence</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('attendance-employee')); ?>" class="<?php echo e(Request::is('attendance-employee') ? 'active' : ''); ?>">Employee</a></li>
                                                                        <li><a href="<?php echo e(url('attendance-admin')); ?>" class="<?php echo e(Request::is('attendance-admin') ? 'active' : ''); ?>">Admin</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('leaves-admin','leaves-employee','leave-types') ? 'active' : ''); ?>"><i class="ti ti-calendar fs-16 me-2"></i><span>Leaves</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('leaves-admin')); ?>" class="<?php echo e(Request::is('leaves-admin') ? 'active' : ''); ?>">Admin Leaves</a></li>
                                                                        <li><a href="<?php echo e(url('leaves-employee')); ?>" class="<?php echo e(Request::is('leaves-employee') ? 'active' : ''); ?>">Employee Leaves</a></li>
                                                                        <li><a href="<?php echo e(url('leave-types')); ?>" class="<?php echo e(Request::is('leave-types') ? 'active' : ''); ?>">Leave Types</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('holidays') ? 'active' : ''); ?>"><a href="<?php echo e(url('holidays')); ?>" ><i class="ti ti-calendar-share fs-16 me-2"></i><span>Holidays</span></a>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="<?php echo e(url('employee-salary')); ?>" class="<?php echo e(Request::is('employee-salary','payslip') ? 'active' : ''); ?>"><i class="ti ti-file-dollar fs-16 me-2"></i><span>Payroll</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('employee-salary')); ?>" class="<?php echo e(Request::is('employee-salary') ? 'active' : ''); ?>">Employee Salary</a></li>
                                                                        <li><a href="<?php echo e(url('payslip')); ?>" class="<?php echo e(Request::is('payslip') ? 'active' : ''); ?>">Payslip</a></li>
                                                                </ul>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Reports</h6>
                                                <ul>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('sales-report','best-seller') ? 'active' : ''); ?>"><i class="ti ti-chart-bar fs-16 me-2"></i><span>Sales Report</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('sales-report')); ?>" class="<?php echo e(Request::is('sales-report') ? 'active' : ''); ?>">Sales Report</a></li>
                                                                        <li><a href="<?php echo e(url('best-seller')); ?>" class="<?php echo e(Request::is('best-seller') ? 'active' : ''); ?>">Best Seller</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('purchase-report') ? 'active' : ''); ?>"><a href="<?php echo e(url('purchase-report')); ?>" ><i class="ti ti-chart-pie-2 fs-16 me-2"></i><span>Purchase report</span></a></li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('inventory-report','stock-history','sold-stock') ? 'active' : ''); ?>"><i class="ti ti-triangle-inverted fs-16 me-2"></i><span>Inventory Report</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('inventory-report')); ?>" class="<?php echo e(Request::is('inventory-report') ? 'active' : ''); ?>">Inventory Report</a></li>
                                                                        <li><a href="<?php echo e(url('stock-history')); ?>" class="<?php echo e(Request::is('stock-history') ? 'active' : ''); ?>">Stock History</a></li>
                                                                        <li><a href="<?php echo e(url('sold-stock')); ?>" class="<?php echo e(Request::is('sold-stock') ? 'active' : ''); ?>">Sold Stock</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('invoice-report') ? 'active' : ''); ?>"><a href="<?php echo e(url('invoice-report')); ?>"><i class="ti ti-businessplan fs-16 me-2"></i><span>Invoice Report</span></a></li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('supplier-report','supplier-due-report') ? 'active' : ''); ?>"><i class="ti ti-user-star fs-16 me-2"></i><span>Supplier Report</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('supplier-report')); ?>" class="<?php echo e(Request::is('supplier-report') ? 'active' : ''); ?>">Supplier Report</a></li>
                                                                        <li><a href="<?php echo e(url('supplier-due-report')); ?>" class="<?php echo e(Request::is('supplier-due-report') ? 'active' : ''); ?>">Supplier Due Report</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"class="<?php echo e(Request::is('customer-report','customer-due-report') ? 'active' : ''); ?>" ><i class="ti ti-report fs-16 me-2"></i><span>Customer Report</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('customer-report')); ?>" class="<?php echo e(Request::is('customer-report') ? 'active' : ''); ?>">Customer Report</a></li>
                                                                        <li><a href="<?php echo e(url('customer-due-report')); ?>" class="<?php echo e(Request::is('customer-due-report') ? 'active' : ''); ?>">Customer Due Report</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"  class="<?php echo e(Request::is('product-report','product-expiry-report','product-quantity-alert') ? 'active' : ''); ?>"><i class="ti ti-report-analytics fs-16 me-2"></i><span>Product Report</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('product-report')); ?>" class="<?php echo e(Request::is('product-report') ? 'active' : ''); ?>">Product Report</a></li>
                                                                        <li><a href="<?php echo e(url('product-expiry-report')); ?>" class="<?php echo e(Request::is('product-expiry-report') ? 'active' : ''); ?>">Product Expiry Report</a></li>
                                                                        <li><a href="<?php echo e(url('product-quantity-alert')); ?>" class="<?php echo e(Request::is('product-quantity-alert') ? 'active' : ''); ?>">Product Quantity Alert</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('expense-report') ? 'active' : ''); ?>"><a href="<?php echo e(url('expense-report')); ?>"><i class="ti ti-file-vector fs-16 me-2"></i><span>Expense Report</span></a></li>
                                                        <li class="<?php echo e(Request::is('income-report') ? 'active' : ''); ?>"><a href="<?php echo e(url('income-report')); ?>"><i class="ti ti-chart-ppf fs-16 me-2"></i><span>Income Report</span></a></li>
                                                        <li class="<?php echo e(Request::is('tax-reports') ? 'active' : ''); ?>"><a href="<?php echo e(url('tax-reports')); ?>" ><i class="ti ti-chart-dots-2 fs-16 me-2"></i><span>Tax Report</span></a></li>
                                                        <li class="<?php echo e(Request::is('profit-and-loss') ? 'active' : ''); ?>"><a href="<?php echo e(url('profit-and-loss')); ?>"><i class="ti ti-chart-donut fs-16 me-2"></i><span>Profit & Loss</span></a></li>
                                                        <li class="<?php echo e(Request::is('annual-report') ? 'active' : ''); ?>"><a href="<?php echo e(url('annual-report')); ?>"><i class="ti ti-report-search fs-16 me-2"></i><span>Annual Report</span></a></li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Content (CMS)</h6>
                                                <ul>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"><i class="ti ti-page-break fs-16 me-2"></i><span>Pages</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('pages')); ?>" class="<?php echo e(Request::is('pages') ? 'active' : ''); ?>">Pages</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('all-blog','blog-tag','blog-categories','blog-comments') ? 'active' : ''); ?>"><i class="ti ti-wallpaper fs-16 me-2"></i><span>Blog</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('all-blog')); ?>" class="<?php echo e(Request::is('all-blog') ? 'active' : ''); ?>">All Blog</a></li>
                                                                        <li><a href="<?php echo e(url('blog-tag')); ?>" class="<?php echo e(Request::is('blog-tag') ? 'active' : ''); ?>">Blog Tags</a></li>
                                                                        <li><a href="<?php echo e(url('blog-categories')); ?>" class="<?php echo e(Request::is('blog-categories') ? 'active' : ''); ?>">Categories</a></li>
                                                                        <li><a href="<?php echo e(url('blog-comments')); ?>" class="<?php echo e(Request::is('blog-comments') ? 'active' : ''); ?>">Blog Comments</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('countries','states','cities') ? 'active' : ''); ?>"><i class="ti ti-map-pin fs-16 me-2"></i><span>Location</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('countries')); ?>" class="<?php echo e(Request::is('countries') ? 'active' : ''); ?>">Countries</a></li>
                                                                        <li><a href="<?php echo e(url('states')); ?>" class="<?php echo e(Request::is('states') ? 'active' : ''); ?>">States</a></li>
                                                                        <li><a href="<?php echo e(url('cities')); ?>" class="<?php echo e(Request::is('cities') ? 'active' : ''); ?>">Cities</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('testimonials') ? 'active' : ''); ?>"><a href="<?php echo e(url('testimonials')); ?>" ><i class="ti ti-star fs-16 me-2"></i><span>Testimonials</span></a></li>
                                                        <li class="<?php echo e(Request::is('faq') ? 'active' : ''); ?>"><a href="<?php echo e(url('faq')); ?>" ><i class="ti ti-help-circle fs-16 me-2"></i><span>FAQ</span></a></li>

                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">User Management</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('users') ? 'active' : ''); ?>"><a href="<?php echo e(url('users')); ?>" ><i class="ti ti-shield-up fs-16 me-2"></i><span>Users</span></a></li>
                                                        <li class="<?php echo e(Request::is('roles-permissions') ? 'active' : ''); ?>"><a href="<?php echo e(url('roles-permissions')); ?>"><i class="ti ti-jump-rope fs-16 me-2"></i><span>Roles & Permissions</span></a></li>
                                                        <li class="<?php echo e(Request::is('delete-account') ? 'active' : ''); ?>"><a href="<?php echo e(url('delete-account')); ?>"><i class="ti ti-trash-x fs-16 me-2"></i><span>Delete Account Request</span></a></li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Pages</h6>
                                                <ul>
                                                        <li class="<?php echo e(Request::is('profile') ? 'active' : ''); ?>"><a href="<?php echo e(url('profile')); ?>"><i class="ti ti-user-circle fs-16 me-2"></i><span>Profile</span></a></li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"><i class="ti ti-shield fs-16 me-2"></i><span>Authentication</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Login<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('signin')); ?>" class="<?php echo e(Request::is('signin') ? 'active' : ''); ?>">Cover</a></li>
                                                                                        <li><a href="<?php echo e(url('signin-2')); ?>" class="<?php echo e(Request::is('signin-2') ? 'active' : ''); ?>">Illustration</a></li>
                                                                                        <li><a href="<?php echo e(url('signin-3')); ?>" class="<?php echo e(Request::is('signin-3') ? 'active' : ''); ?>">Basic</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Register<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('register')); ?>" class="<?php echo e(Request::is('register') ? 'active' : ''); ?>">Cover</a></li>
                                                                                        <li><a href="<?php echo e(url('register-2')); ?>" class="<?php echo e(Request::is('register-2') ? 'active' : ''); ?>">Illustration</a></li>
                                                                                        <li><a href="<?php echo e(url('register-3')); ?>" class="<?php echo e(Request::is('register-3') ? 'active' : ''); ?>">Basic</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Forgot Password<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('forgot-password')); ?>" class="<?php echo e(Request::is('forgot-password') ? 'active' : ''); ?>">Cover</a></li>
                                                                                        <li><a href="<?php echo e(url('forgot-password-2')); ?>" class="<?php echo e(Request::is('forgot-password-2') ? 'active' : ''); ?>">Illustration</a></li>
                                                                                        <li><a href="<?php echo e(url('forgot-password-3')); ?>" class="<?php echo e(Request::is('forgot-password-3') ? 'active' : ''); ?>">Basic</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Reset Password<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('reset-password')); ?>" class="<?php echo e(Request::is('reset-password') ? 'active' : ''); ?>">Cover</a></li>
                                                                                        <li><a href="<?php echo e(url('reset-password-2')); ?>" class="<?php echo e(Request::is('reset-password-2') ? 'active' : ''); ?>">Illustration</a></li>
                                                                                        <li><a href="<?php echo e(url('reset-password-3')); ?>" class="<?php echo e(Request::is('reset-password-3') ? 'active' : ''); ?>">Basic</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Email Verification<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('email-verification')); ?>" class="<?php echo e(Request::is('email-verification') ? 'active' : ''); ?>">Cover</a></li>
                                                                                        <li><a href="<?php echo e(url('email-verification-2')); ?>" class="<?php echo e(Request::is('email-verification-2') ? 'active' : ''); ?>">Illustration</a></li>
                                                                                        <li><a href="<?php echo e(url('email-verification-3')); ?>" class="<?php echo e(Request::is('email-verification-3') ? 'active' : ''); ?>">Basic</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">2 Step Verification<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('two-step-verification')); ?>" class="<?php echo e(Request::is('two-step-verification') ? 'active' : ''); ?>">Cover</a></li>
                                                                                        <li><a href="<?php echo e(url('two-step-verification-2')); ?>" class="<?php echo e(Request::is('two-step-verification-2') ? 'active' : ''); ?>">Illustration</a></li>
                                                                                        <li><a href="<?php echo e(url('two-step-verification-3')); ?>" class="<?php echo e(Request::is('two-step-verification-3') ? 'active' : ''); ?>">Basic</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="<?php echo e(Request::is('lock-screen') ? 'active' : ''); ?>"><a href="<?php echo e(url('lock-screen')); ?>">Lock Screen</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"><i class="ti ti-file-x fs-16 me-2"></i><span>Error Pages</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('error-404')); ?>" class="<?php echo e(Request::is('error-404') ? 'active' : ''); ?>">404 Error </a></li>
                                                                        <li><a href="<?php echo e(url('error-500')); ?>" class="<?php echo e(Request::is('error-500') ? 'active' : ''); ?>">500 Error </a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('blank-page') ? 'active' : ''); ?>">
                                                                <a href="<?php echo e(url('blank-page')); ?>" ><i class="ti ti-file fs-16 me-2"></i><span>Blank Page</span> </a>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('pricing') ? 'active' : ''); ?>">
                                                                <a href="<?php echo e(url('pricing')); ?>" ><i class="ti ti-currency-dollar fs-16 me-2"></i><span>Pricing</span> </a>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('coming-soon') ? 'active' : ''); ?>">
                                                                <a href="<?php echo e(url('coming-soon')); ?>" ><i class="ti ti-send fs-16 me-2"></i><span>Coming Soon</span> </a>
                                                        </li>
                                                        <li class="<?php echo e(Request::is('under-maintenance') ? 'active' : ''); ?>">
                                                                <a href="<?php echo e(url('under-maintenance')); ?>"><i class="ti ti-alert-triangle fs-16 me-2"></i><span>Under Maintenance</span> </a>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Settings</h6>
                                                <ul>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('general-settings','security-settings','notification','activities','connected-apps') ? 'active' : ''); ?>"><i class="ti ti-settings fs-16 me-2"></i><span>General Settings</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('general-settings')); ?>" class="<?php echo e(Request::is('general-settings') ? 'active' : ''); ?>">Profile</a></li>
                                                                        <li><a href="<?php echo e(url('security-settings')); ?>" class="<?php echo e(Request::is('security-settings') ? 'active' : ''); ?>">Security</a></li>
                                                                        <li><a href="<?php echo e(url('notification')); ?>" class="<?php echo e(Request::is('notification','activities') ? 'active' : ''); ?>">Notifications</a></li>
                                                                        <li><a href="<?php echo e(url('connected-apps')); ?>" class="<?php echo e(Request::is('connected-apps') ? 'active' : ''); ?>">Connected Apps</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('system-settings','company-settings','localization-settings','prefixes','preference','appearance','social-authentication','language-settings') ? 'active' : ''); ?>"><i class="ti ti-world fs-16 me-2"></i><span>Website Settings</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('system-settings')); ?>" class="<?php echo e(Request::is('system-settings') ? 'active' : ''); ?>">System Settings</a></li>
                                                                        <li><a href="<?php echo e(url('company-settings')); ?>" class="<?php echo e(Request::is('company-settings') ? 'active' : ''); ?>">Company Settings </a></li>
                                                                        <li><a href="<?php echo e(url('localization-settings')); ?>" class="<?php echo e(Request::is('localization-settings') ? 'active' : ''); ?>">Localization</a></li>
                                                                        <li><a href="<?php echo e(url('prefixes')); ?>" class="<?php echo e(Request::is('prefixes') ? 'active' : ''); ?>">Prefixes</a></li>
                                                                        <li><a href="<?php echo e(url('preference')); ?>" class="<?php echo e(Request::is('preference') ? 'active' : ''); ?>">Preference</a></li>
                                                                        <li><a href="<?php echo e(url('appearance')); ?>" class="<?php echo e(Request::is('appearance') ? 'active' : ''); ?>">Appearance</a></li>
                                                                        <li><a href="<?php echo e(url('social-authentication')); ?>" class="<?php echo e(Request::is('social-authentication') ? 'active' : ''); ?>">Social Authentication</a></li>
                                                                        <li><a href="<?php echo e(url('language-settings')); ?>" class="<?php echo e(Request::is('language-settings') ? 'active' : ''); ?>">Language</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('invoice-settings','invoice-template','printer-settings','pos-settings','custom-fields') ? 'active' : ''); ?>"><i class="ti ti-device-mobile fs-16 me-2"></i>
                                                                        <span>App Settings</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Invoice<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('invoice-settings')); ?>" class="<?php echo e(Request::is('invoice-settings') ? 'active' : ''); ?>">Invoice Settings</a></li>
                                                                                        <li><a href="<?php echo e(url('invoice-template')); ?>" class="<?php echo e(Request::is('invoice-template') ? 'active' : ''); ?>">Invoice Template</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li><a href="<?php echo e(url('printer-settings')); ?>" class="<?php echo e(Request::is('printer-settings') ? 'active' : ''); ?>">Printer</a></li>
                                                                        <li><a href="<?php echo e(url('pos-settings')); ?>" class="<?php echo e(Request::is('pos-settings') ? 'active' : ''); ?>">POS</a></li>
                                                                        <li><a href="<?php echo e(url('custom-fields')); ?>" class="<?php echo e(Request::is('custom-fields') ? 'active' : ''); ?>">Custom Fields</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('email-settings','email-template','sms-settings','sms-template','otp-settings','gdpr-settings','payment-gateway-settings','bank-settings-grid','tax-rates','currency-settings') ? 'active' : ''); ?>"><i class="ti ti-device-desktop fs-16 me-2"></i>
                                                                        <span>System Settings</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);" class="<?php echo e(Request::is('email-settings','email-template') ? 'active' : ''); ?>">Email<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('email-settings')); ?>" class="<?php echo e(Request::is('email-settings') ? 'active' : ''); ?>">Email Settings</a></li>
                                                                                        <li><a href="<?php echo e(url('email-template')); ?>" class="<?php echo e(Request::is('email-template') ? 'active' : ''); ?>">Email Template</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);" class="<?php echo e(Request::is('sms-settings','sms-template') ? 'active' : ''); ?>">SMS<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="<?php echo e(url('sms-settings')); ?>" class="<?php echo e(Request::is('sms-settings') ? 'active' : ''); ?>">SMS Settings</a></li>
                                                                                        <li><a href="<?php echo e(url('sms-template')); ?>" class="<?php echo e(Request::is('sms-template') ? 'active' : ''); ?>">SMS Template</a></li>
                                                                                </ul>
                                                                        </li>
                                                                        <li class="<?php echo e(Request::is('otp-settings') ? 'active' : ''); ?>"><a href="<?php echo e(url('otp-settings')); ?>">OTP</a></li>
                                                                        <li class="<?php echo e(Request::is('gdpr-settings') ? 'active' : ''); ?>"><a href="<?php echo e(url('gdpr-settings')); ?>">GDPR Cookies</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('payment-gateway-settings','bank-settings-grid','tax-rates','currency-settings') ? 'active' : ''); ?>"><i class="ti ti-settings-dollar fs-16 me-2"></i>
                                                                        <span>Financial Settings</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('payment-gateway-settings')); ?>" class="<?php echo e(Request::is('payment-gateway-settings') ? 'active' : ''); ?>">Payment Gateway</a></li>
                                                                        <li><a href="<?php echo e(url('bank-settings-grid')); ?>" class="<?php echo e(Request::is('bank-settings-grid') ? 'active' : ''); ?>">Bank Accounts</a></li>
                                                                        <li><a href="<?php echo e(url('tax-rates')); ?>" class="<?php echo e(Request::is('tax-rates') ? 'active' : ''); ?>">Tax Rates</a></li>
                                                                        <li><a href="<?php echo e(url('currency-settings')); ?>" class="<?php echo e(Request::is('currency-settings') ? 'active' : ''); ?>">Currencies</a></li>
                                                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('storage-settings','ban-ip-address') ? 'active' : ''); ?>"><i class="ti ti-settings-2 fs-16 me-2"></i>
                                                                        <span>Other Settings</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                                                        <li><a href="<?php echo e(url('storage-settings')); ?>" class="<?php echo e(Request::is('storage-settings') ? 'active' : ''); ?>">Storage</a></li>
                                                                        <li><a href="<?php echo e(url('ban-ip-address')); ?>" class="<?php echo e(Request::is('ban-ip-address') ? 'active' : ''); ?>">Ban IP Address</a></li>
                                                                </ul>
                                                        </li>
                                                        <li>
                                                                <a href="<?php echo e(url('signin')); ?>" class="<?php echo e(Request::is('signin') ? 'active' : ''); ?>"><i class="ti ti-logout fs-16 me-2"></i><span>Logout</span> </a>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">UI Interface</h6>
                                                <ul>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('ui-alerts', 'ui-sortable', 'ui-swiperjs', 'ui-accordion', 'ui-avatar', 'ui-badges', 'ui-borders', 'ui-buttons', 'ui-buttons-group', 'ui-breadcrumb', 'ui-cards', 'ui-carousel', 'ui-colors', 'ui-dropdowns', 'ui-grid', 'ui-images', 'ui-lightbox', 'ui-modals', 'ui-media', 'ui-offcanvas', 'ui-pagination', 'ui-popovers', 'ui-progress', 'ui-placeholders', 'ui-rangeslider', 'ui-spinner', 'ui-sweetalerts', 'ui-nav-tabs', 'ui-toasts', 'ui-tooltips', 'ui-typography', 'ui-video') ? 'active subdrop' : ''); ?>">
                                                                        <i class="ti ti-vector-bezier fs-16 me-2"></i><span>Base UI</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                        <li><a href="<?php echo e(url('ui-alerts')); ?>"
                                                class="<?php echo e(Request::is('ui-alerts') ? 'active' : ''); ?>">Alerts</a></li>
                                        <li><a href="<?php echo e(url('ui-accordion')); ?>"
                                                class="<?php echo e(Request::is('ui-accordion') ? 'active' : ''); ?>">Accordion</a></li>
                                        <li><a href="<?php echo e(url('ui-avatar')); ?>"
                                                class="<?php echo e(Request::is('ui-avatar') ? 'active' : ''); ?>">Avatar</a></li>
                                        <li><a href="<?php echo e(url('ui-badges')); ?>"
                                                class="<?php echo e(Request::is('ui-badges') ? 'active' : ''); ?>">Badges</a></li>
                                        <li><a href="<?php echo e(url('ui-borders')); ?>"
                                                class="<?php echo e(Request::is('ui-borders') ? 'active' : ''); ?>">Border</a></li>
                                        <li><a href="<?php echo e(url('ui-buttons')); ?>"
                                                class="<?php echo e(Request::is('ui-buttons') ? 'active' : ''); ?>">Buttons</a></li>
                                        <li><a href="<?php echo e(url('ui-buttons-group')); ?>"
                                                class="<?php echo e(Request::is('ui-buttons-group') ? 'active' : ''); ?>">Button
                                                Group</a></li>
                                        <li><a href="<?php echo e(url('ui-breadcrumb')); ?>"
                                                class="<?php echo e(Request::is('ui-breadcrumb') ? 'active' : ''); ?>">Breadcrumb</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-cards')); ?>"
                                                class="<?php echo e(Request::is('ui-cards') ? 'active' : ''); ?>">Card</a></li>
                                        <li><a href="<?php echo e(url('ui-carousel')); ?>"
                                                class="<?php echo e(Request::is('ui-carousel') ? 'active' : ''); ?>">Carousel</a></li>
                                        <li><a href="<?php echo e(url('ui-colors')); ?>"
                                                class="<?php echo e(Request::is('ui-colors') ? 'active' : ''); ?>">Colors</a></li>
                                        <li><a href="<?php echo e(url('ui-dropdowns')); ?>"
                                                class="<?php echo e(Request::is('ui-dropdowns') ? 'active' : ''); ?>">Dropdowns</a></li>
                                        <li><a href="<?php echo e(url('ui-grid')); ?>"
                                                class="<?php echo e(Request::is('ui-grid') ? 'active' : ''); ?>">Grid</a></li>
                                        <li><a href="<?php echo e(url('ui-images')); ?>"
                                                class="<?php echo e(Request::is('ui-images') ? 'active' : ''); ?>">Images</a></li>
                                        <li><a href="<?php echo e(url('ui-lightbox')); ?>"
                                                class="<?php echo e(Request::is('ui-lightbox') ? 'active' : ''); ?>">Lightbox</a></li>
                                        <li><a href="<?php echo e(url('ui-media')); ?>"
                                                class="<?php echo e(Request::is('ui-media') ? 'active' : ''); ?>">Media</a></li>
                                        <li><a href="<?php echo e(url('ui-modals')); ?>"
                                                class="<?php echo e(Request::is('ui-modals') ? 'active' : ''); ?>">Modals</a></li>
                                        <li><a href="<?php echo e(url('ui-offcanvas')); ?>"
                                                class="<?php echo e(Request::is('ui-offcanvas') ? 'active' : ''); ?>">Offcanvas</a></li>
                                        <li><a href="<?php echo e(url('ui-pagination')); ?>"
                                                class="<?php echo e(Request::is('ui-pagination') ? 'active' : ''); ?>">Pagination</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-popovers')); ?>"
                                                class="<?php echo e(Request::is('ui-popovers') ? 'active' : ''); ?>">Popovers</a></li>
                                        <li><a href="<?php echo e(url('ui-progress')); ?>"
                                                class="<?php echo e(Request::is('ui-progress') ? 'active' : ''); ?>">Progress</a></li>
                                        <li><a href="<?php echo e(url('ui-placeholders')); ?>"
                                                class="<?php echo e(Request::is('ui-placeholders') ? 'active' : ''); ?>">Placeholders</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-rangeslider')); ?>"
                                                class="<?php echo e(Request::is('ui-rangeslider') ? 'active' : ''); ?>">Range Slider</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-spinner')); ?>"
                                                class="<?php echo e(Request::is('ui-spinner') ? 'active' : ''); ?>">Spinner</a></li>
                                        <li><a href="<?php echo e(url('ui-sweetalerts')); ?>"
                                                class="<?php echo e(Request::is('ui-sweetalerts') ? 'active' : ''); ?>">Sweet Alerts</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-nav-tabs')); ?>"
                                                class="<?php echo e(Request::is('ui-nav-tabs') ? 'active' : ''); ?>">Tabs</a></li>
                                        <li><a href="<?php echo e(url('ui-toasts')); ?>"
                                                class="<?php echo e(Request::is('ui-toasts') ? 'active' : ''); ?>">Toasts</a></li>
                                        <li><a href="<?php echo e(url('ui-tooltips')); ?>"
                                                class="<?php echo e(Request::is('ui-tooltips') ? 'active' : ''); ?>">Tooltips</a></li>
                                        <li><a href="<?php echo e(url('ui-typography')); ?>"
                                                class="<?php echo e(Request::is('ui-typography') ? 'active' : ''); ?>">Typography</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-video')); ?>"
                                                class="<?php echo e(Request::is('ui-video') ? 'active' : ''); ?>">Video</a></li>
                                        <li><a href="<?php echo e(url('ui-sortable')); ?>" class="<?php echo e(Request::is('ui-sortable') ? 'active' : ''); ?>">Sortable</a></li>
                                        <li><a href="<?php echo e(url('ui-swiperjs')); ?>" class="<?php echo e(Request::is('ui-swiperjs') ? 'active' : ''); ?>">Swiperjs</a></li>
                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"   class="<?php echo e(Request::is('ui-ribbon', 'ui-clipboard', 'ui-drag-drop', 'ui-rating', 'ui-text-editor', 'ui-counter', 'ui-scrollbar', 'ui-stickynote', 'ui-timeline') ? 'active subdrop' : ''); ?>">
                                                                        <i data-feather="layers"></i><span>Advanced UI</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                        <li><a href="<?php echo e(url('ui-ribbon')); ?>"
                                                class="<?php echo e(Request::is('ui-ribbon') ? 'active' : ''); ?>">Ribbon</a></li>
                                        <li><a href="<?php echo e(url('ui-clipboard')); ?>"
                                                class="<?php echo e(Request::is('ui-clipboard') ? 'active' : ''); ?>">Clipboard</a></li>
                                        <li><a href="<?php echo e(url('ui-drag-drop')); ?>"
                                                class="<?php echo e(Request::is('ui-drag-drop') ? 'active' : ''); ?>">Drag & Drop</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-rating')); ?>"
                                                class="<?php echo e(Request::is('ui-rating') ? 'active' : ''); ?>">Rating</a></li>
                                        <li><a href="<?php echo e(url('ui-text-editor')); ?>"
                                                class="<?php echo e(Request::is('ui-text-editor') ? 'active' : ''); ?>">Text Editor</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-counter')); ?>"
                                                class="<?php echo e(Request::is('ui-counter') ? 'active' : ''); ?>">Counter</a></li>
                                        <li><a href="<?php echo e(url('ui-scrollbar')); ?>"
                                                class="<?php echo e(Request::is('ui-scrollbar') ? 'active' : ''); ?>">Scrollbar</a></li>
                                        <li><a href="<?php echo e(url('ui-stickynote')); ?>"
                                                class="<?php echo e(Request::is('ui-stickynote') ? 'active' : ''); ?>">Sticky Note</a>
                                        </li>
                                        <li><a href="<?php echo e(url('ui-timeline')); ?>"
                                                class="<?php echo e(Request::is('ui-timeline') ? 'active' : ''); ?>">Timeline</a></li>
                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"   class="<?php echo e(Request::is('chart-apex', 'chart-c3', 'chart-js', 'chart-morris', 'chart-flot', 'chart-peity') ? 'active subdrop' : ''); ?>"><i class="ti ti-chart-infographic fs-16 me-2"></i>
                                                                        <span>Charts</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                        <li><a href="<?php echo e(url('chart-apex')); ?>"
                                                class="<?php echo e(Request::is('chart-apex') ? 'active' : ''); ?>">Apex Charts</a></li>
                                        <li><a href="<?php echo e(url('chart-c3')); ?>"
                                                class="<?php echo e(Request::is('chart-c3') ? 'active' : ''); ?>">Chart C3</a></li>
                                        <li><a href="<?php echo e(url('chart-js')); ?>"
                                                class="<?php echo e(Request::is('chart-js') ? 'active' : ''); ?>">Chart Js</a></li>
                                        <li><a href="<?php echo e(url('chart-morris')); ?>"
                                                class="<?php echo e(Request::is('chart-morris') ? 'active' : ''); ?>">Morris Charts</a>
                                        </li>
                                        <li><a href="<?php echo e(url('chart-flot')); ?>"
                                                class="<?php echo e(Request::is('chart-flot') ? 'active' : ''); ?>">Flot Charts</a></li>
                                        <li><a href="<?php echo e(url('chart-peity')); ?>"
                                                class="<?php echo e(Request::is('chart-peity') ? 'active' : ''); ?>">Peity Charts</a>
                                        </li>
                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"  class="<?php echo e(Request::is('icon-fontawesome', 'icon-tabler', 'icon-bootstrap', 'icon-remix',  'icon-feather', 'icon-ionic', 'icon-material', 'icon-pe7', 'icon-simpleline', 'icon-themify', 'icon-weather', 'icon-typicon', 'icon-flag') ? 'active subdrop' : ''); ?>"><i class="ti ti-icons fs-16 me-2"></i>
                                                                        <span>Icons</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                        <li><a href="<?php echo e(url('icon-fontawesome')); ?>"
                                                class="<?php echo e(Request::is('icon-fontawesome') ? 'active' : ''); ?>">Fontawesome
                                                Icons</a></li>
                                        <li><a href="<?php echo e(url('icon-feather')); ?>"
                                                class="<?php echo e(Request::is('icon-feather') ? 'active' : ''); ?>">Feather Icons</a>
                                        </li>
                                        <li><a href="<?php echo e(url('icon-ionic')); ?>"
                                                class="<?php echo e(Request::is('icon-ionic') ? 'active' : ''); ?>">Ionic Icons</a></li>
                                        <li><a href="<?php echo e(url('icon-material')); ?>"
                                                class="<?php echo e(Request::is('icon-material') ? 'active' : ''); ?>">Material Icons</a>
                                        </li>
                                        <li><a href="<?php echo e(url('icon-pe7')); ?>"
                                                class="<?php echo e(Request::is('icon-pe7') ? 'active' : ''); ?>">Pe7 Icons</a></li>
                                        <li><a href="<?php echo e(url('icon-simpleline')); ?>"
                                                class="<?php echo e(Request::is('icon-simpleline') ? 'active' : ''); ?>">Simpleline
                                                Icons</a></li>
                                        <li><a href="<?php echo e(url('icon-themify')); ?>"
                                                class="<?php echo e(Request::is('icon-themify') ? 'active' : ''); ?>">Themify Icons</a>
                                        </li>
                                        <li><a href="<?php echo e(url('icon-weather')); ?>"
                                                class="<?php echo e(Request::is('icon-weather') ? 'active' : ''); ?>">Weather Icons</a>
                                        </li>
                                        <li><a href="<?php echo e(url('icon-typicon')); ?>"
                                                class="<?php echo e(Request::is('icon-typicon') ? 'active' : ''); ?>">Typicon Icons</a>
                                        </li>
                                        <li><a href="<?php echo e(url('icon-flag')); ?>"
                                                class="<?php echo e(Request::is('icon-flag') ? 'active' : ''); ?>">Flag Icons</a></li>
                                        <li><a href="<?php echo e(url('icon-tabler')); ?>" class="<?php echo e(Request::is('icon-tabler') ? 'active' : ''); ?>">Tabler Icons</a></li>
                                        <li><a href="<?php echo e(url('icon-bootstrap')); ?>" class="<?php echo e(Request::is('icon-bootstrap') ? 'active' : ''); ?>">Bootstrap Icons</a></li>
                                        <li><a href="<?php echo e(url('icon-remix')); ?>" class="<?php echo e(Request::is('icon-remix') ? 'active' : ''); ?>">Remix Icons</a></li>
                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('form-wizard', 'form-select2', 'form-validation', 'form-floating-labels', 'form-vertical', 'form-horizontal', 'form-basic-inputs', 'form-checkbox-radios', 'form-input-groups', 'form-grid-gutters', 'form-select', 'form-mask', 'form-fileupload') ? 'active subdrop' : ''); ?>">
                                                                        <i class="ti ti-input-search fs-16 me-2"></i><span>Forms</span><span class="menu-arrow"></span>
                                                                </a>
                                                                <ul>
                                                                        <li class="submenu submenu-two">
                                                                                <a href="javascript:void(0);">Form Elements<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                <li><a href="<?php echo e(url('form-basic-inputs')); ?>"
                                                        class="<?php echo e(Request::is('form-basic-inputs') ? 'active' : ''); ?>">Basic
                                                        Inputs</a></li>
                                                <li><a href="<?php echo e(url('form-checkbox-radios')); ?>"
                                                        class="<?php echo e(Request::is('form-checkbox-radios') ? 'active' : ''); ?>">Checkbox
                                                        & Radios</a></li>
                                                <li><a href="<?php echo e(url('form-input-groups')); ?>"
                                                        class="<?php echo e(Request::is('form-input-groups') ? 'active' : ''); ?>">Input
                                                        Groups</a></li>
                                                <li><a href="<?php echo e(url('form-grid-gutters')); ?>"
                                                        class="<?php echo e(Request::is('form-grid-gutters') ? 'active' : ''); ?>">Grid &
                                                        Gutters</a></li>
                                                <li><a href="<?php echo e(url('form-select')); ?>"
                                                        class="<?php echo e(Request::is('form-select') ? 'active' : ''); ?>">Form
                                                        Select</a></li>
                                                <li><a href="<?php echo e(url('form-mask')); ?>"
                                                        class="<?php echo e(Request::is('form-mask') ? 'active' : ''); ?>">Input
                                                        Masks</a></li>
                                                <li><a href="<?php echo e(url('form-fileupload')); ?>"
                                                        class="<?php echo e(Request::is('form-fileupload') ? 'active' : ''); ?>">File
                                                        Uploads</a></li>
                                        </ul>

                                                                        </li>
                                                                        <li class="submenu submenu-two">
                                                                                <a href="javascript:void(0);">Layouts<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                <li><a href="<?php echo e(url('form-horizontal')); ?>"
                                                        class="<?php echo e(Request::is('form-horizontal') ? 'active' : ''); ?>">Horizontal
                                                        Form</a></li>
                                                <li><a href="<?php echo e(url('form-vertical')); ?>"
                                                        class="<?php echo e(Request::is('form-vertical') ? 'active' : ''); ?>">Vertical
                                                        Form</a></li>
                                                <li><a href="<?php echo e(url('form-floating-labels')); ?>"
                                                        class="<?php echo e(Request::is('form-floating-labels') ? 'active' : ''); ?>">Floating
                                                        Labels</a></li>
                                        </ul>
                                                                        </li>
                                                                        <li><a href="<?php echo e(url('form-validation')); ?>"
                                                class="<?php echo e(Request::is('form-validation') ? 'active' : ''); ?>">Form
                                                Validation</a></li>
                                        <li><a href="<?php echo e(url('form-select2')); ?>"
                                                class="<?php echo e(Request::is('form-select2') ? 'active' : ''); ?>">Select2</a></li>
                                        <li><a href="<?php echo e(url('form-wizard')); ?>"
                                                class="<?php echo e(Request::is('form-wizard') ? 'active' : ''); ?>">Form Wizard</a></li>
                                        <li><a href="<?php echo e(url('form-pickers')); ?>" class="<?php echo e(Request::is('form-pickers') ? 'active' : ''); ?>">Form Picker</a></li>
                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);" class="<?php echo e(Request::is('tables-basic','data-tables') ? 'active' : ''); ?>"><i class="ti ti-table fs-16 me-2"></i><span>Tables</span><span class="menu-arrow"></span></a>
                                                        <ul>
                                        <li><a href="<?php echo e(url('tables-basic')); ?>"
                                                class="<?php echo e(Request::is('tables-basic') ? 'active' : ''); ?>">Basic Tables </a>
                                        </li>
                                        <li><a href="<?php echo e(url('data-tables')); ?>"
                                                class="<?php echo e(Request::is('data-tables') ? 'active' : ''); ?>">Data Table </a></li>
                                </ul>
                                                        </li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"><i class="ti ti-map-pin-pin fs-16 me-2"></i><span>Maps</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                        <li><a href="<?php echo e(url('maps-vector')); ?>">Vector</a></li>
                                        <li><a href="<?php echo e(url('maps-leaflet')); ?>">Leaflet</a></li>
                                </ul>
                                                        </li>
                                                </ul>
                                        </li>
                                        <li class="submenu-open">
                                                <h6 class="submenu-hdr">Help</h6>
                                                <ul>
                                                        <li><a href="javascript:void(0);"><i class="ti ti-file-text fs-16 me-2"></i><span>Documentation</span></a></li>
                                                        <li><a href="javascript:void(0);"><i class="ti ti-exchange fs-16 me-2"></i><span>Changelog </span><span class="badge bg-primary badge-xs text-white fs-10 ms-2">v2.1.9</span></a></li>
                                                        <li class="submenu">
                                                                <a href="javascript:void(0);"><i class="ti ti-menu-2 fs-16 me-2"></i><span>Multi Level</span><span class="menu-arrow"></span></a>
                                                                <ul>
                                                                        <li><a href="javascript:void(0);">Level 1.1</a></li>
                                                                        <li class="submenu submenu-two"><a href="javascript:void(0);">Level 1.2<span class="menu-arrow inside-submenu"></span></a>
                                                                                <ul>
                                                                                        <li><a href="javascript:void(0);">Level 2.1</a></li>
                                                                                        <li class="submenu submenu-two submenu-three"><a href="javascript:void(0);">Level 2.2<span class="menu-arrow inside-submenu inside-submenu-two"></span></a>
                                                                                                <ul>
                                                                                                        <li><a href="javascript:void(0);">Level 3.1</a></li>
                                                                                                        <li><a href="javascript:void(0);">Level 3.2</a></li>
                                                                                                </ul>
                                                                                        </li>
                                                                                </ul>
                                                                        </li>
                                                                </ul>
                                                        </li>
                                                </ul>
                                        </li>
                                </ul>
                        </div>
                </div>
        </div>
        <!-- /Sidebar -->
<?php endif; ?>