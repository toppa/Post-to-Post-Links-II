jQuery(document).ready(function($) {
    tinymce.create('tinymce.plugins.P2pPlugin', {
        init : function(ed, url) {
            ed.addCommand('p2p', function() {
                ed.windowManager.open({
                    file : url + '/tinyMceButtonDialog.php',
                    title : 'Post to Post Links',
                    width : 500,
                    height : 350,
                    inline : 'yes',
                    resizable : 'yes'
                }, {
                    plugin_url : url
                });
            });

            ed.addButton('p2p', {
                title : 'Post to Post Link',
                cmd : 'p2p',
                image: url + '/p2p-button.png'
            });
        },
        getInfo : function() {
            return {
                longname : 'Post to Post Links',
                author : 'Michael Toppa',
                authorurl : 'http://www.toppa.com',
                infourl : 'http://www.toppa.com',
                version : "1.0"
            };
        }
    });

    tinymce.PluginManager.add('p2p', tinymce.plugins.P2pPlugin);
});

