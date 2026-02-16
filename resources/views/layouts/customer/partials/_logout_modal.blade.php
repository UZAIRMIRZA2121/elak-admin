   <li class="__sidebar-hs-unfold px-2" id="tourb-9">
        <div class="hs-unfold w-100">
            <a class="js-hs-unfold-invoker navbar-dropdown-account-wrapper" href="javascript:;"
                data-hs-unfold-options='{
                        "target": "#accountNavbarDropdown_customer",
                        "type": "css-animation"
                    }'>
                <div class="cmn--media right-dropdown-icon d-flex align-items-center">
                    <div class="avatar avatar-sm avatar-circle">
                        <img class="avatar-img"
                             src="{{ asset(auth('customer')->user()->logo) }}"
                             alt="Image"
                             onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'">
                        <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                    </div>
                    <div class="media-body pl-3">
                        <span class="card-title h5">
                            {{auth('customer')->user()->name}}
                        </span>
                        <span class="card-text">{{Str::limit(auth('customer')->user()->email, 15, '...') }}</span>
                    </div>
                </div>
            </a>

            <div id="accountNavbarDropdown_customer"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account min--240">
                            <div class="dropdown-item-text">
                                <div class="media align-items-center">
                                    <div class="avatar avatar-sm avatar-circle mr-2">
                                        <img class="avatar-img"
                                             src="{{ asset(auth('customer')->user()->logo) }}"
                                             alt="Image"
                                             onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'">
                                    </div>
                                    <div class="media-body">
                                        <span class="card-title h5">{{auth('customer')->user()->name}}</span>
                                        <span class="card-text">{{Str::limit(auth('customer')->user()->email, 15, '...') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" href="{{route('customer.settings')}}">
                                <span class="text-truncate pr-2" title="Settings">{{translate('messages.settings')}}</span>
                            </a> 

                            <div class="dropdown-divider"></div>

                           <a class="dropdown-item" href="{{route('customer.auth.logout')}}">
                                <span class="text-truncate pr-2" title="Sign out">{{translate('messages.sign_out')}}</span>
                            </a>
                        </div>
        </div>
    </li>
