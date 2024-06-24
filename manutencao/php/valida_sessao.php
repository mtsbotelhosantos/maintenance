<?php 

if(!isset($_SESSION)){
    session_start();


    if(!isset($_SESSION['id_session'])){
        die(header("Location: ../../login_page"));

    }

}