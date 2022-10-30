<div id="notifications-container">
    <h3>Movies</h3>
    <ul style="display: flex;margin-left:auto">
        <?php
        $tooltiptext = "Non-Functional Link";
        ?>
        <li class="navbar_tooltip">
            <a class="nav-link">
                <i class="fas fa-address-card"></i>
            </a>
            <span class="tooltiptext"><?= $tooltiptext ?></span>
        </li>
        <li class="navbar_tooltip">
            <a class="nav-link">
                <i class="fas fa-envelope"></i>
            </a>
            <span class="tooltiptext"><?= $tooltiptext ?></span>
        </li>
        <li class="navbar_tooltip">
            <a class="nav-link">
                <i class="fas fa-bell"></i>
            </a>
            <span class="tooltiptext"><?= $tooltiptext ?></span>
        </li>
        <li>
            <a class="nav-link">
                <img src="../<?= (is_array($user_array)) ? $user_array['photo_img'] : 'image/admin.jpg' ?>" height="25px" width="25px">
            </a>

        </li>
        <li >
            <a class="nav-link">
                <?= (is_array($user_array)) ? $user_array['name'] : 'Guest Name' ?>
            </a>
        </li>
        <li>
            <a href="../login.php?action=logout" class="nav-link">
                <i class="fas fa-sign-out-alt" title="Logout" aria-hidden="true"></i>
            </a>
        </li>
    </ul>
</div>