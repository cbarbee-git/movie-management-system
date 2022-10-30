<?php
$tooltiptext = "Non-Functional Link";
?>
<aside id="left-sidebar">
    <div id="nav-brand-container">
        <div class="sidebar-row">
            <a id="nav-brand"> Movie Manager
            </a>
            <div class="sidebar_tooltip cursor-pointer" style="display: inline;"><i id="bars" class="fas fa-bars"></i><span class="tooltiptext"><?= $tooltiptext ?></span></div>
        </div>
    </div>
    <div id="profile-pic-container">
        <div class = "sidebar-row">
            <img src="../<?= (is_array($user_array)) ? $user_array['photo_img'] : 'image/admin.jpg' ?>" height="60px" width="60px">
            <ul id="button-container">
                <li><strong><i>Welcome!</i> <?= (is_array($user_array)) ? $user_array['name'] : 'Guest User' ?></strong><span class="active"></span></li>
                <li style="color:#8393aa; font-size:10px;font-weight: 800;text-transform: uppercase;"><?= (is_array($user_array)) ? $user_array['role'] : 'guest' ?></li>
                <li>
                    <div class="sidebar_tooltip edit-profile"><button class="btn btn-edit-profile">Edit Profile</button>
                        <span class="tooltiptext"><?= $tooltiptext ?></span></div>
                    <button class="btn btn-logout alive" title="Logout"><i class="fas fa-sign-out-alt"></i>Logout</button>
                </li>
            </ul>
        </div>
    </div>
    <div class = "sidebar-row">
        <div id="sidebar-items">
            <ul>
                <li class="sidebar_tooltip">
                    <a>
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <span class="tooltiptext"><?= $tooltiptext ?></span>
                </li>
                <li class="sidebar_tooltip">
                    <a>
                        <i class="fas fa-file-video"></i>
                        <span>Movies</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <span class="tooltiptext"><?= $tooltiptext ?></span>
                </li>
                <li class="sidebar_tooltip">
                    <a>
                        <i class="fas fa-tv"></i>
                        <span>Genres</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <span class="tooltiptext"><?= $tooltiptext ?></span>
                </li>
                <li class="sidebar_tooltip">
                    <a>
                        <i class="far fa-images"></i>
                        <span>Movie Icons</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <span class="tooltiptext"><?= $tooltiptext ?></span>
                </li>
                <span style="color:#a6b6c7;">EXTRAS</span>
                <li class="sidebar_tooltip">
                    <a>
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <span class="tooltiptext"><?= $tooltiptext ?></span>
                </li>
                <li class="sidebar_tooltip">
                    <a>
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                    <span class="tooltiptext"><?= $tooltiptext ?></span>
                </li>
                <li class="sidebar_tooltip">
                    <a>
                        <i class="fas fa-users-cog"></i>
                        <span>User Config</span>
                    </a>
                    <span class="tooltiptext"><?= $tooltiptext ?></span>
                </li>
                <li class="sidebar_tooltip">
                    <a>
                        <i class="fas fa-money-check-alt"></i>
                        <span>Payment Methods</span>
                    </a>
                    <span class="tooltiptext"><?= $tooltiptext ?></span>
                </li>
                <li class="sidebar_tooltip">
                    <a>
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Invoices</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <span class="tooltiptext"><?= $tooltiptext ?></span>
                </li>
            </ul>
        </div>
    </div>
</aside>
