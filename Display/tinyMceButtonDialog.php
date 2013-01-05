<!DOCTYPE html>
<head>
    <title>Post to Post Links</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="../../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
    <link rel="stylesheet" href="../../../../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/dialog.css">
    <style>
        .p2p_title {
            text-align: left;
            font-weight: normal;
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            $('#buttonable_p2p_form').submit(function(ev) {
                if (!$('#buttonable_p2p_value').val()) {
                    alert('Please enter a slug or ID');
                    return false;
                }

                var attributes = $('#buttonable_p2p_attributes').val().replace(/"/g, "'");

                var replace = '[p2p type="' + $('#buttonable_p2p_type').val()
                    + '" value="' + $('#buttonable_p2p_value').val() + '"'
                    + ($('#buttonable_p2p_anchor').val() ? (' anchor="' + $('#buttonable_p2p_anchor').val() + '"') : '')
                    + (attributes ? (' attributes="' + attributes + '"') : '')
                    + ']'
                    + tinyMCE.activeEditor.selection.getContent()
                    + '[/p2p]';
                tinyMCEPopup.execCommand('mceReplaceContent', false, replace);
                tinyMCEPopup.close();
                ev.preventDefault();
            });
        });
    </script>
</head>
<body>
<?php
    // when called through TincyMCE, the WordPress _e function
    // used in buttonDialogs.html is not defined
    if (!function_exists('_e')) {
        function _e($text, $namespace) {
            echo $text;
        }
    }

    include_once('buttonDialog.html');
?>
</body>
</html>
