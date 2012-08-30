//$("#markItUp").markItUp(myMarkdownSettings);

window.documentModel = Backbone.Model.extend({
    
    });

window.documentCollection = Backbone.Collection.extend({
    
    model: window.documentModel,
    
    initialize: function(models, options) {
        this.url = options.url_service + "/book/" + options.bookcode + "/docs";
    }        
});

window.bookTools = Backbone.View.extend({   
    
    el: $('#book_tools'),
    
    events: {
        "click #btn_config_book": "configTool",
        "click .build_option":    "buildBook" 
    },
    
    configTool: function(){
        console.log('bookTools.configTool');
    },
    
    buildBook: function(a){
        console.log('documentTools.buildBook.' + a.currentTarget.id);
    }    
});

window.documentTools = Backbone.View.extend({   
    
    el: $('#document_tools'),
    
    initialize: function(){
        
        this.collection.bind('reset', this.render,this);
        
        this.updateDocumentList();
    },
    
    events: {
        "click #btn_edit_doc": "editDoc",
        "click #btn_save_doc": "saveDoc",
        "click #btn_del_doc": "deleteDoc",
        "click .download_option": "downloadDoc"
    },
    
    template: _.template($('#t_document_list').html()),
    
    render: function(){
        console.log('documentTools.render');
       
        console.log({
            documents: this.collection.toJSON()
        });
        
        //this.$el.empty();
        
        this.$el.find('#document_list_selection').html(this.template({
            documents: this.collection.toJSON()
        }));
        
        return this;
    },
    
    updateDocumentList: function(){
        this.collection.fetch({
            success: function(collection, response){
                console.log('documentTools.initialize.fetch.success');
            },
            error: function(collection, response){
                console.log(response);
                console.log('documentTools.initialize.fetch.error');
                jsonResponse = jQuery.parseJSON(response.responseText);

                $('#msg_error').html(jsonResponse.message);
                $('#alert_book_tools').toggleClass('hide');
            }
        });
    },
    
    editDoc: function(){
        console.log('documentTools.editDoc');
        this.updateDocumentList();
    },
    
    saveDoc: function(){
        console.log('documentTools.saveDoc');
    },
    
    deleteDoc: function(){
        console.log('documentTools.deleteDoc');
    },
    
    downloadDoc: function(a){
        console.log('documentTools.downloadDoc.' + a.currentTarget.id);
    }

});

window.genericTools = Backbone.View.extend({   
    
    el: $('#generic_tools'),
           
    events: {
        "click #btn_new_doc":        "newDoc",
        "click #btn_upload_doc":     "uploadDoc",
        "click #btn_image_manager":  "imageManagerTool",
        "click #btn_toggle_preview": "togglePreview",
        "click #btn_full_screen":    "fullScreen"        
    },
    
    newDoc: function(){
        console.log('genericTools.newDoc');
    },
    
    uploadDoc: function(){
        console.log('genericTools.uploadDoc');
    },
    
    imageManagerTool: function(){
        console.log('genericTools.imageManagerTool');
    },
    
    togglePreview: function(a){        
        console.log('genericTools.togglePreview');
        
        if($(a.currentTarget).hasClass('active')){            
            app.editor.edit();
            // It must be added the class `active` due to 
            // a "strange" interaction with the preview event 
            // set in the appRouter
            $(a.currentTarget).addClass('active');
        }else{
            app.editor.preview();
            // It must be added the class `active` due to 
            // a "strange" interaction with the edit event 
            // set in the appRouter
            $(a.currentTarget).removeClass('active');
        }                
    },
    
    fullScreen: function(){
        console.log('genericTools.fullScreen');
        app.editor._goFullscreen();
    }        
});


window.appRouter = Backbone.Router.extend({
   
    initialize: function(){
       
        console.log('window.app.initialize');
                
        this.editor = new EpicEditor(config.epiceditor).load();
        
        this.editor.on('preview', function(){
            
            $('#btn_toggle_preview').addClass('active');
            
        });
        
        this.editor.on('edit', function(){
           
            $('#btn_toggle_preview').removeClass('active');
            
        });  
        
        this.documentCollection = new window.documentCollection(
        {},
        {
            url_service: config.webBasePath,
            bookcode: $('#book_code').val()
        });
                        
        this.bookToolsView     = new window.bookTools;
        this.documentToolsView = new window.documentTools({
            collection: this.documentCollection
        });
        this.genericToolsView  = new window.genericTools;                
    }
   
});

var app = new window.appRouter;