<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


    <li class="nav-item">
      <a href="{{ url('retailer/dashboard') }}" class="nav-link {{ (url()->full() == url('retailer/dashboard'))? 'active':''}}">
        <i class="nav-icon fas fa-tachometer-alt text-danger"></i>
        <p class="text">Dashboard</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ url('retailer/topup-history') }}" class="nav-link {{ (url()->full() == url('retailer/topup-history'))? 'active':''}}">
        <i class="fas fa-wallet nav-icon text-info"></i>
        <p>Topup List</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ url('retailer/passbook') }}" class="nav-link {{ (url()->full() == url('retailer/passbook'))? 'active':''}}">
        <i class="fas fa-solid fa-book nav-icon text-warning"></i>
        <p>Passbook</p>
      </a>
    </li>


    <li class="nav-item">
      <a href="{{ url('retailer/transaction') }}" class="nav-link {{ (url()->full() == url('retailer/transaction'))? 'active':''}}">
        <!-- <i class="fas fa-solid fa-book nav-icon text-warning"></i> -->
        <i class="fas fa-money-bill-wave nav-icon text-primary"></i>
        <p>Transaction</p>
      </a>
    </li>


    <!-- <li class="nav-item {{ (url()->full() == url('retailer/customer-trans') || url()->full() == url('retailer/retailer-trans'))?'menu-is-opening menu-open':''}}">
      <a href="javascript:void(0);" class="nav-link ">
        <i class="fas fa-list-ul nav-icon text-primary"></i>
        <p>Transaction
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        @if(!empty(moneyTransferOption()->dmt_transfer_offline))
        <li class="nav-item {{ (url()->full() == url('retailer/customer-trans') || url()->full() == url('retailer/retailer-trans'))?'d-block':''}}">
          <a href="{{ url('retailer/customer-trans') }}" class="nav-link {{ (url()->full() == url('retailer/customer-trans'))? 'active':''}}">
            <i class="fas fa-file-invoice-dollar nav-icon"></i>
            <p>DMT Transaction</p>
          </a>
        </li>
        @endif
        @if(!empty(moneyTransferOption()->payout_offline))
        <li class="nav-item">
          <a href="{{ url('retailer/retailer-trans') }}" class="nav-link {{ (url()->full() == url('retailer/retailer-trans'))? 'active':''}}">
            <i class="fas fa-money-check nav-icon"></i>
            <p>
              Payout Transaction
            </p>
          </a>
        </li>
        @endif
        @if(!empty(moneyTransferOption()->payout_offline_api))
        <li class="nav-item">
          <a href="{{ url('retailer/offline-payout') }}" class="nav-link {{ (url()->full() == url('retailer/offline-payout'))? 'active':''}}">
            <i class="fas fa-hand-holding-usd nav-icon"></i>
            <p>
               Payout Api
            </p>
          </a>
        </li>
        @endif

      </ul>
    </li> -->

    <li class="nav-item">
      <a href="{{ url('retailer/webhook-api') }}" class="nav-link {{ (url()->full() == url('retailer/webhook-api'))? 'active':''}}">
        <i class="fas fa-swatchbook  nav-icon text-danger"></i>

        <p>Webhook & Api</p>
      </a>
    </li>

  </ul>
</nav>
<!-- /.sidebar-menu -->