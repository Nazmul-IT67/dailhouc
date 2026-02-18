<!-- Page Sidebar Start -->
<div class="sidebar-wrapper" data-layout="stroke-svg">
    <div class="logo-wrapper">
        <a href="index.html"><img class="img-fluid" src="{{ asset($setting->logo) }}" alt=""></a>
        <div class="back-btn"><i class="fa fa-angle-left"></i></div>
        <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"></i></div>
    </div>
    <div class="logo-icon-wrapper">
        <a href="index.html"><img class="img-fluid" src="../assets/images/logo/logo-icon.png" alt=""></a>
    </div>

    <nav class="sidebar-main">
        <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
        <div id="sidebar-menu">
            <ul class="sidebar-links" id="simple-bar">
                <li class="back-btn">
                    <a href="index.html"><img class="img-fluid" src="../assets/images/logo/logo-icon.png"
                            alt=""></a>
                    <div class="mobile-back text-end">
                        <span>Back</span>
                        <i class="fa fa-angle-right ps-2" aria-hidden="true"></i>
                    </div>
                </li>
                <li class="pin-title sidebar-main-title">
                    <div>
                        <h6>Pinned</h6>
                    </div>
                </li>
                <li class="sidebar-main-title">
                    <div>
                        <h6 class="lan-1">General</h6>
                    </div>
                </li>
                <li class="sidebar-list">
                    <i class="fa fa-thumb-tack"></i>
                    <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                            fill="none" stroke="#ffffff" stroke-width="1" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                            <path d="M10 12h4v4h-4z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-list">
                    <i class="fa fa-thumb-tack"></i>
                    <a class="sidebar-link sidebar-title
                        {{ request()->routeIs('admin.system.*') || request()->routeIs('admin.profile.*') || request()->routeIs('admin.social.*') ? 'active' : '' }}"
                        href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                            fill="none" stroke="#ffffff" stroke-width="1" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                            <path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                            <path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                            <path d="M14 7l6 0" />
                            <path d="M17 4l0 6" />
                        </svg>
                        <span>Settings</span>
                    </a>
                    <ul
                        class="sidebar-submenu {{ request()->routeIs('admin.system.*') || request()->routeIs('admin.profile.*') || request()->routeIs('admin.social.*') || request()->routeIs('admin.dynamic_page.*') ? 'd-block' : '' }}">
                        <li><a class="{{ request()->routeIs('admin.system.*') ? 'active' : '' }}"
                                href="{{ route('admin.system.index') }}">System Settings</a></li>
                        <li><a class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"
                                href="{{ route('admin.profile.setting') }}">Profile Setting</a></li>
                        <li><a class="{{ request()->routeIs('admin.social.*') ? 'active' : '' }}"
                                href="{{ route('admin.social.index') }}">Social Setting</a></li>
                        <li><a class="{{ request()->routeIs('admin.dynamic_page.*') ? 'active' : '' }}"
                                href="{{ route('admin.dynamic_page.index') }}">Dynamic Page</a></li>
                    </ul>
                </li>
                <li class="sidebar-main-title">
                    <div>
                        <h6 class="lan-8">Applications</h6>
                    </div>
                </li>
                <li class="sidebar-list">
                    <i class="fa fa-thumb-tack"></i>
                    <a class="sidebar-link sidebar-title
                        {{ request()->routeIs('admin.currencies.*') || request()->routeIs('admin.countries.*') || request()->routeIs('admin.cities.*') ? 'active' : '' }}"
                        href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                            fill="none" stroke="#ffffff" stroke-width="1" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                            <path
                                d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                        </svg>
                        <span>Country</span>
                    </a>
                    <ul
                        class="sidebar-submenu {{ request()->routeIs('admin.currencies.*') || request()->routeIs('admin.countries.*') || request()->routeIs('admin.cities.*') ? 'd-block' : '' }}">
                        <li><a class="{{ request()->routeIs('admin.currencies.*') ? 'active' : '' }}"
                                href="{{ route('admin.currencies.index') }}">Currencies</a></li>
                        <li><a class="{{ request()->routeIs('admin.countries.*') ? 'active' : '' }}"
                                href="{{ route('admin.countries.index') }}">Countries</a></li>
                        <li><a class="{{ request()->routeIs('admin.cities.*') ? 'active' : '' }}"
                                href="{{ route('admin.cities.index') }}">Cities</a></li>
                    </ul>
                </li>
                <li class="sidebar-list">
                    <i class="fa fa-thumb-tack"></i>
                    <a class="sidebar-link sidebar-title
        {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}"
                        href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                            fill="none" stroke="#ffffff" stroke-width="1" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M3 13h18v6H3z" /> <!-- car body -->
                            <circle cx="6.5" cy="19" r="1.5" /> <!-- left wheel -->
                            <circle cx="17.5" cy="19" r="1.5" /> <!-- right wheel -->
                            <path d="M3 13l3-6h12l3 6" /> <!-- roof -->
                        </svg>
                        <span>Vehicle List</span>
                    </a>

                    <ul class="sidebar-submenu {{ request()->routeIs('admin.vehicles.*') ? 'd-block' : '' }}">
                        <li>
                            <a class="{{ request()->routeIs('admin.vehicles.index') ? 'active' : '' }}"
                                href="{{ route('admin.vehicles.index') }}">
                                All List
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Vehicle --}}
                <li class="sidebar-list">
                    <i class="fa fa-thumb-tack"></i>
                    <a class="sidebar-link sidebar-title {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.brands.*') || request()->routeIs('admin.car_models.*') || request()->routeIs('admin.sub_models.*') || request()->routeIs('admin.fuels.*') || request()->routeIs('admin.equipment.*') || request()->routeIs('admin.body_types.*') || request()->routeIs('admin.powers.*') || request()->routeIs('admin.equipment_lines.*') || request()->routeIs('admin.model_years.*') || request()->routeIs('admin.seller_types.*') ? 'active' : '' }}"
                        href="#">

                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                            fill="none" stroke="#ffffff" stroke-width="1" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M3 13h2l3 8h8l3-8h2" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <span>Vehicles</span>
                    </a>

                    <ul
                        class="sidebar-submenu {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.brands.*') || request()->routeIs('admin.car_models.*') || request()->routeIs('admin.sub_models.*') || request()->routeIs('admin.fuels.*') || request()->routeIs('admin.equipment.*') || request()->routeIs('admin.body_types.*') || request()->routeIs('admin.powers.*') || request()->routeIs('admin.equipment_lines.*') || request()->routeIs('admin.model_years.*') || request()->routeIs('admin.seller_types.*') ? 'd-block' : '' }}">
                        <li>
                            <a class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                                href="{{ route('admin.categories.index') }}">
                                Categories
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"
                                href="{{ route('admin.brands.index') }}">
                                Brands
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.car_models.*') ? 'active' : '' }}"
                                href="{{ route('admin.car_models.index') }}">
                                Models
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.sub_models.*') ? 'active' : '' }}"
                                href="{{ route('admin.sub_models.index') }}">
                                Sub Models
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.fuels.*') ? 'active' : '' }}"
                                href="{{ route('admin.fuels.index') }}">
                                Fuel Types
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.equipment.*') ? 'active' : '' }}"
                                href="{{ route('admin.equipment.index') }}">
                                Equipment
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.body_types.*') ? 'active' : '' }}"
                                href="{{ route('admin.body_types.index') }}">
                                Body Types
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.powers.*') ? 'active' : '' }}"
                                href="{{ route('admin.powers.index') }}">
                                Powers
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.equipment_lines.*') ? 'active' : '' }}"
                                href="{{ route('admin.equipment_lines.index') }}">
                                Equipment Lines
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.seller_types.*') ? 'active' : '' }}"
                                href="{{ route('admin.seller_types.index') }}">
                                Seller Types
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.model_years.*') ? 'active' : '' }}"
                                href="{{ route('admin.model_years.index') }}">
                                Model Years
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- Vehicle --}}
                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title {{ request()->routeIs(
                        'admin.vehicle_conditions.*',
                        'admin.body_colors.*',
                        'admin.upholsteries.*',
                        'admin.interior_colors.*',
                        'admin.previous_owners.*',
                        'admin.number_of_doors.*',
                        'admin.number_of_seats.*',
                        'admin.bed_counts.*',
                        'admin.bed_types.*',
                    )
                        ? 'active'
                        : '' }}"
                        href="#">
                        <!-- Updated car icon SVG -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                            fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M3 13h18v6H3z" /> <!-- car body -->
                            <circle cx="6.5" cy="19" r="1.5" /> <!-- left wheel -->
                            <circle cx="17.5" cy="19" r="1.5" /> <!-- right wheel -->
                            <path d="M3 13l3-6h12l3 6" /> <!-- roof -->
                        </svg>
                        <span>Vehicle details</span>
                    </a>

                    <ul
                        class="sidebar-submenu {{ request()->routeIs(
                            'admin.vehicle_conditions.*',
                            'admin.body_colors.*',
                            'admin.upholsteries.*',
                            'admin.interior_colors.*',
                            'admin.previous_owners.*',
                            'admin.number_of_doors.*',
                            'admin.number_of_seats.*',
                            'admin.bed_counts.*',
                            'admin.bed_types.*',
                        )
                            ? 'd-block'
                            : '' }}">
                        <li>
                            <a class="{{ request()->routeIs('admin.vehicle_conditions.index') ? 'active' : '' }}"
                                href="{{ route('admin.vehicle_conditions.index') }}">Vehicle Conditions</a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.body_colors.index') ? 'active' : '' }}"
                                href="{{ route('admin.body_colors.index') }}">Body Colors</a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.upholsteries.index') ? 'active' : '' }}"
                                href="{{ route('admin.upholsteries.index') }}">Upholstery</a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.interior_colors.index') ? 'active' : '' }}"
                                href="{{ route('admin.interior_colors.index') }}">Interior Colors</a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.previous_owners.index') ? 'active' : '' }}"
                                href="{{ route('admin.previous_owners.index') }}">Number Of Previous Owner</a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.number_of_doors.index') ? 'active' : '' }}"
                                href="{{ route('admin.number_of_doors.index') }}">Number Of Doors</a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.number_of_seats.index') ? 'active' : '' }}"
                                href="{{ route('admin.number_of_seats.index') }}">Number Of Seats</a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.bed_counts.index') ? 'active' : '' }}"
                                href="{{ route('admin.bed_counts.index') }}">Bed Counts</a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.bed_types.index') ? 'active' : '' }}"
                                href="{{ route('admin.bed_types.index') }}">Bed Types</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title {{ request()->routeIs('admin.driver_types.*', 'admin.transmissions.*', 'admin.num_of_gears.*', 'admin.cylinders.*', 'admin.emission_classes.*', 'admin.axle-counts.*') ? 'active' : '' }}"
                        href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                            fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M3 10h18v4H3z" />
                            <path d="M7 6v4" />
                            <path d="M17 6v4" />
                            <path d="M7 14v4" />
                            <path d="M17 14v4" />
                        </svg>
                        <span>Engine & Environment</span>
                    </a>

                    <ul
                        class="sidebar-submenu {{ request()->routeIs('admin.driver_types.*', 'admin.transmissions.*', 'admin.num_of_gears.*', 'admin.cylinders.*', 'admin.emission_classes.*', 'admin.axle-counts.*') ? 'd-block' : '' }}">
                        <li>
                            <a class="{{ request()->routeIs('admin.driver_types.index') ? 'active' : '' }}"
                                href="{{ route('admin.driver_types.index') }}">
                                Driver Types
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.transmissions.index') ? 'active' : '' }}"
                                href="{{ route('admin.transmissions.index') }}">
                                Transmission
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.num_of_gears.index') ? 'active' : '' }}"
                                href="{{ route('admin.num_of_gears.index') }}">
                                Number of Gears
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.cylinders.index') ? 'active' : '' }}"
                                href="{{ route('admin.cylinders.index') }}">
                                Cylinders
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.emission_classes.index') ? 'active' : '' }}"
                                href="{{ route('admin.emission_classes.index') }}">
                                Emission Classes
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.axle-counts.index') ? 'active' : '' }}"
                                href="{{ route('admin.axle-counts.index') }}">
                                Axle Counts
                            </a>
                        </li>
                    </ul>
                </li>


            </ul>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
    </nav>
</div>
<!-- Page Sidebar Ends -->
