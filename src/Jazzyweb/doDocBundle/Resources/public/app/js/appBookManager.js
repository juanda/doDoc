window.bookModel = Backbone.Model.extend({
    
    url: function(){
    //        return config.webBasePath + '/book/' + this.get('book') + '/docs/' + this.get('name');
    }
});
window.bookCollection = Backbone.Collection.extend({
      
    });

jQuery(document).ready(function(){ 
    jQuery("#books").jqGrid({
        url: config.webBasePath + '/books',
        datatype: "json",
        mtype: 'GET',
        colNames:[ 'Name', 'slug', 'Description'],
        colModel:[
        {
            name:'name',
            index:'name', 
            width:55, 
            sortable:false, 
            editable:false, 
            editoptions:{
                readonly:true,
                size:10
            }
        },

        {
            name:'slug',
            index:'slug', 
            width:200,
            editable:false
        },

        {
            name:'description',
            index:'description', 
            width:200,
            editable:true
        },
        
        ],
        jsonReader : {
            repeatitems:false
        },
        rowNum:10,
        rowList:[10,20,30],
        pager: jQuery('#gridpager'),
        sortname: 'name',
        viewrecords: true,
        sortorder: "asc",
        caption:"Books",
        editurl:"/jqGridModel?model=Wine"
    })
});




