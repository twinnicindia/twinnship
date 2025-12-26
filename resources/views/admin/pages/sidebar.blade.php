<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    @php
        $config = DB::table('configuration')->first();
    @endphp
    <a href="{{route('administrator.dashboard')}}" class="brand-link">
        <img src="{{asset($config->favicon ?? '')}}"
             alt="{{env('appTitle')}}"
             class="brand-image img-circle elevation-3"
             style="opacity: 1">
        <span class="brand-text font-weight-light">{{env('appTitle')}}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img style="height: 34px;" title="{{Session()->get('MyAdmin')->name}}" src="{{asset(Session()->get('MyAdmin')->image)}}" class="img-circle elevation-2" alt="{{Session()->get('MyAdmin')->name}}">
            </div>
            <div class="info">
                <a href="{{route('administrator.profile')}}" class="d-block">{{Session()->get('MyAdmin')->name}}</a>
            </div>
        </div>
        <?php
        $url=basename($_SERVER['REQUEST_URI']);
        ?>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                @if(Session()->get('MyAdmin')->email == 'admin@twinnship.com')
                <li class="nav-item">
                    <a href="{{route('administrator.dashboard')}}" class="nav-link <?php echo $url=="administrator-dashboard"?"active":""; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @endif
                <?php foreach ($menus['menu'] as $m) {
                    $flag=false;
                    foreach($menus['submenu'][$m->id] as $s){
                        if(request()->is($s->link)) $flag=true;
                    } ?>
                <li class="nav-item <?php echo $flag==true?"menu-open":""; ?> <?php if(count($menus['submenu'][$m->id])!=0) echo "has-treeview"; ?>">
                    <a href="{{url('/')."/".$m->link}}" class="nav-link <?php echo request()->is($m->link)?"active":""; ?>">
                        <i class="<?php echo $m->icon != ""?$m->icon." nav-icon":"far fa-circle nav-icon"; ?>"></i>
                        <p>{{$m->title}}</p>
                        <?php if(count($menus['submenu'][$m->id])!=0) { ?> <i class="right fas fa-angle-left"></i> <?php } ?>
                    </a>
                    <?php if(count($menus['submenu'][$m->id])!=0) { ?>
                    <ul class="nav nav-treeview">
                        <?php foreach ($menus['submenu'][$m->id] as $s) { ?>
                        <li class="nav-item">
                            <a href="<?php echo url('/')."/".$s->link; ?>" class="nav-link <?php echo request()->is($s->link)?"active":""; ?> ">
                                <i class="far fa-circle nav-icon"></i>
                                <p> <?php echo $s->title; ?></p>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                </li>
               <?php } ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
