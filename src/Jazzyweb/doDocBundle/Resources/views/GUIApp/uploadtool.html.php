<!DOCTYPE HTML>
<!--
/*
 * jQuery File Upload Plugin Demo 6.9.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
-->
<html>
    <head>
        <!-- Force latest IE rendering engine or ChromeFrame if installed -->
        <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
        <meta charset="utf-8">
        <title>Upload tool</title>
        <meta name="description" content="File Upload widget with multiple file selection, drag&amp;drop support, progress bar and preview images for jQuery. Supports cross-domain, chunked and resumable file uploads. Works with any server-side platform (Google App Engine, PHP, Python, Ruby on Rails, Java, etc.) that supports standard HTML form file uploads.">
        <meta name="viewport" content="width=device-width">
        <!-- Bootstrap CSS Toolkit styles -->
        <link rel="stylesheet" href="<?php echo $view['assets']->getUrl('bundles/jazzywebdodoc/bootstrap/css/bootstrap.css') ?>">
        <!-- Generic page styles -->
        <link rel="stylesheet" href="<?php echo $view['assets']->getUrl('bundles/jazzywebdodoc/blueimpFileUpload/css/style.css') ?>">
        <!-- Bootstrap styles for responsive website layout, supporting different screen sizes -->
        <link rel="stylesheet" href="<?php echo $view['assets']->getUrl('bundles/jazzywebdodoc/bootstrap/css/bootstrap-responsive.min.css') ?>">
        <!-- Bootstrap CSS fixes for IE6 -->
        <!--[if lt IE 7]><link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap-ie6.min.css"><![endif]-->
        <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
        <link rel="stylesheet" href="<?php echo $view['assets']->getUrl('bundles/jazzywebdodoc/blueimpFileUpload/css/jquery.fileupload-ui.css') ?>">
        <!-- Shim to make HTML5 elements usable in older Internet Explorer versions -->
        <!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    </head>
    <body>
        <!-- The file upload form used as target for the file upload widget -->
        <?php if($images): ?>       
        <form id="fileupload" action="<?php echo $view['router']->generate('JazzywebdoDocBundle_api_upload_images_post', array('dir' => $dir)) ?>" method="POST" enctype="multipart/form-data">           
         <?php else: ?>
        <form id="fileupload" action="<?php echo $view['router']->generate('JazzywebdoDocBundle_api_upload_post', array('dir' => $dir)) ?>" method="POST" enctype="multipart/form-data">           
        <?php endif; ?>
            <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
            <div class="row fileupload-buttonbar">
                <div class="span7">
                    <!-- The fileinput-button span is used to style the file input field as button -->
                    <span class="btn btn-success fileinput-button">
                        <i class="icon-plus icon-white"></i>
                        <span>Add files...</span>
                        <input type="file" name="files[]" multiple>
                    </span>
                    <button type="submit" class="btn btn-primary start">
                        <i class="icon-upload icon-white"></i>
                        <span>Start upload</span>
                    </button>
                    <button type="reset" class="btn btn-warning cancel">
                        <i class="icon-ban-circle icon-white"></i>
                        <span>Cancel upload</span>
                    </button>
                    <button type="button" class="btn btn-danger delete">
                        <i class="icon-trash icon-white"></i>
                        <span>Delete</span>
                    </button>
                    <input type="checkbox" class="toggle">
                </div>
                <!-- The global progress information -->
                <div class="span5 fileupload-progress fade">
                    <!-- The global progress bar -->
                    <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                        <div class="bar" style="width:0%;"></div>
                    </div>
                    <!-- The extended global progress information -->
                    <div class="progress-extended">&nbsp;</div>
                </div>
            </div>
            <!-- The loading indicator is shown during file processing -->
            <div class="fileupload-loading"></div>
            <br>
            <!-- The table listing the files available for upload/download -->
            <table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
        </form>

        <!-- The template to display files available for upload -->
        <script id="template-upload" type="text/x-tmpl">
            {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-upload fade">
            <td class="preview"><span class="fade"></span></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
            {% } else if (o.files.valid && !i) { %}
            <td style="width: 50px;">
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>{%=locale.fileupload.start%}</span>
                </button>
                {% } %}</td>
            {% } else { %}
            <td colspan="2"></td>
            {% } %}
            <td class="cancel">{% if (!i) { %}
                <button class="btn btn-warning">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>{%=locale.fileupload.cancel%}</span>
                </button>
                {% } %}</td>
        </tr>
        {% } %}
    </script>
    <!-- The template to display files available for download -->
    <script id="template-download" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-download fade">
            {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
            {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
                {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
            {% } %}
            <td class="delete">
                <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
                    <i class="icon-trash icon-white"></i>
                    <span>{%=locale.fileupload.destroy%}</span>
                </button>
                <input type="checkbox" name="delete" value="1">
            </td>
        </tr>
        {% } %}
    </script>


    <script src="<?php echo $view['assets']->getUrl('bundles/jazzywebdodoc/jquery/jquery.js') ?>"></script>
    <script src="<?php echo $view['assets']->getUrl('bundles/jazzywebdodoc/jquery/jquery-ui-1.8.23.custom.min.js') ?>"></script>

    <!-- The Templates plugin is included to render the upload/download listings -->
    <script src="http://blueimp.github.com/JavaScript-Templates/tmpl.min.js"></script>
    <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
    <script src="http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js"></script>
    <!-- The Canvas to Blob plugin is included for image resizing functionality -->
    <script src="http://blueimp.github.com/JavaScript-Canvas-to-Blob/canvas-to-blob.min.js"></script>
    <!-- Bootstrap JS and Bootstrap Image Gallery are not required, but included for the demo -->
    <script src="<?php echo $view['assets']->getUrl('bundles/jazzywebdodoc/bootstrap/js/bootstrap.min.js') ?>"></script>
    <script src="http://blueimp.github.com/Bootstrap-Image-Gallery/js/bootstrap-image-gallery.min.js"></script>
    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
    <script src="<?php echo $view['assets']->getUrl('bundles/jazzywebdodoc/blueimpFileUpload/js/jquery.iframe-transport.js') ?>"></script>
    <!-- The basic File Upload plugin -->
    <script src="<?php echo $view['assets']->getUrl('bundles/jazzywebdodoc/blueimpFileUpload/js/jquery.fileupload.js') ?>"></script>
    <!-- The File Upload file processing plugin -->
    <script src="<?php echo $view['assets']->getUrl('bundles/jazzywebdodoc/blueimpFileUpload/js/jquery.fileupload-fp.js') ?>"></script>
    <!-- The File Upload user interface plugin -->
    <script src="<?php echo $view['assets']->getUrl('bundles/jazzywebdodoc/blueimpFileUpload/js/jquery.fileupload-ui.js') ?>"></script>
    <!-- The localization script -->
    <script src="<?php echo $view['assets']->getUrl('bundles/jazzywebdodoc/blueimpFileUpload/js/locale.js') ?>"></script>
    <!-- The main application script -->
    <script src="<?php echo $view['assets']->getUrl('bundles/jazzywebdodoc/blueimpFileUpload/js/main.js') ?>"></script>
    <!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
    <!--[if gte IE 8]><script src="js/cors/jquery.xdr-transport.js"></script><![endif]-->
</body> 
</html>
