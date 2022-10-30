<ul>
    <?php
                        //need both type and a field key
        $feature_types = ['movies' => 'mv_title','genres' => 'gnr_name'];
        foreach ($feature_types as $feature_type => $field_name ){
            echo("<span>FEATURED ".strtoupper($feature_type)."</span>");
            $method_to_call = "getFeatured".ucfirst(strtolower($feature_type));
            $featured_items = $moviesController->$method_to_call();
            foreach($featured_items as $featured_item){
                //get the ID fieldname....
                $link = '';
                $link_close = '';
                $class = '';
                $tooltip_output = '';
                if($feature_type == 'movies'){
                                             //remove 's'
                    $link = "<a href='add-".rtrim($feature_type,'s').".php?action=edit&id=".$featured_item[str_replace(substr($field_name, strpos($field_name, "_") + 1), 'guid', $field_name)]."'>";
                    $link_close = "</a>";
                }else{
                    $class = 'class="sidebar_tooltip cursor-pointer"';
                                            //this is declared in sidebar...this is not ideal
                    $tooltip_output = "<span class=\"tooltiptext\">". $tooltiptext ."</span>";
                }

                echo("<li $class>".$link . $featured_item[$field_name]. $link_close. $tooltip_output."</li>");
            }
        }
    ?>
</ul>
