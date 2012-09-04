{% extends 'JazzywebdoDocBundle:GUIApp:layout.html.twig' %}

{% block stylesheets %}

 {{ parent() }}

<link href="{{ asset('bundles/jazzywebdodoc/blueimpFileUpload/css/jquery.fileupload-ui.css') }}" type="text/css" rel="stylesheet" />

{% endblock%}

{% block body %}
<form id="fileupload" action="{{ url('JazzywebdoDocBundle_api_upload_post', {'book': book_code }) }}" method="POST" enctype="multipart/form-data">
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

<div id="tmpls"></div>


{% endblock %}

{% block javascripts %}
<script type="text/javascript" src="{{ asset('bundles/jazzywebdodoc/app/js/upload.tmpl.js') }}"></script>  

<script type="text/javascript" src="{{ asset('bundles/jazzywebdodoc/jquery/jquery.js') }}"></script>  
<script type="text/javascript" src="{{ asset('bundles/jazzywebdodoc/jquery/jquery-ui-1.8.23.custom.min.js') }}"></script>    

<!-- The Templates plugin is included to render the upload/download listings -->
<script src="http://blueimp.github.com/JavaScript-Templates/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="http://blueimp.github.com/JavaScript-Canvas-to-Blob/canvas-to-blob.min.js"></script>
<script type="text/javascript" src="{{ asset('bundles/jazzywebdodoc/bootstrap/js/bootstrap.min.js') }}"></script>    
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="{{ asset('bundles/jazzywebdodoc/blueimpFileUpload/js/jquery.iframe-transport.js')}}"></script>
<!-- The basic File Upload plugin -->
<script src="{{ asset('bundles/jazzywebdodoc/blueimpFileUpload/js/jquery.fileupload.js')}}"></script>
<!-- The File Upload file processing plugin -->
<script src="{{ asset('bundles/jazzywebdodoc/blueimpFileUpload/js/jquery.fileupload-fp.js')}}"></script>
<!-- The File Upload user interface plugin -->
<script src="{{ asset('bundles/jazzywebdodoc/blueimpFileUpload/js/jquery.fileupload-ui.js')}}"></script>
<!-- The localization script -->
<script src="{{ asset('bundles/jazzywebdodoc/blueimpFileUpload/js/locale.js')}}"></script>
<!-- The main application script -->
<script src="{{ asset('bundles/jazzywebdodoc/blueimpFileUpload/js/main.js')}}"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="js/cors/jquery.xdr-transport.js"></script><![endif]-->




{% endblock %}


