<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


    <li class="nav-item">
      <a href="{{ url('employee/dashboard') }}" class="nav-link {{ (url()->full() == url('employee/dashboard'))? 'active':''}}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p class="text">Dashboard</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ url('employee/outlets') }}" class="nav-link {{ (url()->full() == url('employee/outlets'))? 'active':''}}">
        <i class=" nav-icon fas fa-store"></i>
        <p>Outlets</p>
      </a>
    </li>

    <li class="nav-item {{ (url()->full() == url('employee/topup-list') || url()->full() == url('employee/pending-topup'))?'menu-is-opening menu-open':''}}">
      <a href="javascript:void(0);" class="nav-link">
        <i class="fas fa-wallet nav-icon"></i>
        <p>Topup Request
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item {{ (url()->full() == url('employee/topup-list') || url()->full() == url('employee/pending-topup'))?'d-block':''}}">
          <a href="{{ url('employee/topup-list') }}" class="nav-link {{ (url()->full() == url('employee/topup-list'))?'active':''}}">
            <i class="fas fa-hand-holding-usd nav-icon"></i>
            <p>All Request</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('employee/pending-topup') }}" class="nav-link {{ (url()->full() == url('employee/pending-topup'))? 'active':''}}">
            <i class="fas fa-hand-holding-water nav-icon"></i>
            <p>
              Pending Request
            </p>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a href="{{ url('employee/passbook') }}" class="nav-link {{ (url()->full() == url('employee/passbook'))? 'active':''}}">
        <i class="fas fa-solid fa-book nav-icon"></i>
        <p>Passbook</p>
      </a>
    </li>

    <!-- <li class="nav-item">
      <a href="{{ url('employee/a-transaction') }}" class="nav-link {{ (url()->full() == url('employee/a-transaction'))? 'active':''}}">

        <i class="fas fa-money-bill-wave nav-icon"></i>
        <p>Transaction</p>
      </a>
    </li> -->


    <li class="nav-item {{ (url()->full() == url('employee/a-transaction') || url()->full() == url('employee/refund-pending'))?'menu-is-opening menu-open':''}}">
      <a href="javascript:void(0);" class="nav-link">
        <i class="fas fa-wallet nav-icon"></i>
        <p>Transaction
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item {{ (url()->full() == url('employee/a-transaction') || url()->full() == url('employee/refund-pending'))?'d-block':''}}">
          <a href="{{ url('employee/a-transaction') }}" class="nav-link {{ (url()->full() == url('employee/a-transaction'))?'active':''}}">
            <i class="fas fa-hand-holding-usd nav-icon"></i>
            <p>All Transaction</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('employee/refund-pending') }}" class="nav-link {{ (url()->full() == url('employee/refund-pending'))? 'active':''}}">
            <i class="fas fa-hand-holding-water nav-icon"></i>
            <p>
              Refund Pending
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('employee/transaction-report') }}" class="nav-link {{ (url()->full() == url('employee/transaction-report'))? 'active':''}}">
            <i class="fas fa-solid fa-file-circle-check nav-icon"></i>
            <p>
              Report
            </p>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a href="{{ url('employee/payment-channel') }}" class="nav-link {{ (url()->full() == url('employee/payment-channel'))? 'active':''}}">
        <i class="fas fa-regular fa-dice nav-icon"></i>
        <p>Payment Channel</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ url('employee/comment') }}" class="nav-link {{ (url()->full() == url('employee/comment'))? 'active':''}}">
        <i class="fas fa-regular nav-icon fa-comment-medical" style="color: white;"></i>
        <p>Comment List</p>
      </a>
    </li>


    <li class="nav-item {{ (url()->full() == url('employee/bank-account') || url()->full() == url('employee/upi') || url()->full() == url('employee/qr-code'))?'menu-is-opening menu-open':''}}">
      <a href="javascript:void(0);" class="nav-link">
        <i class="nav-icon fas fa-money-bill-wave"></i>
        <p>Payment Mode
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item {{ (url()->full() == url('employee/bank-account') || url()->full() == url('employee/upi') || url()->full() == url('employee/qr-code'))?'d-block':''}}">
          <a href="{{ url('employee/bank-account') }}" class="nav-link {{ (url()->full() == url('employee/bank-account'))?'active':''}}">
            <i class="nav-icon fas fa-university"></i>
            <p>Bank Account</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('employee/upi') }}" class="nav-link {{ (url()->full() == url('employee/upi'))? 'active':''}}">
            <i class="nav-icon fas fa-rupee-sign"></i>
            <p>
              UPI
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('employee/qr-code') }}" class="nav-link {{ (url()->full() == url('employee/qr-code'))? 'active':''}}">
            <i class="nav-icon fas fa-qrcode"></i>
            <p>QR Code</p>
          </a>
        </li>
      </ul>
    </li>


    <li class="nav-item {{ (url()->full() == url('employee/credit') || url()->full() == url('employee/debit'))?'menu-is-opening menu-open':''}}">
      <a href="javascript:void(0);" class="nav-link">
        <i class="fas fa-list nav-icon"></i>
        <p>Action
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item {{ (url()->full() == url('employee/credit') || url()->full() == url('employee/debit'))?'d-block':''}}">
          <a href="{{ url('employee/credit') }}" class="nav-link {{ (url()->full() == url('employee/credit'))?'active':''}}">
            <i class="fas fa-money-bill nav-icon"></i>
            <p>Manual Credit</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('employee/debit') }}" class="nav-link {{ (url()->full() == url('employee/debit'))? 'active':''}}">
            <i class="fas fa-money-bill nav-icon"></i>
            <p>
              Manual Debit
            </p>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a href="{{ url('employee/earn-history') }}" class="nav-link {{ (url()->full() == url('employee/earn-history'))? 'active':''}}">
        <i class="fas fa-solid fa-filter-circle-dollar nav-icon"></i>
        <p class="text">Earned History</p>
      </a>
    </li>

    <!-- <li class="nav-item">
      <a href="{{ url('employee/withdrawal') }}" class="nav-link {{ (url()->full() == url('employee/withdrawal'))? 'active':''}}">
        <i class="fas fa-regular nav-icon fa-comment-medical" style="color: white;"></i>
        <p>Withdrawal</p>
      </a>
    </li> -->

  </ul>
</nav>
<!-- /.sidebar-menu -->