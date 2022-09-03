<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


    <li class="nav-item">
      <a href="{{ url('retailer/dashboard') }}" class="nav-link {{ (url()->full() == url('retailer/dashboard'))? 'active':''}}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p class="text">Dashboard</p>
      </a>
    </li>

    <!-- <li class="nav-item">
      <a href="{{ url('retailer/topup-history') }}" class="nav-link {{ (url()->full() == url('retailer/topup-history'))? 'active':''}}">
        <i class="fas fa-wallet nav-icon"></i>
        <p>Topup List</p>
      </a>
    </li> -->


    <li class="nav-item {{ (url()->full() == url('retailer/topup-history') || url()->full() == url('retailer/pending-topup'))?'menu-is-opening menu-open':''}}">
      <a href="javascript:void(0);" class="nav-link">
        <i class="fas fa-wallet nav-icon"></i>
        <p>Topup Request
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item {{ (url()->full() == url('retailer/topup-history') || url()->full() == url('retailer/pending-topup'))?'d-block':''}}">
          <a href="{{ url('retailer/topup-history') }}" class="nav-link {{ (url()->full() == url('retailer/topup-history'))?'active':''}}">
            <i class="fas fa-hand-holding-usd nav-icon"></i>
            <p>All Request</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('retailer/pending-topup') }}" class="nav-link {{ (url()->full() == url('retailer/pending-topup'))? 'active':''}}">
            <i class="fas fa-hand-holding-water nav-icon"></i>
            <p>
              Pending Request
            </p>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a href="{{ url('retailer/passbook') }}" class="nav-link {{ (url()->full() == url('retailer/passbook'))? 'active':''}}">
        <i class="fas fa-solid fa-book nav-icon"></i>
        <p>Passbook</p>
      </a>
    </li>

    <!-- <li class="nav-item">
      <a href="{{ url('retailer/transaction') }}" class="nav-link {{ (url()->full() == url('retailer/transaction'))? 'active':''}}">

        <i class="fas fa-money-bill-wave nav-icon"></i>
        <p>Transaction</p>
      </a>
    </li> -->

    <li class="nav-item {{ (url()->full() == url('retailer/transaction') || url()->full() == url('retailer/refund-pending'))?'menu-is-opening menu-open':''}}">
      <a href="javascript:void(0);" class="nav-link">
        <i class="fas fa-wallet nav-icon"></i>
        <p>Transaction
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item {{ (url()->full() == url('retailer/transaction') || url()->full() == url('retailer/refund-pending'))?'d-block':''}}">
          <a href="{{ url('retailer/transaction') }}" class="nav-link {{ (url()->full() == url('retailer/transaction'))?'active':''}}">
            <i class="fas fa-hand-holding-usd nav-icon"></i>
            <p>All Transaction</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('retailer/refund-pending') }}" class="nav-link {{ (url()->full() == url('retailer/refund-pending'))? 'active':''}}">
            <i class="fas fa-hand-holding-water nav-icon"></i>
            <p>
              Refund Pending
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ url('retailer/transaction-report') }}" class="nav-link {{ (url()->full() == url('retailer/transaction-report'))? 'active':''}}">
            <i class="fas fa-solid fa-file-circle-check nav-icon"></i>
            <p>
              Report
            </p>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item {{ (url()->full() == url('retailer/credit-report') || url()->full() == url('retailer/debit-report'))?'d-block':''}}">
      <a href="{{ url('retailer/credit-report') }}" class="nav-link {{ (url()->full() == url('retailer/credit-report'))?'active':''}}">
        <i class="fas fa-money-bill nav-icon"></i>
        <p>Manual Credit</p>
      </a>
    </li>

    <!-- <li class="nav-item {{ (url()->full() == url('retailer/credit-report') || url()->full() == url('retailer/debit-report'))?'menu-is-opening menu-open':''}}">
      <a href="javascript:void(0);" class="nav-link">
        <i class="fas fa-list nav-icon"></i>
        <p>Credit/Debit
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item {{ (url()->full() == url('retailer/credit-report') || url()->full() == url('retailer/debit-report'))?'d-block':''}}">
          <a href="{{ url('retailer/credit-report') }}" class="nav-link {{ (url()->full() == url('retailer/credit-report'))?'active':''}}">
            <i class="fas fa-money-bill nav-icon"></i>
            <p>Manual Credit</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('retailer/debit-report') }}" class="nav-link {{ (url()->full() == url('retailer/debit-report'))? 'active':''}}">
            <i class="fas fa-money-bill nav-icon"></i>
            <p>
              Manual Debit
            </p>
          </a>
        </li>
      </ul>
    </li> -->


    @if(!empty(MoneyPartnerOption()->e_collection) && MoneyPartnerOption()->e_collection ==1)
    <li class="nav-item">
      <a href="{{ url('retailer/e-collection') }}" class="nav-link {{ (url()->full() == url('retailer/e-collection'))? 'active':''}}">
        <i class="fas fa-ethernet  nav-icon"></i>
        <p>E-Collection</p>
      </a>
    </li>
    @endif


    @if(!empty(MoneyPartnerOption()->recharge) && MoneyPartnerOption()->recharge ==1)
    <li class="nav-item">
      <a href="{{ url('retailer/services') }}" class="nav-link {{ (url()->full() == url('retailer/services'))? 'active':''}}">
        <i class="fa-brands fa-servicestack nav-icon"></i>
        <p>Services</p>
      </a>
    </li>
    @endif

    <li class="nav-item {{ (url()->full() == url('retailer/webhook-api') || url()->full() == url('retailer/pin-password'))?'menu-is-opening menu-open':''}}">
      <a href="javascript:void(0);" class="nav-link ">
        <i class="fas fa-cogs nav-icon"></i>
        <p>Setting
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ url('retailer/webhook-api') }}" class="nav-link {{ (url()->full() == url('retailer/webhook-api'))? 'active':''}}">
            <i class="fas fa-swatchbook  nav-icon"></i>
            <p>Webhook & Api</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ url('retailer/pin-password') }}" class="nav-link {{ (url()->full() == url('retailer/pin-password'))? 'active':''}}">
            <!-- <i class="fas fa-money-check "></i> -->
            <i class="fas fa-key nav-icon"></i>
            <p>
              Pin & Password
            </p>
          </a>
        </li>


      </ul>
    </li>



  </ul>
</nav>
<!-- /.sidebar-menu -->