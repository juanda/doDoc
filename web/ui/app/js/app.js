//$("#markItUp").markItUp(myMarkdownSettings);

var editor = new EpicEditor().load();

var btn_edit_doc = $('#btn_edit_doc');

btn_edit_doc.click(function () {
    $.get('http://localhost/kuku.html', function(data) {
        editor.importFile('kuku', data); //Imports a file when the user clicks this button
    });
    
  
});
