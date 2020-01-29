<div class="navbar-custom-menu">
    <ul class="nav navbar-nav">
        <!-- Messages: style can be found in dropdown.less-->
        <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-envelope-o"></i>
                <span class="label label-success">{{App\Models\Contact::where('seen', 0)->count()}}</span>
            </a>
            <ul class="dropdown-menu">

                <li class="header">You have {{App\Models\Contact::where('seen', 0)->count()}} messages</li>
                <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu">
                        @foreach(App\Models\Contact::take(3)->orderBy('created_at','DESC')->get() as $contact)
                            <li><!-- start message -->
                                <a href="{{route('manage.single',['id'=>$contact->id])}}">
                                    <div class="pull-left">
                                        <img src="{{asset('uploads/publishers/'.$contact->publisher->image)}}"
                                             class="img-circle" alt="User Image">
                                    </div>
                                    <h4>
                                      {{$contact->contact_number}}
                                        <small><i class="fa fa-clock-o"></i> {{$contact->created_at->diffForHumans()}}</small>
                                    </h4>
                                    <h5>{{$contact->email}}</h5>
                                    <p>{{$contact->subject}}</p>
                                    <p>{{$contact->desc}}</p>
                                </a>
                            </li>
                    @endforeach
                    <!-- end message -->

                    </ul>
                <li class="footer"><a href="{{route('manage.messages')}}">See All Messages</a></li>
            </ul>
        </li>
        <!-- Notifications: style can be found in dropdown.less -->
{{--        <li class="dropdown notifications-menu">--}}
{{--            <a href="#" class="dropdown-toggle" data-toggle="dropdown">--}}
{{--                <i class="fa fa-bell-o"></i>--}}
{{--                <span class="label label-warning">10</span>--}}
{{--            </a>--}}
{{--            <ul class="dropdown-menu">--}}
{{--                <li class="header">You have 10 notifications</li>--}}
{{--                <li>--}}
{{--                    <!-- inner menu: contains the actual data -->--}}
{{--                    <ul class="menu">--}}
{{--                        <li>--}}
{{--                            <a href="#">--}}
{{--                                <i class="fa fa-users text-aqua"></i> 5 new members joined today--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <a href="#">--}}
{{--                                <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit--}}
{{--                                into the--}}
{{--                                page and may cause design problems--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <a href="#">--}}
{{--                                <i class="fa fa-users text-red"></i> 5 new members joined--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <a href="#">--}}
{{--                                <i class="fa fa-shopping-cart text-green"></i> 25 sales made--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <a href="#">--}}
{{--                                <i class="fa fa-user text-red"></i> You changed your username--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </li>--}}
{{--                <li class="footer"><a href="#">View all</a></li>--}}
{{--            </ul>--}}
{{--        </li>--}}
        <!-- Tasks: style can be found in dropdown.less -->
{{--        <li class="dropdown tasks-menu">--}}
{{--            <a href="#" class="dropdown-toggle" data-toggle="dropdown">--}}
{{--                <i class="fa fa-flag-o"></i>--}}
{{--                <span class="label label-danger">9</span>--}}
{{--            </a>--}}
{{--            <ul class="dropdown-menu">--}}
{{--                <li class="header">You have 9 tasks</li>--}}
{{--                <li>--}}
{{--                    <!-- inner menu: contains the actual data -->--}}
{{--                    <ul class="menu">--}}
{{--                        <li><!-- Task item -->--}}
{{--                            <a href="#">--}}
{{--                                <h3>--}}
{{--                                    Design some buttons--}}
{{--                                    <small class="pull-right">20%</small>--}}
{{--                                </h3>--}}
{{--                                <div class="progress xs">--}}
{{--                                    <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar"--}}
{{--                                         aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">--}}
{{--                                        <span class="sr-only">20% Complete</span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <!-- end task item -->--}}
{{--                        <li><!-- Task item -->--}}
{{--                            <a href="#">--}}
{{--                                <h3>--}}
{{--                                    Create a nice theme--}}
{{--                                    <small class="pull-right">40%</small>--}}
{{--                                </h3>--}}
{{--                                <div class="progress xs">--}}
{{--                                    <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar"--}}
{{--                                         aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">--}}
{{--                                        <span class="sr-only">40% Complete</span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <!-- end task item -->--}}
{{--                        <li><!-- Task item -->--}}
{{--                            <a href="#">--}}
{{--                                <h3>--}}
{{--                                    Some task I need to do--}}
{{--                                    <small class="pull-right">60%</small>--}}
{{--                                </h3>--}}
{{--                                <div class="progress xs">--}}
{{--                                    <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar"--}}
{{--                                         aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">--}}
{{--                                        <span class="sr-only">60% Complete</span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <!-- end task item -->--}}
{{--                        <li><!-- Task item -->--}}
{{--                            <a href="#">--}}
{{--                                <h3>--}}
{{--                                    Make beautiful transitions--}}
{{--                                    <small class="pull-right">80%</small>--}}
{{--                                </h3>--}}
{{--                                <div class="progress xs">--}}
{{--                                    <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar"--}}
{{--                                         aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">--}}
{{--                                        <span class="sr-only">80% Complete</span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <!-- end task item -->--}}
{{--                    </ul>--}}
{{--                </li>--}}
{{--                <li class="footer">--}}
{{--                    <a href="#">View all tasks</a>--}}
{{--                </li>--}}
{{--            </ul>--}}
{{--        </li>--}}
        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="{{url('/design/adminlte')}}/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                <span class="hidden-xs"></span>
            </a>
            <ul class="dropdown-menu">
                <!-- User image -->
                <li class="user-header">
                    <img src="{{url('/design/adminlte')}}/dist/img/user2-160x160.jpg" class="img-circle"
                         alt="User Image">

                    <p>
                        Alexander Pierce - Web Developer
                        <small>Member since Nov. 2012</small>
                    </p>
                </li>
                <!-- Menu Body -->
                <li class="user-body">
                    <div class="row">
                        <div class="col-xs-4 text-center">
                            <a href="#">Followers</a>
                        </div>
                        <div class="col-xs-4 text-center">
                            <a href="#">Sales</a>
                        </div>
                        <div class="col-xs-4 text-center">
                            <a href="#">Friends</a>
                        </div>
                    </div>
                    <!-- /.row -->
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                    <div class="pull-left">
                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                    </div>
                    <div class="pull-right">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-default btn-flat">Sign out</button>
                        </form>
                    </div>
                </li>
            </ul>
        </li>
        <!-- Control Sidebar Toggle Button -->
        <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
        </li>
    </ul>
</div>
