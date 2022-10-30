<?php

class Paginator {

    private $request_path;
    private $per_page; //limit the number of records to display
    //current page
    private $page;


    //last page
    private $last_page;
    //calculated based on the number of rows found & the number of rows to
    //display per page: $rows_found/$per_page (number of results to display per page)

    private $rows_found;
    private $pagination_links;

    public function __construct($rows_found,$per_page){
        $this->rows_found = $rows_found;
        $this->per_page = $per_page;
        //calculated based on the number of rows found & the number of rows to display per page:
        // $rows_found/$per_page (number of results to display per page) and remove any decimals
        $this->last_page = ceil($rows_found/$per_page);
        //initialize which page. if none provided, start at 1
        $this->page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $this->request_path = $this->get_request_path();
    }

    public function get_request_path(){

        return parse_url($_SERVER['REQUEST_URI'])['path'];

    }

    public function create_pagination_links(){
        for($page = 1; $page <= $this->last_page; $page++){
            $is_link_active = "";
            if($page == $this->page) $is_link_active = " active";

            $query_strings = $this->get_query_strings();
            unset($query_strings['page']);
            //append QS to preserve any and prepend (? or &) conditionally
            $request_url = (isset($query_strings) && !empty($query_strings)) ? $this->get_request_path()."?".http_build_query($query_strings) . '&' : $this->get_request_path() . '?' ;

            $this->pagination_links .= $this->create_html_for_pagination_links($page,$request_url,$page,$is_link_active);
        }
    }

    public function create_html_for_pagination_links($page_number,$request_url,$page_value,$is_link_active=""){

        return "<li class='page-item". $is_link_active. "'>
                    <a class='page-link' href='".$request_url."page=".$page_number."'>".$page_value."</a>
               </li>";

    }

    public function create_previous($previous_text = 'Previous'){
        if ($this->page > 1) {
            //Show 'Previous' only if page number is greater than 1,
            $previous_page = $this->page - 1;
            //TODO: duplicate code, could be moved
            $query_strings = $this->get_query_strings();
            unset($query_strings['page']);
            //append QS to preserve any and prepend (? or &) conditionally
            $request_url = (isset($query_strings) && !empty($query_strings)) ? $this->get_request_path()."?".http_build_query($query_strings) . '&' : $this->get_request_path() . '?' ;
            $this->pagination_links .= $this->create_html_for_pagination_links($previous_page,$request_url,$previous_text);
        }
    }

    public function create_next($next_text = "Next"){
        if($this->last_page != 1){
            //Do NOT show 'Next' on last page,
            if($this->page != 1 && $this->page != $this->last_page){
                $next_page = $this->page + 1;
                //TODO: duplicate code, could be moved
                $query_strings = $this->get_query_strings();
                unset($query_strings['page']);
                //append QS to preserve any and prepend (? or &) conditionally
                $request_url = (isset($query_strings) && !empty($query_strings)) ? $this->get_request_path()."?".http_build_query($query_strings) . '&' : $this->get_request_path() . '?' ;
                $this->pagination_links .= $this->create_html_for_pagination_links($next_page,$request_url,$next_text);
            }
        }
    }

    public function get_pagination_links(){
        //if only one page is found, no pagination should be returned.
        if($this->last_page == 1) return '';

        $this->create_previous();
        $this->create_pagination_links();
        $this->create_next();

        return $this->pagination_links;

    }

    public function get_offset_and_limit(){
        //offset = ($current - 1) * $per_page
        // ex: page 5 of 100 rows with 10 per page ~~ (5 - 1) * 10 = start 40 and get 10 more
        return "LIMIT ".($this->page - 1) * $this->per_page.",". $this->per_page;

    }

    public function get_query_strings(){

        parse_str($_SERVER['QUERY_STRING'],$query_strings);
        return $query_strings;

    }

}