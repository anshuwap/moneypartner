<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


    <li class="nav-item">
      <a href="{{ url('retailer/dashboard') }}" class="nav-link">
        <i class="nav-icon far fa-circle text-danger"></i>
        <p class="text">Dashboard</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ url('retailer/topup') }}" class="nav-link">
        <i class="nav-icon far fa-circle text-warning"></i>
        <p>Topup List</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="javascript:void(0);" class="nav-link">
        <i class="nav-icon far fa-circle text-info"></i>
        <p>Transaction
        <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ url('retailer/customer-trans') }}" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Customer Transaction</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('retailer/retailer-trans') }}" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>
            Retailer Transaction
            </p>
          </a>
        </li>

      </ul>
    </li>

    <li class="nav-header">MULTI LEVEL EXAMPLE</li>
    <!-- <li class="nav-item">
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