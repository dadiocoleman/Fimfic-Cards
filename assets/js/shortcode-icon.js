jQuery(document).ready(function($) {	
    tinymce.create('tinymce.plugins.fimfic_plugin', {		        init : function(ed, url) {			
                ed.addCommand('fimfic_insert_shortcode', function() {
                    selected = tinyMCE.activeEditor.selection.getContent();
                        if( selected ){
                        content =  '[fimfic source="'+selected+'"]';
						} else {
							content =  '[fimfic source=""]';
						}
                    tinymce.execCommand('mceInsertContent', false, content);
                });
            ed.addButton('fimfic_button', {title : 'Insert a shortcode for fimfiction card.', cmd : 'fimfic_insert_shortcode', image: url + '/shortcode-icon.png' });
        },   
    });
    tinymce.PluginManager.add('fimfic_button', tinymce.plugins.fimfic_plugin);
});