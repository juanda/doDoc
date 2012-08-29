//$("#markItUp").markItUp(myMarkdownSettings);

window.opts = {
    container: 'epiceditor',
    basePath: '/doDoc/web/bundles/jazzywebdodoc/epiceditor',
    focusOnLoad: true
}

window.editor = new EpicEditor(window.opts).load();

window.bookTools = Backbone.View.extend({   
    
    el: $('#book_tools')

});

window.documentTools = Backbone.View.extend({   
    
    el: $('#document_tools'),
    
    events: {
        "click #btn_edit_doc":  "open" 
    },
    
    open: function(){
        console.log('kuku');
    }

});

window.generalTools = Backbone.View.extend({   
    
    el: $('#generic_tools')
});


window.app = Backbone.Router.extend({
   
   initialize: function(){
       
       console.log('window.app.initialize');
       this.documentToolView = new window.documentTools;
   }
   
});

var app = new window.app;