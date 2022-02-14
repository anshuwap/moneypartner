<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


    <li class="nav-item">
      <a href="{{ url('employee/dashboard') }}" class="nav-link {{ (url()->full() == url('employee/dashboard'))? 'active':''}}">
        <i class="nav-icon fas fa-tachometer-alt text-danger"></i>
        <p class="text">Dashboard</p>
      </a>
    </li>


    <li class="nav-item">
      <a href="{{ url('employee/topup-list') }}" class="nav-link {{ (url()->full() == url('employee/topup-list'))? 'active':''}}">
        <i class="fas fa-wallet nav-icon text-info"></i>
        <p class="text">Topup Request</p>
      </a>
    </li>

    <li class="nav-item {{ (url()->full() == url('employee/e-customer-trans') || url()->full() == url('employee/e-retailer-trans'))?'menu-is-opening menu-open':''}}">
      <a href="javascript:void(0);" class="nav-link ">
      <i class="fas fa-list-ul nav-icon text-primary"></i>
        <p>Transaction
        <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item {{ (url()->full() == url('employee/e-customer-trans') || url()->full() == url('employee/e-retailer-trans'))?'d-block':''}}">
          <a href="{{ url('employee/e-customer-trans') }}" class="nav-link {{ (url()->full() == url('employee/e-customer-trans'))? 'active':''}}">
            <i class="fas fa-users nav-icon"></i>
            <p>DMT Transaction</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('employee/e-retailer-trans') }}" class="nav-link {{ (url()->full() == url('employee/e-retailer-trans'))? 'active':''}}">
          <i class="fas fa-store-alt nav-icon"></i>
            <p>
            Payout Transaction
            </p>
          </a>
        </li>

      </ul>
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