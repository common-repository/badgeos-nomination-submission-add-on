(function($){

    $(window).load(function(){
        $('.badgeos_comment').each(function(){
            //Load ckeditor with multiple instances
            var editor_id = $(this).attr('id');
            if(editor_id!=null || editor_id!=undefined){
                CKEDITOR.replace(editor_id);
            }
        });

        //submission content
        if($('#badgeos_submission_content').length>0) {
            CKEDITOR.replace('badgeos_submission_content');
        }

        $( '.badgeos_nomination_textarea' ).each( function() {
            var textarea_id = $(this).attr('id');
            
            //nomination content
            if( $( '#' + textarea_id ).length > 0 ) {
                CKEDITOR.replace( textarea_id );
            }
        });

        $('.badgeos_nomination_btn').click( function( e ){
            console.log( $(this).parents('form') );
        });
        $("span.cke_toolbox .cke_toolbar_break").css('clear','inherit');

    });

})(jQuery);