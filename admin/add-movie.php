<?php
include_once 'header.php';

$action = (isset($_GET['action']) && $_GET['action'] == 'edit') ? 'Edit' : 'Add';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(strtolower($action) == 'edit'){
        $moviesController->editMovie($_POST);
    }else{
        $moviesController->addMovie();
    }
}
if(strtolower($action) == 'edit'){
    $movie = $moviesController->getMovieByGuid($_GET['id']);
}
?>
    <body>
<div class="container-fluid">
    <div class="row">
        <div class ="col-sm-12" style="padding-left:0px;padding-right:0px;">
            <div id="main-container">
                <?php
                include_once 'sidebar.php';
                ?>
                <div id="main-panel">
                    <?php
                    include_once 'notifications.php';
                    ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="add-movie-header">
                                <h4><?= $action ?> Movie -> <a href="list-movies.php">Go Back</a></h4>
                            </div>
                            <div id="add-movie-form-container">
                                <form class="form-horizontal" method="post" id="add-movie-form" autocomplete="off" enctype="multipart/form-data">

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="mv_title">Title:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="title" placeholder="" name="mv_title" value="<?= isset($_POST['mv_title']) ? $_POST['mv_title'] : ((isset($movie['mv_title'])) ? $movie['mv_title'] : '') ; ?>" />
                                            <span class="help-block error"><?= Validator::getErrorForField('mv_title') ?></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="genre[]">Genre:</label>
                                        <div class="col-sm-10">
                                            <select data-placeholder="Select Genre(s)..." multiple class="form-control genre"  name="genres[]" id="genre[]">
                                                <?php
                                                include_once ('../Crud.php');
                                                $crud = new Crud();
                                                $genres = $crud->read('SELECT * FROM genres');
                                                $current_genres = $moviesController->getMovieGenresByID($movie['mv_id']);
                                                foreach ($genres as $genre){
                                                    //this will handle POSTBACK, if validation Fails and we need to keep the values from the POST
                                                    $selected = (in_array($genre['gnr_id'],$_POST['genres'])) ? "selected='selected'" : "";
                                                    echo("<option value=\"".$genre['gnr_id']."\" $selected>".$genre['gnr_name']."</option>");
                                                }
                                                foreach ($current_genres as $genre){
                                                    echo("<option value='".$genre['gnr_id']."' selected='selected'>".$genre['gnr_name']."</option>");
                                                }
                                                ?>
                                            </select>
                                            <span class="help-block error"><?= Validator::getErrorForField('genres') ?></span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="rating">Rating:</label>
                                        <div class="col-sm-10">
                                            <select data-placeholder="Select Rating" class="form-control rating"  name="rating_id" id="rating_id">
                                                <option value="">- Select a Rating</option>
                                                <?php
                                                //include_once ('../Crud.php');
                                                //$crud = new Crud();
                                                $ratings = $crud->read('SELECT * FROM ratings');
                                                //$current_genres = $moviesController->getMovieGenresByID($movie['mv_id']);
                                                foreach ($ratings as $rating){
                                                    //this will handle POSTBACK, if validation Fails and we need to keep the values from the POST
                                                    $selected = (in_array($rating['rating_id'], $_POST['rating_id']) || $movie['rating_id'] == $rating['rating_id']) ? "selected='selected'" : "";
                                                    echo("<option value=\"".$rating['rating_id']."\" $selected>".$rating['rating']."</option>");
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="mv_year_released">Year Released:</label>
                                        <div class="col-sm-10">
                                            <input id="datepicker" name="mv_year_released" data-date-format="yyyy-mm-dd" class="form-control" type="text" value="<?= (isset($_POST['mv_year_released'])) ? $_POST['mv_year_released'] : ((isset($movie['mv_year_released'])) ? $movie['mv_year_released'] : '') ; ?>" />
                                            <span class="help-block error"><?= Validator::getErrorForField('mv_year_released') ?></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2 feature-label" for="mv_featured">Featured:</label>
                                        <div class="col-sm-10">
                                            <input id="mv_featured" name="mv_featured" class="form-control" type="checkbox" value="<?= (isset($movie['mv_featured']) && $movie['mv_featured'] == 1) ? 1 : 0 ?>" <?= (isset($movie['mv_featured']) && $movie['mv_featured'] == 1) ? "checked='checked'" : "" ?> />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="year">Cover Image:</label>
                                        <div id="cover-img-input-container" class="col-sm-10">
                                                <?php
                                                $hide_cover = (!isset($movie['img_path'])) ? "hidden" : "";
                                                ?>
                                                <div class="image-container <?=$hide_cover?>">
                                                    <button type="button" id="cover-x" class="x" title="Remove Cover Image">X</button>
                                                    <?php
                                                    echo("<img class='".$hide_cover."' id='customFileInput-img' src='../".$movie['img_path']."' alt='' width='194' height='259' style='margin-bottom:10px;' />");
                                                    //now use a different input group
                                                    ?>
                                                </div>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" name="cover_image" class="custom-file-input form-control <?= isset($movie['img_path']) ? 'hidden' : '' ?>" id="customFileInput" aria-describedby="customFileInput">
                                                        <label class="custom-file-label <?=$hide_cover?>" for="customFileInput">Replace existing cover file:</label>
                                                    </div>
                                                    <div class="input-group-append <?=$hide_cover?>">
                                                        <button id="movie-cover-upload-button" class="btn btn-primary cursor-pointer" type="button" id="customFileInput">Upload new cover</button>
                                                    </div>
                                                </div>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="year">Hero Image: <br /><small>(home carousel)</small></label>
                                        <div id="hero-img-input-container" class="col-sm-10">
                                            <?php
                                            $hide_cover = (!isset($movie['hero_path'])) ? "hidden" : "";
                                            ?>
                                                <div class="image-container <?= $hide_cover ?>">
                                                    <button type="button" id="hero-x" class="x" title="Remove Cover Image">X</button>
                                                    <?php
                                                    echo("<img class='".$hide_cover."' id='customFileHeroInput-img' src='../".$movie['hero_path']."' alt='' style='margin-bottom:10px;max-height:200px;' />");
                                                    //now use a different input group
                                                    ?>
                                                </div>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" name="hero_image" class="custom-file-input form-control <?= isset($movie['hero_path']) ? 'hidden' : '' ?>" id="customFileHeroInput" aria-describedby="customFileInput">
                                                        <label class="custom-file-label <?=$hide_cover?>" for="customFileHeroInput">Replace existing hero file:</label>
                                                    </div>
                                                    <div class="input-group-append <?=$hide_cover?>">
                                                        <button id="movie-hero-upload-button" class="btn btn-primary cursor-pointer" type="button" id="customFileHeroInput">Upload new hero image</button>
                                                    </div>
                                                </div>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>



                                    <div class="form-group">
                                        <label class="control-label col-sm-2 synopsis-label" for="mv_synopsis">Synopsis:</label>
                                        <div class="col-sm-10">
                                            <textarea id="mv_synopsis" name="mv_synopsis" class="form-control" type="textarea" rows="4" cols="50"><?= (isset($_POST['mv_synopsis'])) ? $_POST['mv_synopsis'] : ((isset($movie['mv_synopsis'])) ? $movie['mv_synopsis'] : '') ; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn pull-right btn-success">Submit</button>
                                            <a href="list-movies.php" class="btn pull-right btn-secondary" style="margin-right: 5px;">Back</a>
                                        </div>
                                    </div>
                                    <input type="hidden" name="action" value="<?= (isset($movie)) ? 'edit' : 'add' ?>" />
                                    <?php
                                    if (isset($movie)){
                                        ?>
                                        <input type="hidden" name="mv_guid" value="<?= $movie['mv_guid'] ?>" />
                                        <?php
                                    }
                                    ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Footer -->
                    <footer class="page-footer font-small blue">
                        <!-- Copyright -->
                        <div class="footer-copyright text-center py-3 sidebar_tooltip">&copy <?= date('Y'); ?> Copyright:
                            <a href="#"> Movie Manager</a>
                            <span class="tooltiptext"><?= $tooltiptext ?></span>
                        </div>
                        <!-- Copyright -->

                    </footer>
                    <!-- Footer -->
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    $( function() {
        $( "#datepicker" ).datepicker({
            format: 'yyyy-mm-dd'
        });
        $(".genre").chosen();
    } );
</script>
<?php
include_once 'footer.php';
?>