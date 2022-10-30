<?php
include_once('../Session.php');
include_once('../Validator.php');
//make this class available to all pages here in Admin
include_once('../MoviesController.php');
$moviesController = new MoviesController();

appSession::start();
if(!appSession::exists('active_user')){
    header('Location: ../login.php');
    exit();
}else{
    //get values to be used later on to display user info
    $user_array = appSession::get('active_user');
}

//TODO: find a better way to manage this
//find out which file is begin used. This class is only needed on this one page.
if(basename($_SERVER["SCRIPT_NAME"]) == 'list-movies.php'){
    $per_page = isset($_GET['per_page']) ? $_GET['per_page'] : 10;
    $movies = $moviesController->getMovies($per_page);
    //go get pagination HTML output to use later
    $pagination_links = $moviesController->pagination_links;
}

if(isset($_GET['action']) && $_GET['action'] == "delete-movie"){
    if(isset($_GET['id'])){
        //TODO: do more validation here, need to prevent URL hijacking
        $movie_guid = $_GET['id'];
        $moviesController->deleteMovie($movie_guid);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movies - Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

    <link rel="apple-touch-icon" sizes="120x120" href="../images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../images/favicon/favicon-16x16.png">
    <link rel="manifest" href="../images/favicon/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <link href="../css/style.css" rel="stylesheet" type="text/css">
    <?php //TODO: maybe these should be conditional to reduce pageload? ?>
    <!-- Jquery Plugins (datepicker, chosen, & confirm) CSS below -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.css" integrity="sha512-YdYyWQf8AS4WSB0WWdc3FbQ3Ypdm0QCWD2k4hgfqbQbRCJBEgX0iAegkl2S1Evma5ImaVXLBeUkIlP6hQ1eYKQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" integrity="sha512-yVvxUQV0QESBt1SyZbNJMAwyKvFTLMyXSyBHDO4BG5t7k/Lw34tyqlSDlKIrIENIzCl+RVUNjmCPG+V/GMesRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css" integrity="sha512-wJgJNTBBkLit7ymC6vvzM1EcSWeM9mmOu+1USHaRBbHkm6W9EgM0HY27+UtUaprntaYQJF75rc8gjxllKs5OIQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- //End Jquery plugins -->

    <style>
        .page-link {
            background-color: unset !important;
            padding: 6px 12px 6px 12px !important;
            color:white;
            border: none;
        }
        .page-item {
            padding-bottom:4px;
        }
    </style>


</head>