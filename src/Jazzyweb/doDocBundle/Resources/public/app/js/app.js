//$("#markItUp").markItUp(myMarkdownSettings);

window.documentModel = Backbone.Model.extend({
    
    url: function(){
        return config.webBasePath + '/book/' + this.get('book') + '/docs/' + this.get('name');
    }
    
});

window.alertModel = Backbone.Model.extend();

window.documentCollection = Backbone.Collection.extend({
    
    model: window.documentModel,
    
    initialize: function(models, options) {
        this.url = options.url_service + "/book/" + options.bookcode + "/docs";
    }        
});

window.bookTools = Backbone.View.extend({   
    
    el: $('#book_tools'),
    
    events: {
        "click  #btn_config_book": "configTool",
        "click .build_option":    "buildBook"
        
    },
    
    configTool: function(){
        console.log('bookTools.configTool');
    },
    
    buildBook: function(a){
        console.log('bookTools.buildBook.' + a.currentTarget.name);
        
        
    }    
});

window.documentTools = Backbone.View.extend({   
    
    el: $('#document_tools'),
    
    initialize: function(){
        
        this.collection.bind('reset destroy add', this.render,this);
        this.collection.bind('add', this.render_add,this);
        
        this.updateDocumentList();
        
        window.disableDocumentTools();
    },
    
    events: {
        "change #document_list_selection": "editDoc",        
        "click #btn_save_doc": "saveDoc",
        "click #btn_del_doc": "askDeleteDoc",
        "click .download_option": "downloadDoc",
        "click #btn_delete_ok"  : "deleteDoc",
        "click #btn_delete_cancel"  : "cancelDeleteDoc"
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
    
    render_add: function()
    {
        this.render();
        
        var addedDoc = this.collection.last();
        
        $('#'+addedDoc.get('name')).attr('selected', 'selected');
        window.changeDocTitle(addedDoc.get('name'));
        
        app.status.currentDoc = addedDoc;

        
    },
    
    updateDocumentList: function(){
        
        window.showNotification('label-info', 'Loading book documents ...');
        
        this.collection.fetch({
            success: function(collection, response){
                console.log('documentTools.initialize.fetch.success');
                
                var message = 'Opening book: ' 
                + app.status.currentBook 
                + ', '
                + collection.size() 
                + ' documents have been loaded';
                window.showAlert('alert-success', message);
                window.hideNotification();
            },
            error: function(collection, response){
                console.log(response);
                console.log('documentTools.initialize.fetch.error');
                jsonResponse = jQuery.parseJSON(response.responseText);
                
                window.showAlert('alert-error', jsonResponse.message);
                window.hideNotification();       
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
            
            window.showNotification('label-info', 'Loading document ...');
            window.disableDocumentTools();
            
            document.fetch({
                success: function(model, response){
                    window.changeDocTitle(model.get('name'));
                    app.editor.importFile(model.get('name'), model.get('content'));
                    app.status.currentDoc = model;
                                    
                    var message = 'Document ' + response.name + ' has been loaded';
                    window.showAlert('alert-success', message);
                    window.hideNotification();
                    window.enableDocumentTools();
                    
                },
                error: function(model, response){
                    
                    app.editor.importFile('', '');
                    app.status.currentDoc = null;
                    
                    jsonResponse = jQuery.parseJSON(response.responseText);
                    var message = jsonResponse.message;
                    window.showAlert('alert-error', message);
                    window.hideNotification();
                    window.enableDocumentTools();
                }
            });
        }else{
            window.changeDocTitle('-- Select a document to work with --');
            app.editor.importFile('', '');
            app.status.currentDoc = null;
            window.ebleDocumentTools();
            
        }                    
    },
       
    saveDoc: function(){
        console.log('documentTools.saveDoc');  
                        
        if(app.status.currentDoc)
        {         
            window.showNotification('label-info', 'Saving document ...');
            window.disableDocumentTools();
            
            var content = app.editor.exportFile();
          
            app.status.currentDoc.set('content', content);
            app.status.currentDoc.save({},{
                success: function(model, response){
                    var message = 'Document ' + response.name + ' has been saved';
                    
                    window.showAlert('alert-success', message);
                    window.hideNotification();
                    window.enableDocumentTools();
                },
                error: function(model, response){
                    jsonResponse = jQuery.parseJSON(response.responseText);
                    var message = jsonResponse.message;
                    window.showAlert('alert-error', message);
                    window.hideNotification();
                    window.enableDocumentTools();
                }
            });
        }        
    },
    
    askDeleteDoc: function(){
        $('#modal_ask_delete_doc').modal('show'); 
        if(typeof(app.status.currentDoc) == 'object')
        {
            var message = 'You are about to delete the document: ' + app.status.currentDoc.get('name');
            $('#txt_ask_delete_doc').html(message);
            
        }
             
    },
    
    cancelDeleteDoc: function(){
        $('#modal_ask_delete_doc').modal('hide');
    },
    
    deleteDoc: function(){
        console.log('documentTools.deleteDoc');          
        
        $('#modal_ask_delete_doc').modal('hide');   
        var that = this;
        
        if(app.status.currentDoc)
        {
            window.disableDocumentTools();
            window.showNotification('label-info', 'removing ...');
            
            app.status.currentDoc.destroy({
                success: function(model, response){
                    var message = response.message;
                    app.editor.importFile('','');
                    
                    window.showAlert('alert-success', message);
                    window.hideNotification();
                    window.enableDocumentTools();
                    window.changeDocTitle('-- Select a document to edit --');
                    
                },
                
                error: function(model, response){
                    var jsonResponse = jQuery.parseJSON(response.responseText);
                    var message = "<strong>Error:</strong> " + jsonResponse.message;
                
                    window.showAlert('alert-error', message);
                    window.hideNotification();
                    window.enableDocumentTools();
                }
            });
        }
    },   
    
    downloadDoc: function(a){
        console.log('documentTools.downloadDoc.' + a.currentTarget.id);
    }
        
});

window.genericTools = Backbone.View.extend({   
    
    el: $('#generic_tools'),
          
    initialize: function(){
        $('#form_new_doc').on('shown', function () {
            console.log('genericTools showing modal');
            $('#txt_new_doc').focus();
        });
        
        $('#upload_tool').on('hidden', function () {
            console.log('genericTools hidding modal');
            app.documentCollection.fetch();
        });
    },
    
    events: {
        "click #btn_new_doc":        "newDoc",
        "click #btn_name_new_doc":   "createNewDoc",
        "click #btn_upload_doc":     "uploadDoc",
        "click #btn_image_manager":  "imageManagerTool",
        "click #btn_toggle_preview": "togglePreview",
        "click #btn_full_screen":    "fullScreen",        
        "keydown #txt_new_doc": "enterDoc"       
    },
    
    newDoc: function(){
        console.log('genericTools.newDoc');
        
        $('#form_new_doc').modal('show');        
        $('#txt_new_doc').val('');
        $('#txt_new_doc', this).focus();                      
    },
    
    createNewDoc: function(){
        console.log('genericTools.createNewDoc');
        
        that = this;
        
        var document = new window.documentModel(
        {
            name: $('#txt_new_doc').val(),
            book: app.status.currentBook
        });
        
        $('#form_new_doc').modal('hide');
        window.showNotification('label-info','Creating document ...');
        window.disableDocumentTools();
        document.save(
        {},
        {
            success: function(model,response){
                var message = "The document " + response.name + " has been created";
                
                that.collection.add(model);
                app.editor.importFile('','');
                
                window.showAlert('alert-success', message);
                window.enableDocumentTools();
                window.hideNotification();
            },
            error: function(model,response){
                var jsonResponse = jQuery.parseJSON(response.responseText);
                var message = "<strong>Error:</strong> " + jsonResponse.message;
                
                window.showAlert('alert-error', message);
                window.enableDocumentTools();
                window.hideNotification();                
            }
        }); 
    },
    
    uploadDoc: function(){
        console.log('genericTools.uploadDoc');
        console.log('/doDoc/web/app_dev.php/uploadtool/' + app.status.currentBook);
        
        $('#txt_title_upload_tool').html('Upload and manage documents');
        $('#frm_upload_tool').attr('src','/doDoc/web/app_dev.php/uploadtool/gui/' + app.status.currentBook);
       
        $('#upload_tool').modal('show');
    },      
    
    imageManagerTool: function(){
        console.log('genericTools.imageManagerTool');
        
        $('#txt_title_upload_tool').html('Upload and manage images');
        $('#frm_upload_tool').attr('src','/doDoc/web/app_dev.php/uploadtool/gui/images/' + app.status.currentBook);
        $('#upload_tool').modal('show');
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
    },

    enterDoc: function(e){
        console.log('documentTools.enterDoc');
        
        if(e.keyCode == 13)
        {
            $('#btn_name_new_doc').click();
        }
    }

});

window.alertView = Backbone.View.extend({

    el: $('#alerts'),
    
    template: _.template($('#t_alerts').html()),
    
    initialize: function(){
        
        this.model.bind('change', this.render, this);
    },
    
    render: function(){
        console.log('alertView.render');
             
        this.$el.empty();
        
        this.$el.removeClass('hide');
        
        this.$el.html(this.template(this.model.toJSON()));
        
        return this;
    }
  
});

window.showAlert = function(t, m){
    app.alert.set(
    {
        type: t, 
        message: m
    });
    
    // To show the alert view each time this function is called,
    // the ``change`` event must triggered even if the alert object
    // hasn't changed. This can occurs from time to time. For instance
    // when the user save the same document two or more times.
    // So we trigger  it manually
    app.alert.trigger('change');
    
    // Close the alert after 5 seconds
    window.setTimeout(function() {
        $(".alert").alert('close');
    }, 5000);
}

window.showNotification = function(t, m){
    
    $('#notifications').removeClass();
    $('#notifications').addClass('label ' + t);
    $('#notifications').html(m);
    $('#ajax-loader').show();
}

window.hideNotification = function(){
   
    $('#notifications').addClass('hide');
    $('#ajax-loader').hide();
}

window.disableDocumentTools = function(){
    $('#btn_save_doc').addClass('disabled');
    $('#btn_del_doc').addClass('disabled');
    $('#btn_download_doc').addClass('disabled');
}

window.enableDocumentTools = function(){
    $('#btn_save_doc').removeClass('disabled');
    $('#btn_del_doc').removeClass('disabled');
    $('#btn_download_doc').removeClass('disabled');
}

window.changeDocTitle = function(title){
    $('#document_editor_container').attr('rel', title);
};

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
        
        this.editor.importFile('','');
        
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
        this.genericToolsView  = new window.genericTools({
            collection: this.documentCollection
        }); 
        
        this.alert = new window.alertModel;
        this.alertView = new window.alertView({
            model: this.alert
        });
        
        this.status = {
            currentDoc: new window.documentModel,
            currentBook: $('#book_code').val()
        }
    }
   
});

var app = new window.appRouter;
