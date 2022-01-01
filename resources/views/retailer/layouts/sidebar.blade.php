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
      <a href="{{ url('retailer/topup') }}" class="nav-link {{ (url()->full() == url('retailer/topup'))? 'active':''}}">
      <i class="fas fa-wallet nav-icon text-info"></i>
        <p>Topup List</p>
      </a>
    </li>

     <li class="nav-item {{ (url()->full() == url('retailer/customer-trans') || url()->full() == url('retailer/retailer-trans'))?'menu-is-opening menu-open':''}}">
      <a href="javascript:void(0);" class="nav-link ">
      <i class="fas fa-list-ul nav-icon text-primary"></i>
        <p>Transaction
        <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item {{ (url()->full() == url('retailer/customer-trans') || url()->full() == url('retailer/retailer-trans'))?'d-block':''}}">
          <a href="{{ url('retailer/customer-trans') }}" class="nav-link {{ (url()->full() == url('retailer/customer-trans'))? 'active':''}}">
            <i class="fas fa-users nav-icon"></i>
            <p>Customer Transaction</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('retailer/retailer-trans') }}" class="nav-link {{ (url()->full() == url('retailer/retailer-trans'))? 'active':''}}">
          <i class="fas fa-store-alt nav-icon"></i>
            <p>
            Retailer Transaction
            </p>
          </a>
        </li>

      </ul>
    </li>


  </ul>
</nav>
<!-- /.sidebar-menu -->