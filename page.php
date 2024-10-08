<?php
include "config/connect.php";
$mod = isset($_GET['mod']) ? $_GET['mod'] : '';

if($_GET['mod']=='home') {
    include "home.php";
    
}

elseif($_GET["mod"]== "register") {
    include "register/register.php";

}

elseif($_GET["mod"]== "reg-rumah") {
    include "register/register_rumah.php";

}

elseif($_GET["mod"]== "reg-warung") {
    include "register/register_warung.php";

}

elseif($_GET["mod"]== "reg-pengelola") {
    include "register/register_pengelola.php";

}

elseif($_GET["mod"]== "verify") {
    include "pengelola/verify_user.php";
}

elseif($_GET["mod"]== "verify-war") {
    include "pengelola/verify_warung.php";
}

elseif($_GET["mod"]== "pengelola") {
    include "pengelola/pengelola.php";
}

elseif($_GET["mod"]== "data-penarikan") {
    include "pengelola/data_penarikan.php";
}

elseif($_GET["mod"]== "edit-sampah") {
    include "pengelola/manage_sampah.php";
}

elseif($_GET["mod"]== "edit") {
    include "pengelola/edit.php";
}

elseif($_GET["mod"]== "edit-user") {
    include "pengelola/edit_user.php";
}

elseif($_GET["mod"]== "edit-war") {
    include "pengelola/edit_warung.php";
}

elseif($_GET["mod"]== "warung") {
    include "warung/warung.php";
}

elseif($_GET["mod"]== "pencairan") {
    include "warung/pencairan_saldo.php";
}

elseif($_GET["mod"]== "admin") {
    include "admin/dashboard.php";
}

elseif($_GET["mod"]== "admin-sampah") {
    include "admin/manage_sampah.php";
}

elseif($_GET["mod"]== "admin-user") {
    include "admin/manage_user.php";
}

elseif($_GET["mod"]== "jual") {
    include "users/sell_sampah.php";
}

elseif($_GET["mod"]== "riwayat") {
    include "users/riwayat.php";
}

elseif($_GET["mod"]== "profile") {
    include "users/profile.php";
}

elseif($_GET["mod"]== "pembayaran") {
    include "users/pembayaran.php";
}

elseif($_GET["mod"]== "users") {
    include "users/dashboard.php";
}

elseif($_GET["mod"]== "unaut") {
    include "unauthorized.php";
}

elseif($_GET["mod"]== "unaut2") {
    include "unauthorized2.php";
}
?>