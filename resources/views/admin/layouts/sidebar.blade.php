<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


    <li class="nav-item">
      <a href="{{ url('admin/dashboard') }}" class="nav-link">
        <i class="nav-icon far fa-circle text-danger"></i>
        <p class="text">Dashboard</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ url('admin/outlets') }}" class="nav-link">
        <i class="nav-icon far fa-circle text-warning"></i>
        <p>Outlets</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="javascript:void(0);" class="nav-link">
        <i class="nav-icon far fa-circle text-info"></i>
        <p>Payment Mode
        <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ url('admin/bank-account') }}" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Bank Account</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('admin/upi') }}" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>
              UPI
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('admin/qr-code') }}" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>QR Code</p>
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