//$("#markItUp").markItUp(myMarkdownSettings);

window.documentModel = Backbone.Model.extend({
    
    url: function(){
        return config.webBasePath + '/book/' + this.get('book') + '/docs/' + this.get('name');
    }
    
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
        
        $('#btn_save_doc').addClass('disabled');
        $('#btn_del_doc').addClass('disabled');
        $('#btn_download_doc').addClass('disabled');
    },
    
    events: {
        "change #document_list_selection": "editDoc",        
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
        console.log($('#document_list_selection option:selected').text());
        
        var document = this.collection.find(function(doc){
            return doc.get('name') === $('#document_list_selection option:selected').text()
        });
        
        if(typeof(document) == 'object' ){
            $('#btn_save_doc').removeClass('disabled');
            $('#btn_del_doc').removeClass('disabled');
            $('#btn_download_doc').removeClass('disabled');
            
            var that = this;
            document.fetch({
                success: function(model, response){
                    $('#document_editor').attr('rel', model.get('name'));
                    app.editor.importFile(model.get('name'), model.get('content'));
                    app.status.currentDoc = model;
                },
                error: function(model, response){
                    that.showError(response);
                    app.editor.importFile('', '');
                    app.status.currentDoc = null;
                }
            });
        }else{
            $('#document_editor').attr('rel', '-- Select a document to work with --');
            app.editor.importFile('', '');
            app.status.currentDoc = null;
            $('#btn_save_doc').addClass('disabled');
            $('#btn_del_doc').addClass('disabled');
            $('#btn_download_doc').addClass('disabled');
            
        }                    
    },
       
    saveDoc: function(){
        console.log('documentTools.saveDoc');  
                        
        if(app.status.currentDoc)
        {
            $('#btn_save_doc').addClass('disabled');
            $('#notifications').addClass('label notification');
            $('#notifications').html('saving document');
            $('#ajax-loader').show();
            
            var content = app.editor.exportFile();
            var that = this;
            app.status.currentDoc.set('content', content);
            app.status.currentDoc.save({},{
                success: function(){
                    $('#btn_save_doc').removeClass('disabled');
                    $('#notifications').removeClass('label notification');
                    $('#notifications').html('');
                    $('#ajax-loader').hide();
                },
                error: function(model, response){
                    that.showError(response);
                    $('#btn_save_doc').removeClass('disabled');
                    $('#notifications').removeClass('label notification');
                    $('#notifications').hide();
                }
            });
        }        
    },
    
    deleteDoc: function(){
        console.log('documentTools.deleteDoc');          
        
        var that = this;
        
        if(app.status.currentDoc)
        {
            $('#btn_del_doc').addClass('disabled');
            $('#notifications').addClass('label notification');
            $('#notifications').html('removing document');
            $('#ajax-loader').show();
            
            app.status.currentDoc.destroy({
                success: function(){
                    $('#btn_del_doc').removeClass('disabled');
                    $('#notifications').removeClass('label notification');
                    $('#notifications').html('');
                    $('#ajax-loader').hide();
                    
                    that.collection.fetch();
                },
                
                error: function(){
                    that.showError(response);
                    $('#btn_del_doc').removeClass('disabled');
                    $('#notifications').removeClass('label notification');
                    $('#notifications').hide();
                }
            });
        }
    },
    
    showError: function(response){
        jsonResponse = jQuery.parseJSON(response.responseText);
        $('#msg_error_doctool').html(jsonResponse.message);
        $('#alert_document_tools').toggleClass('hide');        
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
        
        this.status = {
            currentDoc: null,
            currentBook: $('#book_code').val()
        }
        
    }
   
});

var app = new window.appRouter;