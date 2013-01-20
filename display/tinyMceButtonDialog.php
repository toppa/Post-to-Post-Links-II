<!DOCTYPE html>
<head>
    <title>Post to Post Links</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
    <script type="text/javascript" src="../../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
    <link rel="stylesheet" href="../../../../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/dialog.css">
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/smoothness/jquery-ui.css">
    <style>
        #buttonable_p2p_form th {
            text-align: left;
            font-weight: normal;
            font-size: 1.2em;
            padding: 3px 0;
        }

        #buttonable_p2p_form input {
            font-size: 1.2em;
            padding: 1px;
        }

        #buttonable_p2p_form input[type="submit"] {
            padding: 5px;
        }

        #buttonable_p2p_form select {
            font-size: 1.08em;
        }
    </style>
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
