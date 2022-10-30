<?php

include_once('Crud.php');
include_once('Paginator.php');

class MoviesController {
    private $crud;

    public $pagination_links;

    public function __construct(){
        $this->crud = new \Crud();
    }

    public function addMovie(){
        $movie_data = [
            'mv_title' => $_POST['mv_title'],
            //'mv_year_released' => date('Y-m-d',strtotime($_POST['mv_year_released']))
            'mv_year_released' => $_POST['mv_year_released'],
            //this sets a default value if one is not passed
            'mv_featured' => ((isset($_POST['mv_featured'])) ? $_POST['mv_featured'] : 0),
            //use this to obfusate ID#s later
            'mv_guid' => $this->generate_random_letters(6)
        ];
        $movie_genres = isset($_POST['genres']) ? $_POST['genres'] : "";

        //here are the rules to validate data before passing it on
        $validation_rules = [
            'mv_title' => 'required',
            'mv_year_released' => 'date',
            'genres'=>'required'
        ];

        $validation_data = $movie_data + ['genres' => $movie_genres];
        $validator = new Validator($validation_data,$validation_rules);
        $validator->validate();

        if($validator->passes()) {
            $movie_id = $this->crud->create($movie_data, 'movies');

            $movie_genres = (isset($_POST['genres'])) ? $_POST['genres'] : '';
            $this->createMovieGenre($movie_genres, $movie_id);
            if(isset($_POST['rating_id'])){
                $this->createMovieRating($_POST['rating_id'], $movie_id);
            }
            $this->saveAndUploadCoverImage($movie_id);

            appSession::set("success-message", "Movie " . $_POST['mv_title'] . " was added successfully!");

            //return to the list page
            header('Location: list-movies.php');
        }
        //if not 'passes' reload the page, now complete with error messages
    }

