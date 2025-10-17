<div class="vertical-menu">
    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Menu</li>

                <li>
                    <a href="index.php?link=dashboard">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?link=transaksi">
                        <i data-feather="dollar-sign"></i>
                        <span data-key="t-dashboard">Transaksi</span>
                    </a>
                </li>


                <?php if ($_SESSION['level'] === "super admin") { ?>
                    <li>
                        <a href="index.php?link=laporan">
                            <i data-feather="file-text"></i>
                            <span data-key="t-authentication">Laporan</span>
                        </a>

                    </li>
                <?php } ?>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="database"></i>
                        <span data-key="t-authentication">Data Master</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="index.php?link=kategori" data-key="t-login">Kategori</a>
                        </li>

                        <li>
                            <a href="index.php?link=user" data-key="t-login">User</a>
                        </li>


                    </ul>
                </li>

            </ul>

        </div>
        <!-- Sidebar -->
    </div>
</div>