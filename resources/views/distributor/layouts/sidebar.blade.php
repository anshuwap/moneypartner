<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


    <li class="nav-item">
      <a href="{{ url('distributor/dashboard') }}" class="nav-link {{ (url()->full() == url('distributor/dashboard'))? 'active':''}}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p class="text">Dashboard</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ url('distributor/passbook') }}" class="nav-link {{ (url()->full() == url('distributor/passbook'))? 'active':''}}">
        <i class="fas fa-solid fa-book nav-icon"></i>
        <p>Passbook</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ url('distributor/outlets') }}" class="nav-link {{ (url()->full() == url('distributor/outlets'))? 'active':''}}">
        <i class=" nav-icon fas fa-store"></i>
        <p>Outlets</p>
      </a>
    </li>


    <li class="nav-item">
      <a href="{{ url('distributor/topup-list') }}" class="nav-link {{ (url()->full() == url('distributor/topup-list'))? 'active':''}}">
        <i class="fas fa-wallet nav-icon"></i>
        <p class="text">Topup Request</p>
      </a>
    </li>


    <li class="nav-item">
      <a href="{{ url('distributor/make-topup') }}" class="nav-link {{ (url()->full() == url('distributor/make-topup'))? 'active':''}}">
        <i class="fas fa-wallet nav-icon"></i>
        <p class="text">Make Topup</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ url('distributor/make-transaction') }}" class="nav-link {{ (url()->full() == url('distributor/make-transaction'))? 'active':''}}">
        <!-- <i class="fas fa-solid fa-book nav-icon text-warning"></i> -->
        <i class="fas fa-money-bill-wave nav-icon"></i>
        <p>Make Transaction</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ url('distributor/a-transaction') }}" class="nav-link {{ (url()->full() == url('distributor/a-transaction'))? 'active':''}}">
        <i class="far fa-circle nav-icon"></i>
        <p>Report</p>
      </a>
    </li>



    <li class="nav-item {{ (url()->full() == url('distributor/webhook-api') || url()->full() == url('distributor/pin-password'))?'menu-is-opening menu-open':''}}">
      <a href="javascript:void(0);" class="nav-link ">
        <i class="fas fa-cogs nav-icon"></i>
        <p>Setting
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ url('distributor/webhook-api') }}" class="nav-link {{ (url()->full() == url('distributor/webhook-api'))? 'active':''}}">
            <i class="fas fa-swatchbook  nav-icon"></i>
            <p>Webhook & Api</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ url('distributor/pin-password') }}" class="nav-link {{ (url()->full() == url('distributor/pin-password'))? 'active':''}}">
            <!-- <i class="fas fa-money-check "></i> -->
            <i class="fas fa-key nav-icon"></i>
            <p>
              Pin & Password
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