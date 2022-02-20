<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


    <li class="nav-item">
      <a href="{{ url('admin/dashboard') }}" class="nav-link {{ (url()->full() == url('admin/dashboard'))? 'active':''}}">
        <i class="nav-icon fas fa-tachometer-alt text-danger"></i>
        <p class="text">Dashboard</p>
      </a>
    </li>



    <li class="nav-item">
      <a href="{{ url('admin/outlets') }}" class="nav-link {{ (url()->full() == url('admin/outlets'))? 'active':''}}">
        <i class=" nav-icon fas fa-store  text-warning"></i>
        <p>Outlets</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ url('admin/employee') }}" class="nav-link {{ (url()->full() == url('admin/employee'))? 'active':''}}">
        <i class=" nav-icon fas fa-users"></i>
        <p>Employee</p>
      </a>
    </li>

    <li class="nav-item {{ (url()->full() == url('admin/bank-account') || url()->full() == url('admin/upi') || url()->full() == url('admin/qr-code'))?'menu-is-opening menu-open':''}}">
      <a href="javascript:void(0);" class="nav-link">
        <i class="nav-icon fas fa-money-bill-wave text-success"></i>
        <p>Payment Mode
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item {{ (url()->full() == url('admin/bank-account') || url()->full() == url('admin/upi') || url()->full() == url('admin/qr-code'))?'d-block':''}}">
          <a href="{{ url('admin/bank-account') }}" class="nav-link {{ (url()->full() == url('admin/bank-account'))?'active':''}}">
            <i class="nav-icon fas fa-university"></i>
            <p>Bank Account</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('admin/upi') }}" class="nav-link {{ (url()->full() == url('admin/upi'))? 'active':''}}">
            <i class="nav-icon fas fa-rupee-sign"></i>
            <p>
              UPI
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('admin/qr-code') }}" class="nav-link {{ (url()->full() == url('admin/qr-code'))? 'active':''}}">
            <i class="nav-icon fas fa-qrcode"></i>
            <p>QR Code</p>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a href="{{ url('admin/topup-list') }}" class="nav-link {{ (url()->full() == url('admin/topup-list'))? 'active':''}}">
        <i class="fas fa-wallet nav-icon text-info"></i>
        <p class="text">Topup Request</p>
      </a>
    </li>

    <li class="nav-item {{ (url()->full() == url('admin/a-customer-trans') || url()->full() == url('admin/a-retailer-trans'))?'menu-is-opening menu-open':''}}">
      <a href="javascript:void(0);" class="nav-link ">
        <i class="fas fa-list-ul nav-icon text-primary"></i>
        <p>Transaction
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item {{ (url()->full() == url('admin/a-customer-trans') || url()->full() == url('admin/a-retailer-trans'))?'d-block':''}}">
          <a href="{{ url('admin/a-customer-trans') }}" class="nav-link {{ (url()->full() == url('admin/a-customer-trans'))? 'active':''}}">
            <i class="fas fa-file-invoice-dollar nav-icon"></i>
            <p>DMT Transaction</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('admin/a-retailer-trans') }}" class="nav-link {{ (url()->full() == url('admin/a-retailer-trans'))? 'active':''}}">
          <i class="fas fa-money-check nav-icon"></i>
            <p>
              Payout Transaction
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ url('admin/a-offline-payout') }}" class="nav-link {{ (url()->full() == url('admin/a-offline-payout'))? 'active':''}}">
            <i class="fas fa-hand-holding-usd nav-icon"></i>
            <p>
              Offline Payout
            </p>
          </a>
        </li>

      </ul>
    </li>


    <li class="nav-item">
      <a href="{{ url('admin/payment-channel') }}" class="nav-link {{ (url()->full() == url('admin/payment-channel'))? 'active':''}}">

        <i class="fas fa-regular fa-dice nav-icon text-danger"></i>
        <p>Payment Channel</p>
      </a>
    </li>


    <li class="nav-item">
      <a href="{{ url('admin/comment') }}" class="nav-link {{ (url()->full() == url('admin/comment'))? 'active':''}}">

        <i class="fas fa-regular nav-icon fa-comment-medical" style="color: white;"></i>
        <p>Comment List</p>
      </a>
    </li>

    <!-- <li class="nav-header">MULTI LEVEL EXAMPLE</li>
    <li class="nav-item">
      <a href="#" class="nav-link">
        <i class="fas fa-circle nav-icon"></i>
        <p>Level 1</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="#" class="nav-link">
        <i class="nav-icon fas fa-circle"></i>
        <p>
          Level 1
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Level 2</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>
              Level 2
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-dot-circle nav-icon"></i>
                <p>Level 3</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-dot-circle nav-icon"></i>
                <p>Level 3</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-dot-circle nav-icon"></i>
                <p>Level 3</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Level 2</p>
          </a>
        </li>
      </ul>
    </li> -->

  </ul>
</nav>
<!-- /.sidebar-menu -->