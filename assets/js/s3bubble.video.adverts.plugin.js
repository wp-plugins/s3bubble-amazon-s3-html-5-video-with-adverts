(function() {
    tinymce.create('tinymce.plugins.S3bubbleVideoAdverts', {
        init : function(ed, url) {
            
            /*
             * S3bubble audio playlist
             */
            ed.addButton('s3bubble_video_adverts_wysiwyg_video_shortcode', {
                title : 'Generate S3Bubble Video Advert Shortcode',
                cmd : 's3bubble_video_adverts_wysiwyg_video_shortcode',
                image : url + '/s3bubbletiny_video_single.png'
            });
            ed.addCommand('s3bubble_video_adverts_wysiwyg_video_shortcode', function() {
            	tb_show('Generate S3Bubble Video Advert Shortcode', 'admin-ajax.php?action=s3bubble_video_adverts_wysiwyg_ajax');
            });

        },
    });
    // Register plugin
    tinymce.PluginManager.add( 's3bubbleVideoAdverts', tinymce.plugins.S3bubbleVideoAdverts );
})();