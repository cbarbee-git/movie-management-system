<?php
include_once 'header.php';
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
                <div id="main-panel-left-sidebar">
                    <div id ="add-btn-container">
                        <div class = "sidebar-row">
                            <a href="add-movie.php" class="btn btn-add alive" title="+ Add New Movie">
                                <i class="fas fa-plus"></i>
                                Add New Movie</a>
                        </div>
                    </div>
                    <?php
                    include_once 'featured.php';
                    ?>
            </div>
            <div id="users-container">
                <div id="pagination-container">
                    <ul style="display:flex; position:relative;margin-top:6px">
                        <li>Show</li>
                        <li>
                            <select name="per_page" id="per_page">
                                <?php
                                 $per_page_option_array = [5,10,15,20];
                                 foreach($per_page_option_array as $option_value){
                                     $selected =  ($per_page == $option_value) ? " selected='selected'" : "";
                                     echo("<option $selected>".$option_value."</option>");
                                 }
                                ?>
                            </select>
                        </li>
                        <li>entries</li>
                    </ul>
                    <ul style="display:flex;margin-left: auto" class="paginator-ul">
                    <?= $pagination_links ?>
                    </ul>
                </div>
                <table id="movies">
                    <tr>
                        <th>Movie Title</th> <th>Genre(s)</th> <th>Date Released</th><th>Cover Image</th> <th>Actions</th>
                    </tr>
                        <?php
                        foreach ($movies as $movie){
                        ?>
                        <tr>
                                <td><?= $movie['mv_title'] ?></td>
                                <td><?= $movie['genres'] ?></td>
                                <td><?= date('m/d/Y',strtotime($movie['mv_year_released'])) ?></td>
                                <td style="width:15px">
                                    <img src="../<?= (isset($movie['img_path'])) ? $movie['img_path'] : ('images/movie_covers/empty.jpeg') ?>" height="50px" width="50px" />
                                </td>
                                <td style="width:15px">
                                    <a href="add-movie.php?action=edit&id=<?= $movie['mv_guid'] ?>">edit</a>
                                    <a class="delete-movie" href="#" data-movie-id="<?= $movie['mv_guid'] ?>">delete</a>
                                </td>
                        </tr>
                        <?php } ?>
                </table>
            </div>            
        </div>
            <!-- Footer -->
            <footer class="page-footer font-small blue">

                <!-- Copyright -->
                <div class="footer-copyright text-center py-3">&copy; <?= date('Y'); ?> Copyright:
                    <a href="#"> Movie Manager</a>
                </div>
                <!-- Copyright -->

            </footer>
            <!-- Footer -->
            </div>

        </div>
    </div>
</div>
    </div>
<?php
include_once 'footer.php';
?>