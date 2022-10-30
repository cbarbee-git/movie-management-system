<?php
include_once('MoviesController.php');
$movies_controller = new MoviesController();

$tooltiptext = "Non-Functional Link";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="apple-touch-icon" sizes="120x120" href="images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon/favicon-16x16.png">
    <link rel="manifest" href="images/favicon/site.webmanifest">
    <link rel="mask-icon" href="images/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <title>Latest Movies</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">


        <!--<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link href="css/style.css" rel="stylesheet" type="text/css">-->



        <!--<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
-->
        <link href="css/style.css" rel="stylesheet" type="text/css">

    </head>
<body style="background-color: #26262d">

<header>
    <nav class="navbar navbar-expand-lg fixed-top" style="color:#f0f0f0 !important;">
    <a class="navbar-brand" href="/">Movies Demo</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation" style="color:#f0f0f0">
        MENU
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="/">Home</a>
            </li>
            <li class="nav-item sidebar_tooltip">
                <a class="nav-link">About</a>
                <span class="tooltiptext"><?= $tooltiptext ?></span>
            </li>
            <li class="nav-item sidebar_tooltip">
                <a class="nav-link disabled">Contact us</a>
                <span class="tooltiptext"><?= $tooltiptext ?></span>
            </li>
        </ul>
        <section class="" style="width:34%;color:#ffffff;">
            <form action="#results" method="GET">
                <input class="" type="text" id="search-movies" placeholder="Find movies" name="search-term" required="" value="" style="width:100%">
            </form>
        </section>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active btn-signup">
            <a class="nav-link" href="admin/list-movies.php" style="color:black !important;">Login to Admin</a>
            </li>
        </ul>

    </div>
</nav>
</header>



<div id="demo" class="carousel slide" data-ride="carousel" style="height: 374px; overflow: hidden;background-size:cover;">
    <?php
    $featured_movies = $movies_controller->getFeaturedMovies();
    ?>
    <!-- Indicators -->
    <ul class="carousel-indicators">
        <?php
        $output_html = '';
        $j = count($featured_movies);
        for($i = 0; $i < $j ; $i++) {
            //set first record to the active slide on pageload
            $active = ($i == 0 ) ? " class='active'" : '';
            $output_html .= "<li data-target=\"#demo\" data-slide-to=\"".$i."\" ". $active ."></li>";
        }
        echo($output_html);
        ?>
    </ul>

    <!-- The slideshow -->
    <div class="carousel-inner">
        <?php
        $output_html = '';
        $j = count($featured_movies);
        foreach($featured_movies as $key => $featured_movie) {
            //Set Defaults
            //be sure data is available, otherwise...do show something
            $movie_title = (isset($featured_movie['mv_title'])) ? $featured_movie['mv_title'] : 'None';
            $movie_hero_path = (isset($featured_movie['hero_path'])) ? $featured_movie['hero_path'] : 'https://www.w3schools.com/bootstrap/la.jpg';
            $movie_synopsis = (isset($featured_movie['mv_synopsis'])) ? $featured_movie['mv_synopsis'] : 'This is a sample paragraph. Nothing was found in the database.';

            //set first record to the active slide on pageload
            $active = ($key == 0 ) ? " active" : '';
            $output_html .= "<div class=\"carousel-item".$active."\">\n";
            $output_html .= "\t<img src=\"$movie_hero_path\" alt=\"$movie_title\" width=\"1500\" height=\"370\">\n";
            $output_html .= "\t<div class=\"carousel-caption text-right\">\n";
            $output_html .= "\t\t<h1>".$movie_title."</h1>\n";
            $output_html .= "\t\t<p>".$movie_synopsis."</p>\n";
            $output_html .= "\t</div>\n";
            $output_html .= "</div>\n";
        }
        echo($output_html);
        ?>

    </div>
    <!-- Left and right controls -->
    <a class="carousel-control-prev" href="#demo" data-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </a>
    <a class="carousel-control-next" href="#demo" data-slide="next">
        <span class="carousel-control-next-icon"></span>
    </a>

</div>

<main role="main">
    <?php

    if(isset($_GET['search-term'])){
        $movies = $movies_controller->searchMovies(12,urldecode($_GET['search-term']));
        //var_dump($movies);
    }else{
        //TODO: remove the duplicated items from above section
        $movies = $movies_controller->getMovies(12);
    }
    //get the links here...maybe display before and after (?)
    $pagination_links = $movies_controller->pagination_links;

    ?>
    <div class="container marketing">
        <h3 class="mv-category-title"><?= (isset($_GET['search-term'])) ? "Search Results for: '" .urldecode($_GET['search-term']). "' (".count($movies).") <a class='clear-results btn alive' href='index.php'>Clear Results</a>"  : 'Most Recent' ?></h3>
        <div class="row">
            <div class="col-sm-12" style="text-align:center;margin-top:10px">
                <nav aria-label="" class="pagination-centered" style="display: inline-block; background-color:inherit">
                    <ul class="pagination home-pagination">
                        <?= $pagination_links ?>
                    </ul>
                </nav>
            </div>
        </div><!-- /.row -->
        <div class="row" style="min-height: 300px;">
            <?php
            foreach ($movies as $movie){
                //be sure data is available, otherwise...do show something
                $movie_title = (isset($movie['mv_title'])) ? $movie['mv_title'] : 'None';
                $movie_img_path = (isset($movie['img_path'])) ? $movie['img_path'] : '/image/movie_covers/empty.jpeg';
                $movie_genres = (isset($movie['genres'])) ? $movie['genres'] : 'None';
                $movie_release_date = (isset($movie['mv_year_released'])) ? date('m/d/Y',strtotime($movie['mv_year_released'])) : '';
                $movie_rating = (isset($movie['rating'])) ? $movie['rating'] : 'N/A';

            ?>
            <div class="col-sm-6 col-md-4 col-lg-3 mt-4">
                <div class="card">
                    <img class="card-img-top" src="<?= $movie_img_path ?>" />
                    <div class="card-block">
                        <h4 class="card-title"><?= $movie_title ?></h4>
                    </div>
                    <div class="card-footer">
                        <div id="mv-details-container">
                            <div><?= $movie_genres ?></div>
                            <div><?= $movie_release_date ?></div>
                        </div>
                        <div class="pl-right pg">
                            <div class="mv-pg">
                                <?= $movie_rating ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>

        </div>
        <div class="row">
            <div class="col-sm-12" style="text-align:center;margin-top:10px">
                <nav aria-label="" class="pagination-centered" style="display: inline-block; background-color:inherit">
                    <ul class="pagination home-pagination">
                        <?= $pagination_links ?>
                    </ul>
                </nav>
            </div>
        </div><!-- /.row -->
    </div><!-- /.container -->


    
</main>
<!-- FOOTER -->
<footer class="container-fluid">
        <p class="float-right"><a>Back to top</a></p>
        <p class="sidebar_tooltip text-center">&copy; <?= date("Y"); ?> Company, Inc. · <a>Privacy </a><span class="tooltiptext"><?= $tooltiptext ?>(s)</span> · <a>Terms</a></p>
    </footer>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!--<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>-->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
    <?php
    if(isset($_GET['search-term'])) {
    ?>
        <script>
            $(document).ready(function () {
                $('html, body').animate({
                    scrollTop: 300
                    //scrollLeft: 0
                }, 1000);
            });
        </script>
    <?php
    }
    ?>
</html>