    public function generate_random_letters($length) {
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= chr(rand(ord('a'), ord('z')));
        }
        return $random;
    }

    public function createMovieGenre($movie_genres,$movie_id){
        $params = ['mv_id' => $movie_id];
        $existing_movie_genres = $this->crud->read("SELECT * from mv_genres WHERE mvg_ref_movie = :mv_id",$params,'all');
        foreach ($movie_genres as $genre_id){
            //check the existing records
            if (array_search($genre_id, array_column($existing_movie_genres, "mvg_ref_genre")) === FALSE) {
                //if not already in the DB, add it here.
                $movie_genres_array = [
                    'mvg_ref_genre' => $genre_id,
                    'mvg_ref_movie' => $movie_id
                ];
                $this->crud->create($movie_genres_array, 'mv_genres');
            }
        }

    }

    public function createMovieRating($movie_rating_id,$movie_id){
        $movie_rating_array = [
            'mvr_ref_rating' => $movie_rating_id,
            'mvr_ref_movie' => $movie_id
        ];
        //first check for a database entry for this movie
        if(!empty($this->crud->read("Select mvr_ref_movie from mv_ratings where mvr_ref_movie = :mvr_ref_movie",['mvr_ref_movie' => $movie_id] ,'column'))){
            //if found, update the row
            $this->crud->update("UPDATE mv_ratings set mvr_ref_rating = :mvr_ref_rating WHERE mvr_ref_movie = :mvr_ref_movie", $movie_rating_array, 'query');

        }else{
            //else create a new row
            $this->crud->create($movie_rating_array, 'mv_ratings');
        }
    }

    public function getMovies($per_page = 10, $search_condition = '',$is_featured = false)
    {
        $sql = "select m.mv_id,m.mv_title, m.mv_year_released, i.img_path, m.mv_synopsis, m.mv_guid,
	                    group_concat(g.gnr_name) as genres, r.rating_id, r.rating
	                    from movies m
	                    join mv_genres mg on mg.mvg_ref_movie = m.mv_id
	                    join genres g on mg.mvg_ref_genre = g.gnr_id
	                    LEFT OUTER join mv_ratings mr on mr.mvr_ref_movie = m.mv_id
	                    LEFT OUTER join ratings r on mr.mvr_ref_rating = r.rating_id
                        LEFT OUTER join images i on i.img_ref_movie = m.mv_id and (i.type = 'cover')
	                    ";

        //only fetch 'featured' movies
        if ($is_featured) {
            $sql .= ' WHERE mv_featured = 1 ';
            //if looking for featured, ignore search condition, as to not break sql query
        } else {
            if (!empty($search_condition)) $sql .= " $search_condition";
        }
        $sql .=    " group by m.mv_id,i.img_path,r.rating_id
                    ORDER BY mv_id DESC";

        $rows_found = count($this->crud->read($sql, null,'all'));

        $paginator = new Paginator($rows_found,$per_page);

        $offset_and_limit =  $paginator->get_offset_and_limit();

        $sql .= " " .$offset_and_limit;

        $results = $this->crud->read($sql, null,'all');

        $this->pagination_links = $paginator->get_pagination_links();

        return  $results;
    }

    public function searchMovies($per_page,$search_item){
        $search_condition = $this->constructSearchCondition($search_item);
        return $this->getMovies($per_page,$search_condition);

    }

    public function constructSearchCondition($search_item){
        //TODO: there's got to be a better way to write this logic
        if($this->startsWith($search_item, '"') && $this->endsWith($search_item, '"')){
            //exact search with double-quotes
            $search_item = trim($search_item,'"');
            $search_condition = " WHERE mv_title = '$search_item'";
        }elseif($this->startsWith($search_item, "'") && $this->endsWith($search_item, "'")) {
            //exact search with single-quotes
            $search_item = trim($search_item,"'");
            $search_condition = " WHERE mv_title = '$search_item'";
        }else{
            //otherwise split terms into LIKE condition
            $search_item_arr = explode(' ', $search_item);

            //first remove stopwords
            $stopwords = array('the','this','then','there','from','for','to','as','a','an','and','the');
            $search_item_arr = array_diff($search_item_arr, $stopwords);

            $j = 0;
            foreach ($search_item_arr as $item) {
                $j++;
                if ($j == 1) {
                    $search_condition = " WHERE mv_title LIKE '%$item%'";
                } else if ($item != "") {
                    $search_condition .= " OR mv_title LIKE '%$item%'";
                }
            }
        }
        return $search_condition;
    }

    private function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    private function endsWith($string, $endString)
    {
        $len = strlen($endString);
        if ($len == 0) {
            return true;
        }
        return (substr($string, -$len) === $endString);
    }



    public function getFeaturedMovies(){
        //TODO add an 'order by' for sorting
        $sql = "SELECT mv_guid,mv_title,mv_synopsis,img_path as hero_path
                from movies m
                LEFT OUTER join images i on i.img_ref_movie = m.mv_id and (i.type = 'hero')
                where mv_featured = 1";
        return $this->crud->read($sql,[],'all');
    }

    public function getFeaturedGenres(){
        //TODO add an a way to set these on the front-end
        // ...right now, it's a back-end only task
        $sql = "SELECT gnr_id,gnr_name from genres where gnr_featured = 1";
        return $this->crud->read($sql,[],'all');
    }
    public function getMovieByGuid($movie_guid){
        $params = ['mv_guid' => $movie_guid];
        $sql = "select m.mv_id,m.mv_guid,m.mv_title, m.mv_featured, m.mv_year_released, ic.img_path, ih.img_path as hero_path, m.mv_synopsis,
	                    group_concat(g.gnr_name) as genres, r.rating_id, r.rating
	                    from movies m
	                    join mv_genres mg on mg.mvg_ref_movie = m.mv_id
	                    join genres g on mg.mvg_ref_genre = g.gnr_id
	                    LEFT OUTER join mv_ratings mr on mr.mvr_ref_movie = m.mv_id
	                    LEFT OUTER join ratings r on mr.mvr_ref_rating = r.rating_id
                        LEFT OUTER join images ic on ic.img_ref_movie = m.mv_id and (ic.type = 'cover')
                        LEFT OUTER join images ih on ih.img_ref_movie = m.mv_id and (ih.type = 'hero')
                        where m.mv_guid = :mv_guid
	                    group by m.mv_id,ic.img_path,ih.img_path,r.rating_id";
        return $this->crud->read($sql,$params,'row');
    }

    public function editMovie($params){
        // Validate inputs
        $movie_guid = $params['mv_guid'];

        if (!$movie_guid) {
            throw new Exception("Need a MovieID # to Update");
        }
        $movie_data = [
            'mv_guid' => $movie_guid,
            'mv_title' => $params['mv_title'],
            'mv_year_released' => $params['mv_year_released'],
            //this sets a default value if one is not passed                                    //cast correct datatype
            'mv_featured' => ((isset($params['mv_featured']) && $params['mv_featured'] != '') ? (int)$params['mv_featured'] : 0),
            'mv_synopsis' => $params['mv_synopsis'],
            'genres' => $params['genres']
        ];

        //here are the rules to validate data before passing it on
        $validation_rules = [
            'mv_title' => 'required',
            'mv_year_released' => 'date',
            'genres'=>'required'
        ];

        $validation_data = $movie_data;
        $validator = new Validator($validation_data,$validation_rules);
        $validator->validate();

        if($validator->passes()) {

            //go get the movieID
            $movie_id = $this->crud->read("Select mv_id FROM movies where mv_guid = :mv_guid", ['mv_guid' => $movie_guid] , 'column');

            //TODO: make this build a dynamic update statement for future parameters
            //$sql = foreach($param)....
            $this->crud->update("UPDATE movies set mv_title = :mv_title, mv_year_released = :mv_year_released, mv_synopsis = :mv_synopsis, mv_featured = :mv_featured WHERE mv_guid = :mv_guid", $movie_data, 'query');
            $this->createMovieGenre($movie_data['genres'], $movie_id);
            $this->createMovieRating($params['rating_id'], $movie_id);
            $this->deleteDeselectedGenres($movie_id);

            //upload movie image
            //check if a new file was added to the form
            $types = ['cover', 'hero'];
            foreach ($types as $type) {
                if (!empty($_FILES[$type . '_image']['name'])) {
                    //delete the previous
                    $this->crud->delete("DELETE FROM images where img_ref_movie = :movie_id and type=:type", ['movie_id' => $movie_id, 'type' => $type], 'query');
                    //add the new file
                    $this->saveAndUploadCoverImage($movie_id, $type);
                }
            }

            $title =$params['mv_title'];
            appSession::set("success-message","Movie '".$title."' was updated successfully!");
            //return to the list page
            header('Location: list-movies.php');
            exit();
        }//end if passes

    }

    public function getMovieTitleByGuid($movie_guid){
        if (!$movie_guid) {
            throw new Exception("Need a MovieID # to get title.");
        }
        $params = ['mv_guid' => $movie_guid];
        $sql = "select mv_title from movies where mv_guid = :mv_guid";
        return $this->crud->read($sql,$params,'column');
    }

    public function getMovieGenresByID($movie_id){
        $params = ['mv_id' => $movie_id];
        $sql = "select g.gnr_id,g.gnr_name from mv_genres mg
                    LEFT join genres g on g.gnr_id = mg.mvg_ref_genre
                    where mg.mvg_ref_movie = :mv_id";
        return $this->crud->read($sql,$params,'all');
    }

    public function deleteDeselectedGenres($movie_id){
        //go get all existing genres for this movie
        $movie_genres = $this->crud->read("Select mvg_ref_genre from mv_genres where mvg_ref_movie = :mv_id",['mv_id' => $movie_id], 'all');
        foreach ($movie_genres as $genre){
            //check that the posted data is no longer found in existing records
            $genre_id = $genre['mvg_ref_genre'];
            if(!in_array($genre_id,$_POST['genres'])){
                //item should be deleted, it has been removed from the form.
                $this->crud->delete('DELETE FROM mv_genres WHERE mvg_ref_genre = :genre_id',['genre_id' => $genre_id],'query');
            }
        }
    }

    public function saveAndUploadCoverImage($movie_id,$type = 'cover'){
        //set the directory where the image will live
        $dir = "../images/movie_covers/movie_$movie_id/";
        //if the directory does not exist, create it now
        if(!file_exists($dir)){
            mkdir($dir,0777, true);
        }
        $dir .= basename($_FILES[$type.'_image']['name']);
        //TODO: Display an Error if file move fails
        //use this function to place the file
       if(!move_uploaded_file($_FILES[ $type.'_image']['tmp_name'],$dir )) {
            die('ERROR MOVING FILE');
        }
        //add the params to an array
        $img_info = [
            'img_path' => str_replace('../','',$dir),
            'type' => $type,
            'img_ref_movie' => $movie_id
        ];
        //now add the new data to the DB
        $this->crud->create($img_info,'images');


    }

    public function deleteMovie($movie_guid){
        if (!$movie_guid) {
            //minimum param needed to continue...
            throw new Exception("Need a Movie GUID # to Delete");
        }

        //return the title to the message output
        $movie_title = $this->getMovieTitleByGuid($movie_guid);

        //append quotes if anything is returned
        if (isset($movie_title) && $movie_title != "") "'" . $movie_title . "'";

        //delete the movie entry
        $this->crud->delete('DELETE from movies where mv_guid = :mv_guid',['mv_guid' => $movie_guid],'query');


        //TODO: delete all orphaned rows in genres and ratings tables
        appSession::set('success-message',"Movie $movie_title has been deleted.");

        header('Location: list-movies.php');
        exit();
    }
}