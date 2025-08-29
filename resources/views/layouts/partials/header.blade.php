<header class="topbar">
    <div class="with-vertical"><!-- ---------------------------------- -->
        <!-- Start Vertical Layout Header -->
        <!-- ---------------------------------- -->
        <nav class="navbar navbar-expand-lg p-0">
            <ul class="navbar-nav">
                <li class="nav-item nav-icon-hover-bg rounded-circle ms-n2">
                    <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
            </ul>

            <div class="d-block d-lg-none py-4">
                <a href="{{ route('dashboard') }}" class="text-nowrap logo-img">
                    <img src="{{ asset('assets') }}/images/logos/logoputih.png" class="dark-logo" alt="Logo-Dark"
                        style="width: 100px;">
                    <img src="{{ asset('assets') }}/images/logos/logohitam.png" class="light-logo" alt="Logo-light"
                        style="width: 100px;">
                </a>
            </div>
            <a class="navbar-toggler nav-icon-hover-bg rounded-circle p-0 mx-0 border-0" href="javascript:void(0)"
                data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="ti ti-dots fs-7"></i>
            </a>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex align-items-center justify-content-between">
                    <a href="javascript:void(0)"
                        class="nav-link nav-icon-hover-bg rounded-circle mx-0 ms-n1 d-flex d-lg-none align-items-center justify-content-center"
                        type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar"
                        aria-controls="offcanvasWithBothOptions">
                        <i class="ti ti-align-justified fs-7"></i>
                    </a>
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">

                        <li class="nav-item nav-icon-hover-bg rounded-circle">
                            <a class="nav-link moon dark-layout" href="javascript:void(0)">
                                <i class="ti ti-moon moon"></i>
                            </a>
                            <a class="nav-link sun light-layout" href="javascript:void(0)">
                                <i class="ti ti-sun sun"></i>
                            </a>
                        </li>

                        <!-- ------------------------------- -->
                        <!-- start notification Dropdown -->
                        <!-- ------------------------------- -->
                        <li class="nav-item nav-icon-hover-bg rounded-circle dropdown">
                            <a class="nav-link position-relative" href="javascript:void(0)" id="drop2"
                                aria-expanded="false">
                                <i class="ti ti-bell-ringing"></i>
                                <div class="notification bg-primary rounded-circle"></div>
                            </a>
                            <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                                aria-labelledby="drop2">
                                <div class="d-flex align-items-center justify-content-between py-3 px-7">
                                    <h5 class="mb-0 fs-5 fw-semibold">Notifications</h5>
                                    <span class="badge text-bg-primary rounded-4 px-3 py-1 lh-sm">
                                        <span id="notificationCount">{{ auth()->user()->unreadNotifications->count() ?? 0 }}</span>
                                        new
                                    </span>
                                </div>
                                <div class="message-body" data-simplebar="">

                                    @foreach (auth()->user()->unreadNotifications as $notification)
                                    @php
                                        $type = $notification->data['data']['notification_type'] ?? '';
                                        $title = $notification->data['data']['title'] ?? '';

                                        $urlType = null;
                                        $urlContent = null;

                                        if (str_contains($title, 'Pre-MPDR')) {
                                            $urlType = 'prempdr';
                                        } elseif (str_contains($title, 'MPDR')) {
                                            $urlType = 'mpdr';
                                        }
                                        if ($type === 'approval_request') {
                                            $urlContent = 'approval';
                                        } elseif ($type === 'rejected' || $type === 'approved') {
                                            $urlContent = 'index';
                                        }

                                        $url = $urlType . '.' . $urlContent;
                                    @endphp
                                    <button type="button"
                                        class="p-3 d-flex align-items-center dropdown-item gap-3 border-bottom mark-as-read"
                                        data-id="{{ $notification->id }}">
                                        <span
                                            class="flex-shrink-0 bg-primary-subtle rounded-circle round-40 d-flex align-items-center justify-content-center fs-6 text-primary">
                                            {{ substr(auth()->user()->where('id', $notification->data['user_id'])->first()->name,0,1) }}
                                        </span>
                                        <div class="w-80">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h6 class="mb-1">{{ $notification->data['data']['title'] }}</h6>
                                                <span
                                                    class="fs-2 d-block text-muted">{{ $notification->created_at->format('H:i') }}</span>
                                            </div>
                                            <span class="fs-2 d-block text-truncate">
                                                {{ substr($notification->data['data']['message'], 0, 40) }}...
                                            </span>
                                        </div>
                                    </button>
                                @endforeach
                                </div>
                                <div class="py-6 px-7 mb-1">
                                    <button class="btn btn-outline-primary w-100 mark-as-read-all">Mark all as read</button>
                                </div>
                            </div>
                        </li>
                        <!-- ------------------------------- -->
                        <!-- end notification Dropdown -->
                        <!-- ------------------------------- -->

                        <!-- ------------------------------- -->
                        <!-- start profile Dropdown -->
                        <!-- ------------------------------- -->
                        <li class="nav-item dropdown">
                            <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" aria-expanded="false">
                                <div class="d-flex align-items-center">
                                    <div class="user-profile-img">
                                        @if (Auth::user()->avatar)
                                            <img src="{{ asset('storage/uploads/user_avatars/' . Auth::user()->avatar) }}"
                                                width="35" height="35" alt="modernize-img" class="rounded-circle">
                                        @else
                                            <img src="{{ Avatar::create(Auth::user()->name)->toBase64() }}"
                                                class="rounded-circle" width="35" height="35" alt="modernize-img">
                                        @endif
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                                aria-labelledby="drop1">
                                <div class="profile-dropdown position-relative" data-simplebar="">
                                    <div class="py-3 px-7 pb-0">
                                        <h5 class="mb-0 fs-5 fw-semibold">User Profile</h5>
                                    </div>
                                    <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                                        @if (Auth::user()->avatar)
                                            <img src="{{ asset('storage/uploads/user_avatars/' . Auth::user()->avatar) }}"
                                                width="80" height="80" alt="modernize-img"
                                                class="rounded-circle">
                                        @else
                                            <img src="{{ Avatar::create(Auth::user()->name)->toBase64() }}"
                                                class="rounded-circle" width="80" height="80"
                                                alt="modernize-img">
                                        @endif
                                        <div class="ms-3">
                                            <h5 class="mb-1 fs-3">{{ Auth::user()->name }}</h5>
                                            <span class="mb-1 d-block">{{ Auth::user()->position->name }}</span>
                                            <p class="mb-0 d-flex align-items-center gap-2">
                                                <i class="ti ti-mail fs-4"></i> {{ Auth::user()->email }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="message-body">
                                        <a href="{{ route('profile.edit') }}"
                                            class="py-8 px-7 mt-8 d-flex align-items-center">
                                            <span
                                                class="d-flex align-items-center justify-content-center text-bg-light rounded-1 p-6">
                                                <img src="{{ asset('assets') }}/images/svgs/icon-account.svg"
                                                    alt="modernize-img" width="24" height="24">
                                            </span>
                                            <div class="w-100 ps-3">
                                                <h6 class="mb-1 fs-3 fw-semibold lh-base">My Profile</h6>
                                                <span class="fs-2 d-block text-body-secondary">Account Settings</span>
                                            </div>
                                        </a>
                                        {{-- <a href="app-email.html" class="py-8 px-7 d-flex align-items-center">
                                            <span
                                                class="d-flex align-items-center justify-content-center text-bg-light rounded-1 p-6">
                                                <img src="../assets/images/svgs/icon-inbox.svg" alt="modernize-img"
                                                    width="24" height="24">
                                            </span>
                                            <div class="w-100 ps-3">
                                                <h6 class="mb-1 fs-3 fw-semibold lh-base">My Inbox</h6>
                                                <span class="fs-2 d-block text-body-secondary">Messages & Emails</span>
                                            </div>
                                        </a>
                                        <a href="app-notes.html" class="py-8 px-7 d-flex align-items-center">
                                            <span
                                                class="d-flex align-items-center justify-content-center text-bg-light rounded-1 p-6">
                                                <img src="../assets/images/svgs/icon-tasks.svg" alt="modernize-img"
                                                    width="24" height="24">
                                            </span>
                                            <div class="w-100 ps-3">
                                                <h6 class="mb-1 fs-3 fw-semibold lh-base">My Task</h6>
                                                <span class="fs-2 d-block text-body-secondary">To-do and Daily
                                                    Tasks</span>
                                            </div>
                                        </a> --}}
                                    </div>
                                    <div class="d-grid py-4 px-7 pt-8">
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                        </form>
                                        <a href="javascript:void(0);" class="btn btn-outline-primary"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log
                                            Out</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <!-- ------------------------------- -->
                        <!-- end profile Dropdown -->
                        <!-- ------------------------------- -->
                    </ul>
                </div>
            </div>
        </nav>
        <!-- ---------------------------------- -->
        <!-- End Vertical Layout Header -->
        <!-- ---------------------------------- -->

        <!-- ------------------------------- -->
        <!-- apps Dropdown in Small screen -->
        <!-- ------------------------------- -->
        <!--  Mobilenavbar -->
        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="mobilenavbar"
            aria-labelledby="offcanvasWithBothOptionsLabel">
            <nav class="sidebar-nav scroll-sidebar">
                <div class="offcanvas-header justify-content-between">
                    <img src="{{asset('assets/images/logos/favicon.ico')}}" alt="modernize-img" class="img-fluid">
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body h-n80" data-simplebar="" data-simplebar="">
                    <ul id="sidebarnav">
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <span>
                                    <i class="ti ti-apps"></i>
                                </span>
                                <span class="hide-menu">Apps</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level my-3">
                                <li class="sidebar-item py-2">
                                    <a href="app-chat.html" class="d-flex align-items-center">
                                        <div
                                            class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                            <img src="{{asset('assets/images/svgs/icon-dd-chat.svg')}}" alt="modernize-img"
                                                class="img-fluid" width="24" height="24">
                                        </div>
                                        <div>
                                            <h6 class="mb-1 bg-hover-primary">Chat Application</h6>
                                            <span class="fs-2 d-block text-muted">New messages arrived</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="sidebar-item py-2">
                                    <a href="app-invoice.html" class="d-flex align-items-center">
                                        <div
                                            class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                            <img src="{{asset('assets/images/svgs/icon-dd-invoice.svg')}}" alt="modernize-img"
                                                class="img-fluid" width="24" height="24">
                                        </div>
                                        <div>
                                            <h6 class="mb-1 bg-hover-primary">Invoice App</h6>
                                            <span class="fs-2 d-block text-muted">Get latest invoice</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="sidebar-item py-2">
                                    <a href="../main/app-cotact.html" class="d-flex align-items-center">
                                        <div
                                            class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                            <img src="{{asset('assets/images/svgs/icon-dd-mobile.svg')}}" alt="modernize-img"
                                                class="img-fluid" width="24" height="24">
                                        </div>
                                        <div>
                                            <h6 class="mb-1 bg-hover-primary">Contact Application</h6>
                                            <span class="fs-2 d-block text-muted">2 Unsaved Contacts</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="sidebar-item py-2">
                                    <a href="app-email.html" class="d-flex align-items-center">
                                        <div
                                            class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                            <img src="{{asset('assets/images/svgs/icon-dd-message-box.svg')}}"
                                                alt="modernize-img" class="img-fluid" width="24" height="24">
                                        </div>
                                        <div>
                                            <h6 class="mb-1 bg-hover-primary">Email App</h6>
                                            <span class="fs-2 d-block text-muted">Get new emails</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="sidebar-item py-2">
                                    <a href="page-user-profile.html" class="d-flex align-items-center">
                                        <div
                                            class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                            <img src="{{asset('assets/images/svgs/icon-dd-cart.svg')}}" alt="modernize-img"
                                                class="img-fluid" width="24" height="24">
                                        </div>
                                        <div>
                                            <h6 class="mb-1 bg-hover-primary">User Profile</h6>
                                            <span class="fs-2 d-block text-muted">learn more information</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="sidebar-item py-2">
                                    <a href="app-calendar.html" class="d-flex align-items-center">
                                        <div
                                            class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                            <img src="{{asset('assets/images/svgs/icon-dd-date.svg')}}" alt="modernize-img"
                                                class="img-fluid" width="24" height="24">
                                        </div>
                                        <div>
                                            <h6 class="mb-1 bg-hover-primary">Calendar App</h6>
                                            <span class="fs-2 d-block text-muted">Get dates</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="sidebar-item py-2">
                                    <a href="app-contact2.html" class="d-flex align-items-center">
                                        <div
                                            class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                            <img src="{{asset('assets/images/svgs/icon-dd-lifebuoy.svg')}}" alt="modernize-img"
                                                class="img-fluid" width="24" height="24">
                                        </div>
                                        <div>
                                            <h6 class="mb-1 bg-hover-primary">Contact List Table</h6>
                                            <span class="fs-2 d-block text-muted">Add new contact</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="sidebar-item py-2">
                                    <a href="app-notes.html" class="d-flex align-items-center">
                                        <div
                                            class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                            <img src="{{asset('assets/images/svgs/icon-dd-application.svg')}}"
                                                alt="modernize-img" class="img-fluid" width="24" height="24">
                                        </div>
                                        <div>
                                            <h6 class="mb-1 bg-hover-primary">Notes Application</h6>
                                            <span class="fs-2 d-block text-muted">To-do and Daily tasks</span>
                                        </div>
                                    </a>
                                </li>
                                <ul class="px-8 mt-7 mb-4">
                                    <li class="sidebar-item mb-3">
                                        <h5 class="fs-5 fw-semibold">Quick Links</h5>
                                    </li>
                                    <li class="sidebar-item py-2">
                                        <a class="fw-semibold text-dark" href="page-pricing.html">Pricing Page</a>
                                    </li>
                                    <li class="sidebar-item py-2">
                                        <a class="fw-semibold text-dark"
                                            href="authentication-login.html">Authentication
                                            Design</a>
                                    </li>
                                    <li class="sidebar-item py-2">
                                        <a class="fw-semibold text-dark" href="authentication-register.html">Register
                                            Now</a>
                                    </li>
                                    <li class="sidebar-item py-2">
                                        <a class="fw-semibold text-dark" href="authentication-error.html">404 Error
                                            Page</a>
                                    </li>
                                    <li class="sidebar-item py-2">
                                        <a class="fw-semibold text-dark" href="app-notes.html">Notes App</a>
                                    </li>
                                    <li class="sidebar-item py-2">
                                        <a class="fw-semibold text-dark" href="page-user-profile.html">User
                                            Application</a>
                                    </li>
                                    <li class="sidebar-item py-2">
                                        <a class="fw-semibold text-dark" href="page-account-settings.html">Account
                                            Settings</a>
                                    </li>
                                </ul>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="app-chat.html" aria-expanded="false">
                                <span>
                                    <i class="ti ti-message-dots"></i>
                                </span>
                                <span class="hide-menu">Chat</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="app-calendar.html" aria-expanded="false">
                                <span>
                                    <i class="ti ti-calendar"></i>
                                </span>
                                <span class="hide-menu">Calendar</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="app-email.html" aria-expanded="false">
                                <span>
                                    <i class="ti ti-mail"></i>
                                </span>
                                <span class="hide-menu">Email</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
    <div class="app-header with-horizontal">
        <nav class="navbar navbar-expand-xl container-fluid p-0">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item nav-icon-hover-bg rounded-circle d-flex d-xl-none ms-n2">
                    <a class="nav-link sidebartoggler" id="sidebarCollapse" href="javascript:void(0)">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-xl-block">
                    <a href="index.html" class="text-nowrap nav-link">
                        <img src="{{asset('assets/images/logos/dark-logo.svg')}}" class="dark-logo" width="180"
                            alt="modernize-img">
                        <img src="{{asset('assets/images/logos/light-logo.svg')}}" class="light-logo" width="180"
                            alt="modernize-img">
                    </a>
                </li>
                <li class="nav-item nav-icon-hover-bg rounded-circle d-none d-xl-flex">
                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal"
                        data-bs-target="#exampleModal">
                        <i class="ti ti-search"></i>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav quick-links d-none d-xl-flex align-items-center">
                <!-- ------------------------------- -->
                <!-- start apps Dropdown -->
                <!-- ------------------------------- -->
                <li class="nav-item nav-icon-hover-bg rounded w-auto dropdown d-none d-lg-flex">
                    <div class="hover-dd">
                        <a class="nav-link" href="javascript:void(0)">
                            Apps<span class="mt-1">
                                <i class="ti ti-chevron-down fs-3"></i>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-nav dropdown-menu-animate-up py-0">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-7 pt-7">
                                        <div class="border-bottom">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="position-relative">
                                                        <a href="app-chat.html"
                                                            class="d-flex align-items-center pb-9 position-relative">
                                                            <div
                                                                class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                                                <img src="{{asset('assets/images/svgs/icon-dd-chat.svg')}}"
                                                                    alt="modernize-img" class="img-fluid"
                                                                    width="24" height="24">
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-1 fw-semibold fs-3">
                                                                    Chat Application
                                                                </h6>
                                                                <span class="fs-2 d-block text-body-secondary">New
                                                                    messages arrived</span>
                                                            </div>
                                                        </a>
                                                        <a href="app-invoice.html"
                                                            class="d-flex align-items-center pb-9 position-relative">
                                                            <div
                                                                class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                                                <img src="{{asset('assets/images/svgs/icon-dd-invoice.svg')}}"
                                                                    alt="modernize-img" class="img-fluid"
                                                                    width="24" height="24">
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-1 fw-semibold fs-3">Invoice App</h6>
                                                                <span class="fs-2 d-block text-body-secondary">Get
                                                                    latest invoice</span>
                                                            </div>
                                                        </a>
                                                        <a href="app-contact2.html"
                                                            class="d-flex align-items-center pb-9 position-relative">
                                                            <div
                                                                class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                                                <img src="{{asset('assets/images/svgs/icon-dd-mobile.svg')}}"
                                                                    alt="modernize-img" class="img-fluid"
                                                                    width="24" height="24">
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-1 fw-semibold fs-3">
                                                                    Contact Application
                                                                </h6>
                                                                <span class="fs-2 d-block text-body-secondary">2
                                                                    Unsaved Contacts</span>
                                                            </div>
                                                        </a>
                                                        <a href="app-email.html"
                                                            class="d-flex align-items-center pb-9 position-relative">
                                                            <div
                                                                class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                                                <img src="{{asset('assets/images/svgs/icon-dd-message-box.svg')}}"
                                                                    alt="modernize-img" class="img-fluid"
                                                                    width="24" height="24">
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-1 fw-semibold fs-3">Email App</h6>
                                                                <span class="fs-2 d-block text-body-secondary">Get new
                                                                    emails</span>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="position-relative">
                                                        <a href="page-user-profile.html"
                                                            class="d-flex align-items-center pb-9 position-relative">
                                                            <div
                                                                class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                                                <img src="{{asset('assets/images/svgs/icon-dd-cart.svg')}}"
                                                                    alt="modernize-img" class="img-fluid"
                                                                    width="24" height="24">
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-1 fw-semibold fs-3">
                                                                    User Profile
                                                                </h6>
                                                                <span class="fs-2 d-block text-body-secondary">learn
                                                                    more information</span>
                                                            </div>
                                                        </a>
                                                        <a href="app-calendar.html"
                                                            class="d-flex align-items-center pb-9 position-relative">
                                                            <div
                                                                class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                                                <img src="{{asset('assets/images/svgs/icon-dd-date.svg')}}"
                                                                    alt="modernize-img" class="img-fluid"
                                                                    width="24" height="24">
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-1 fw-semibold fs-3">
                                                                    Calendar App
                                                                </h6>
                                                                <span class="fs-2 d-block text-body-secondary">Get
                                                                    dates</span>
                                                            </div>
                                                        </a>
                                                        <a href="app-contact.html"
                                                            class="d-flex align-items-center pb-9 position-relative">
                                                            <div
                                                                class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                                                <img src="{{asset('assets/images/svgs/icon-dd-lifebuoy.svg')}}"
                                                                    alt="modernize-img" class="img-fluid"
                                                                    width="24" height="24">
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-1 fw-semibold fs-3">
                                                                    Contact List Table
                                                                </h6>
                                                                <span class="fs-2 d-block text-body-secondary">Add new
                                                                    contact</span>
                                                            </div>
                                                        </a>
                                                        <a href="app-notes.html"
                                                            class="d-flex align-items-center pb-9 position-relative">
                                                            <div
                                                                class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                                                <img src="{{asset('assets/images/svgs/icon-dd-application.svg')}}"
                                                                    alt="modernize-img" class="img-fluid"
                                                                    width="24" height="24">
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-1 fw-semibold fs-3">
                                                                    Notes Application
                                                                </h6>
                                                                <span class="fs-2 d-block text-body-secondary">To-do
                                                                    and Daily tasks</span>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row align-items-center py-3">
                                            <div class="col-8">
                                                <a class="fw-semibold d-flex align-items-center lh-1"
                                                    href="javascript:void(0)">
                                                    <i class="ti ti-help fs-6 me-2"></i>Frequently Asked Questions
                                                </a>
                                            </div>
                                            <div class="col-4">
                                                <div class="d-flex justify-content-end pe-4">
                                                    <button class="btn btn-primary">Check</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 ms-n4">
                                    <div class="position-relative p-7 border-start h-100">
                                        <h5 class="fs-5 mb-9 fw-semibold">Quick Links</h5>
                                        <ul class="">
                                            <li class="mb-3">
                                                <a class="fw-semibold bg-hover-primary"
                                                    href="page-pricing.html">Pricing Page</a>
                                            </li>
                                            <li class="mb-3">
                                                <a class="fw-semibold bg-hover-primary"
                                                    href="authentication-login.html">Authentication
                                                    Design</a>
                                            </li>
                                            <li class="mb-3">
                                                <a class="fw-semibold bg-hover-primary"
                                                    href="authentication-register.html">Register Now</a>
                                            </li>
                                            <li class="mb-3">
                                                <a class="fw-semibold bg-hover-primary"
                                                    href="authentication-error.html">404 Error Page</a>
                                            </li>
                                            <li class="mb-3">
                                                <a class="fw-semibold bg-hover-primary" href="app-notes.html">Notes
                                                    App</a>
                                            </li>
                                            <li class="mb-3">
                                                <a class="fw-semibold bg-hover-primary"
                                                    href="page-user-profile.html">User Application</a>
                                            </li>
                                            <li class="mb-3">
                                                <a class="fw-semibold bg-hover-primary"
                                                    href="page-account-settings.html">Account Settings</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <!-- ------------------------------- -->
                <!-- end apps Dropdown -->
                <!-- ------------------------------- -->
                <li class="nav-item dropdown-hover d-none d-lg-block">
                    <a class="nav-link" href="app-chat.html">Chat</a>
                </li>
                <li class="nav-item dropdown-hover d-none d-lg-block">
                    <a class="nav-link" href="app-calendar.html">Calendar</a>
                </li>
                <li class="nav-item dropdown-hover d-none d-lg-block">
                    <a class="nav-link" href="app-email.html">Email</a>
                </li>
            </ul>
            <div class="d-block d-xl-none">
                <a href="index.html" class="text-nowrap nav-link">
                    <img src="{{asset('assets/images/logos/dark-logo.svg')}}" width="180" alt="modernize-img">
                </a>
            </div>
            <a class="navbar-toggler nav-icon-hover-bg rounded-circle p-0 mx-0 border-0" href="javascript:void(0)"
                data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="p-2">
                    <i class="ti ti-dots fs-7"></i>
                </span>
            </a>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex align-items-center justify-content-between px-0 px-xl-8">
                    <a href="javascript:void(0)"
                        class="nav-link round-40 p-1 ps-0 d-flex d-xl-none align-items-center justify-content-center"
                        type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar"
                        aria-controls="offcanvasWithBothOptions">
                        <i class="ti ti-align-justified fs-7"></i>
                    </a>
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                        <!-- ------------------------------- -->
                        <!-- start language Dropdown -->
                        <!-- ------------------------------- -->
                        <li class="nav-item nav-icon-hover-bg rounded-circle">
                            <a class="nav-link moon dark-layout" href="javascript:void(0)">
                                <i class="ti ti-moon moon"></i>
                            </a>
                            <a class="nav-link sun light-layout" href="javascript:void(0)">
                                <i class="ti ti-sun sun"></i>
                            </a>
                        </li>
                        <li class="nav-item nav-icon-hover-bg rounded-circle dropdown">
                            <a class="nav-link" href="javascript:void(0)" id="drop2" aria-expanded="false">
                                <img src="{{asset('assets/images/svgs/icon-flag-en.svg')}}" alt="modernize-img" width="20px"
                                    height="20px" class="rounded-circle object-fit-cover round-20">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                                aria-labelledby="drop2">
                                <div class="message-body">
                                    <a href="javascript:void(0)"
                                        class="d-flex align-items-center gap-2 py-3 px-4 dropdown-item">
                                        <div class="position-relative">
                                            <img src="{{asset('assets/images/svgs/icon-flag-en.svg')}}" alt="modernize-img"
                                                width="20px" height="20px"
                                                class="rounded-circle object-fit-cover round-20">
                                        </div>
                                        <p class="mb-0 fs-3">English (UK)</p>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="d-flex align-items-center gap-2 py-3 px-4 dropdown-item">
                                        <div class="position-relative">
                                            <img src="{{asset('assets/images/svgs/icon-flag-cn.svg')}}" alt="modernize-img"
                                                width="20px" height="20px"
                                                class="rounded-circle object-fit-cover round-20">
                                        </div>
                                        <p class="mb-0 fs-3">中国人 (Chinese)</p>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="d-flex align-items-center gap-2 py-3 px-4 dropdown-item">
                                        <div class="position-relative">
                                            <img src="{{asset('assets/images/svgs/icon-flag-fr.svg')}}" alt="modernize-img"
                                                width="20px" height="20px"
                                                class="rounded-circle object-fit-cover round-20">
                                        </div>
                                        <p class="mb-0 fs-3">français (French)</p>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="d-flex align-items-center gap-2 py-3 px-4 dropdown-item">
                                        <div class="position-relative">
                                            <img src="{{asset('assets/images/svgs/icon-flag-sa.svg')}}" alt="modernize-img"
                                                width="20px" height="20px"
                                                class="rounded-circle object-fit-cover round-20">
                                        </div>
                                        <p class="mb-0 fs-3">عربي (Arabic)</p>
                                    </a>
                                </div>
                            </div>
                        </li>
                        <!-- ------------------------------- -->
                        <!-- end language Dropdown -->
                        <!-- ------------------------------- -->

                        <!-- ------------------------------- -->
                        <!-- start shopping cart Dropdown -->
                        <!-- ------------------------------- -->
                        <li class="nav-item nav-icon-hover-bg rounded-circle">
                            <a class="nav-link position-relative" href="javascript:void(0)"
                                data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                                aria-controls="offcanvasRight">
                                <i class="ti ti-basket"></i>
                                <span class="popup-badge rounded-pill bg-danger text-white fs-2">2</span>
                            </a>
                        </li>
                        <!-- ------------------------------- -->
                        <!-- end shopping cart Dropdown -->
                        <!-- ------------------------------- -->

                        <!-- ------------------------------- -->
                        <!-- start notification Dropdown -->
                        <!-- ------------------------------- -->
                        <li class="nav-item nav-icon-hover-bg rounded-circle dropdown">
                            <a class="nav-link position-relative" href="javascript:void(0)" id="drop2"
                                aria-expanded="false">
                                <i class="ti ti-bell-ringing"></i>
                                <div class="notification bg-primary rounded-circle"></div>
                            </a>
                            <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                                aria-labelledby="drop2">
                                <div class="d-flex align-items-center justify-content-between py-3 px-7">
                                    <h5 class="mb-0 fs-5 fw-semibold">Notifications</h5>
                                    <span class="badge text-bg-primary rounded-4 px-3 py-1 lh-sm">5 new</span>
                                </div>
                                <div class="message-body" data-simplebar="">
                                    <a href="javascript:void(0)"
                                        class="py-6 px-7 d-flex align-items-center dropdown-item">
                                        <span class="me-3">
                                            <img src="{{asset('assets/images/profile/user-2.jpg')}}" alt="user"
                                                class="rounded-circle" width="48" height="48">
                                        </span>
                                        <div class="w-100">
                                            <h6 class="mb-1 fw-semibold lh-base">Roman Joined the Team!</h6>
                                            <span class="fs-2 d-block text-body-secondary">Congratulate him</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="py-6 px-7 d-flex align-items-center dropdown-item">
                                        <span class="me-3">
                                            <img src="{{asset('assets/images/profile/user-3.jpg')}}" alt="user"
                                                class="rounded-circle" width="48" height="48">
                                        </span>
                                        <div class="w-100">
                                            <h6 class="mb-1 fw-semibold lh-base">New message</h6>
                                            <span class="fs-2 d-block text-body-secondary">Salma sent you new
                                                message</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="py-6 px-7 d-flex align-items-center dropdown-item">
                                        <span class="me-3">
                                            <img src="{{asset('assets/images/profile/user-4.jpg')}}" alt="user"
                                                class="rounded-circle" width="48" height="48">
                                        </span>
                                        <div class="w-100">
                                            <h6 class="mb-1 fw-semibold lh-base">Bianca sent payment</h6>
                                            <span class="fs-2 d-block text-body-secondary">Check your earnings</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="py-6 px-7 d-flex align-items-center dropdown-item">
                                        <span class="me-3">
                                            <img src="{{asset('assets/images/profile/user-5.jpg')}}" alt="user"
                                                class="rounded-circle" width="48" height="48">
                                        </span>
                                        <div class="w-100">
                                            <h6 class="mb-1 fw-semibold lh-base">Jolly completed tasks</h6>
                                            <span class="fs-2 d-block text-body-secondary">Assign her new tasks</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="py-6 px-7 d-flex align-items-center dropdown-item">
                                        <span class="me-3">
                                            <img src="{{asset('assets/images/profile/user-6.jpg')}}" alt="user"
                                                class="rounded-circle" width="48" height="48">
                                        </span>
                                        <div class="w-100">
                                            <h6 class="mb-1 fw-semibold lh-base">John received payment</h6>
                                            <span class="fs-2 d-block text-body-secondary">$230 deducted from
                                                account</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="py-6 px-7 d-flex align-items-center dropdown-item">
                                        <span class="me-3">
                                            <img src="{{asset('assets/images/profile/user-7.jpg')}}" alt="user"
                                                class="rounded-circle" width="48" height="48">
                                        </span>
                                        <div class="w-100">
                                            <h6 class="mb-1 fw-semibold lh-base">Roman Joined the Team!</h6>
                                            <span class="fs-2 d-block text-body-secondary">Congratulate him</span>
                                        </div>
                                    </a>
                                </div>
                                <div class="py-6 px-7 mb-1">
                                    <button class="btn btn-outline-primary w-100">See All Notifications</button>
                                </div>
                            </div>
                        </li>
                        <!-- ------------------------------- -->
                        <!-- end notification Dropdown -->
                        <!-- ------------------------------- -->

                        <!-- ------------------------------- -->
                        <!-- start profile Dropdown -->
                        <!-- ------------------------------- -->
                        <li class="nav-item dropdown">
                            <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" aria-expanded="false">
                                <div class="d-flex align-items-center">
                                    <div class="user-profile-img">
                                        @if (Auth::user()->avatar)
                                            <img src="{{ asset('storage/uploads/user_avatars/' . Auth::user()->avatar) }}"
                                                width="35" height="35" alt="modernize-img" class="rounded-circle">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random"
                                                class="rounded-circle" width="35" height="35" alt="modernize-img">
                                        @endif
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                                aria-labelledby="drop1">
                                <div class="profile-dropdown position-relative" data-simplebar="">
                                    <div class="py-3 px-7 pb-0">
                                        <h5 class="mb-0 fs-5 fw-semibold">User Profile</h5>
                                    </div>
                                    <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                                        @if (Auth::user()->avatar)
                                            <img src="{{ asset('storage/uploads/user_avatars/' . Auth::user()->avatar) }}"
                                                width="80" height="80" alt="modernize-img"
                                                class="rounded-circle">
                                        @else
                                            <img src="{{ Avatar::create(Auth::user()->name)->toBase64() }}"
                                                class="rounded-circle" width="80" height="80"
                                                alt="modernize-img">
                                        @endif
                                        <div class="ms-3">
                                            <h5 class="mb-1 fs-3">{{ Auth::user()->name }}</h5>
                                            <span class="mb-1 d-block">{{ Auth::user()->position->name }}</span>
                                            <p class="mb-0 d-flex align-items-center gap-2">
                                                <i class="ti ti-mail fs-4"></i> {{ Auth::user()->email }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="message-body">
                                        <a href="{{ route('profile.edit') }}"
                                            class="py-8 px-7 mt-8 d-flex align-items-center">
                                            <span
                                                class="d-flex align-items-center justify-content-center text-bg-light rounded-1 p-6">
                                                <img src="{{ asset('assets') }}/images/svgs/icon-account.svg"
                                                    alt="modernize-img" width="24" height="24">
                                            </span>
                                            <div class="w-100 ps-3">
                                                <h6 class="mb-1 fs-3 fw-semibold lh-base">My Profile</h6>
                                                <span class="fs-2 d-block text-body-secondary">Account Settings</span>
                                            </div>
                                        </a>
                                        <a href="app-email.html" class="py-8 px-7 d-flex align-items-center">
                                            <span
                                                class="d-flex align-items-center justify-content-center text-bg-light rounded-1 p-6">
                                                <img src="{{ asset('assets') }}/images/svgs/icon-inbox.svg" alt="modernize-img"
                                                    width="24" height="24">
                                            </span>
                                            <div class="w-100 ps-3">
                                                <h6 class="mb-1 fs-3 fw-semibold lh-base">My Inbox</h6>
                                                <span class="fs-2 d-block text-body-secondary">Messages & Emails</span>
                                            </div>
                                        </a>
                                        <a href="app-notes.html" class="py-8 px-7 d-flex align-items-center">
                                            <span
                                                class="d-flex align-items-center justify-content-center text-bg-light rounded-1 p-6">
                                                <img src="{{ asset('assets') }}/images/svgs/icon-tasks.svg" alt="modernize-img"
                                                    width="24" height="24">
                                            </span>
                                            <div class="w-100 ps-3">
                                                <h6 class="mb-1 fs-3 fw-semibold lh-base">My Task</h6>
                                                <span class="fs-2 d-block text-body-secondary">To-do and Daily
                                                    Tasks</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="d-grid py-4 px-7 pt-8">
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                        </form>
                                        <a href="javascript:void(0);" class="btn btn-outline-primary"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log
                                            Out</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <!-- ------------------------------- -->
                        <!-- end profile Dropdown -->
                        <!-- ------------------------------- -->
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const htmlElement = document.documentElement;
            const darkModeStorage = localStorage.getItem('darkModes');

            // Function to set dark mode
            const setDarkMode = (darkModeOn) => {
                htmlElement.setAttribute('data-bs-theme', darkModeOn ? 'light' : 'dark');
                localStorage.setItem('darkModes', darkModeOn ? 'disabled' : 'enabled');
            };

            // Initialize dark mode based on stored preference
            if (darkModeStorage === 'enabled') {
                setDarkMode(false);
            } else {
                setDarkMode(true);
            }

            // Event listeners for mode icons
            const moonIcon = document.querySelector('.moon');
            const sunIcon = document.querySelector('.sun');

            if (moonIcon && sunIcon) {
                moonIcon.addEventListener('click', function() {
                    setDarkMode(false);
                });

                sunIcon.addEventListener('click', function() {
                    setDarkMode(true);
                });
            } else {
                console.error('Moon or Sun icon not found!');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk memperbarui jumlah notifikasi
            function updateNotificationCount() {
                fetch('{{ route('notifications.count') }}')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('notificationCount').textContent = data.count;
                    })
                    .catch(error => console.error('Error:', error));
            }

            const messageBodies = document.querySelectorAll('.message-body.notif');


            messageBodies.forEach(messageBody => {
                messageBody.addEventListener('click', function(event) {
                    const target = event.target.closest('.mark-as-read');
                    if (target) {
                        const notificationId = target.getAttribute('data-id');
                        const redirectUrl = target.getAttribute('data-url');

                        fetch('{{ route('notifications.markAsRead') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    notification_id: notificationId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    target.remove();
                                    const notificationCountElement = document.getElementById(
                                        'notificationCount');
                                    let currentCount = parseInt(notificationCountElement
                                        .textContent);
                                    notificationCountElement.textContent = currentCount - 1;

                                    // Hanya redirect jika ada data-url
                                    if (redirectUrl) {
                                        window.location.href = redirectUrl;
                                    }
                                } else {
                                    alert('Failed to mark notification as read.');
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }
                });
            });

            // Dapatkan semua elemen dengan kelas .mark-as-read-all
            const markAsReadAllButtons = document.querySelectorAll('.mark-as-read-all');

            // Tambahkan event listener ke setiap elemen
            markAsReadAllButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    // Mencegah event bubbling yang mungkin menutup dropdown
                    event.stopPropagation();

                    // Logika untuk menandai semua notifikasi sebagai dibaca
                    fetch('{{ route('notifications.markAllAsRead') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Hapus semua elemen notifikasi dari DOM
                                document.querySelectorAll(
                                    '.message-body.notif button').forEach(
                                    button => button.remove());

                                // Update jumlah notifikasi
                                const notificationCountElement = document
                                    .getElementById('notificationCount');
                                notificationCountElement.textContent =
                                    0; // Set jumlah notifikasi menjadi 0

                                // Tampilkan pesan sukses tanpa alert
                                const successMessage = document.createElement(
                                    'div');
                                successMessage.textContent =
                                    'All notifications have been marked as read.';
                                successMessage.classList.add('alert',
                                    'alert-success');
                                document.body.appendChild(successMessage);
                                setTimeout(() => successMessage.remove(),
                                    3000); // Hapus pesan setelah 3 detik
                            } else {
                                alert('Failed to mark all notifications as read.');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
</header>
