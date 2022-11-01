<!-- Jquery Plugins (datepicker, chosen, & confirm) JS below -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js" integrity="sha512-RCgrAvvoLpP7KVgTkTctrUdv7C6t7Un3p1iaoPr1++3pybCyCsCZZN7QEHMZTcJTmcJ7jzexTO+eFpHk4OCFAg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js" integrity="sha512-rMGGF4wg1R73ehtnxXBt5mbUfN9JUJwbk21KMlnLZDJh7BkPmeovBuddZCENJddHYYMkCh9hPFnPmS9sspki8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js" integrity="sha512-zlWWyZq71UMApAjih4WkaRpikgY9Bz1oXIW5G0fED4vk14JjGlQ1UmkGM392jEULP8jbNMiwLWdM8Z87Hu88Fw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-filestyle/2.1.0/bootstrap-filestyle.min.js" integrity="sha512-HfRdzrvve5p31VKjxBhIaDhBqreRXt4SX3i3Iv7bhuoeJY47gJtFTRWKUpjk8RUkLtKZUhf87ONcKONAROhvIw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- //End Jquery plugins -->
<script language="JavaScript">
    <?php
    if(appSession::exists('success-message')){
    ?>
    $.toast({
        heading: 'Information',
        text: "<?php echo(appSession::get('success-message')); ?>",
        icon: 'info',
        loader: true,        // Change it to false to disable loader
        loaderBg: '#9EC600',  // To change the background
        position: 'bottom-right'
    })
    <?php
    }
    appSession::destroy('success-message');
    ?>
    $('.delete-movie').click(function (){
        var movie_id = $(this).attr('data-movie-id');
        $.confirm({
            title: 'Confirm Deletion',
            content: 'Are you sure you want to delete this movie?',
            buttons: {
                cancel: function(){
                    //do nothing.
                },
                yes: {
                    text: 'Yes',
                    btnClass: 'btn-blue',
                    keys: ['enter', 'shift'],
                    action: function (){
                        window.location.href='list-movies.php?action=delete-movie&id=' + movie_id;
                    }
                }
            }
        });
    });

    $(function(){
        $("#per_page").change(function(){
            window.location='list-movies.php?per_page=' + this.value
        });
    });
    $('input[type="checkbox"][name="mv_featured"]').on('change', function(){
        //oh boy, a chance to use the bitwise XOR operator ;-)
        this.value ^= 1;
        $('input[type="checkbox"][name="mv_featured"]').attr("checked", !$('input[type="checkbox"][name="mv_featured"]').attr("checked"));

    });
    //TODO:this reused code could be optimized
    $("#movie-cover-upload-button").on('click', function (){
        $("#customFileInput").click();
        $("#customFileInput").removeClass('hidden');
    });
    $("#movie-hero-upload-button").on('click', function (){
        $("#customFileHeroInput").click();
        $("#customFileHeroInput").removeClass('hidden');
    });
    //
    $("#cover-x, #hero-x").on('click', function (event){
        if($(this).attr('id') == 'cover-x'){
            selector = "#customFileInput";
            selector_img_container = "#cover-img-input-container";
        }else{
            //hero-x
            selector = "#customFileHeroInput";
            selector_img_container = "#hero-img-input-container";
        }
        //hide the 'remove' button
        $(this).hide();
        //now the current file & image need to be removed
        $(selector).val('');
        $(selector + "-img").hide('slow');
        $(selector + "-img").attr('src','');
        $(selector).removeClass('hidden');
        //now hide the button and label for image replacement
        $(selector_img_container + " .custom-file-label, " + selector_img_container + " .input-group-append").hide();
    });


    function readURL(input,selector) {
        //use this to decide which remove button to add back
        if(selector == "customFileInput"){
            selector_img_container = "cover";
        }else{
            //hero remover
            selector_img_container = "hero";
        }
        selector_img_remove = "#" + selector_img_container + '-x';
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#' + selector + '-img').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
            console.log("#" + selector_img_container + "-img-input-container .image-container");
            $("#" + selector_img_container + "-img-input-container .image-container").removeClass('hidden');
            $("#" + selector_img_container + "-img-input-container .image-container img").removeClass('hidden').addClass('test');
            $("#" + selector_img_container + "-img-input-container .image-container img").css("display","block");
            $(selector_img_remove).show();
        }
    }

    $("#customFileInput , #customFileHeroInput").change(function(){
        readURL(this,$(this).attr('id'));
    });
</script>
</body>
</html>