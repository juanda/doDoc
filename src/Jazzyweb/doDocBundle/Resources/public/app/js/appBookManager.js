

window.bookModel = Backbone.Model.extend({
    
    url: function(){
    //        return config.webBasePath + '/book/' + this.get('book') + '/docs/' + this.get('name');
    }
});
window.bookCollection = Backbone.Collection.extend({
      
    });


jQuery("#books").jqGrid({
    url:config.webBasePath + '/books', 
    datatype: "json", 
    colNames:['Inv No','Date', 'Client', 'Amount','Tax','Total','Notes'], 
    colModel:[ {
        name:'id',
        index:'id', 
        width:55
    }, {
        name:'invdate',
        index:'invdate', 
        width:90, 
        editable:true
    }, {
        name:'name',
        index:'name', 
        width:100,
        editable:true
    }, {
        name:'amount',
        index:'amount', 
        width:80, 
        align:"right",
        editable:true
    }, {
        name:'tax',
        index:'tax', 
        width:80, 
        align:"right",
        editable:true
    }, {
        name:'total',
        index:'total', 
        width:80,
        align:"right",
        editable:true
    }, {
        name:'note',
        index:'note', 
        width:150, 
        sortable:false,
        editable:true,
        formatter:'showlink', 
        formatoptions:{baseLinkUrl:'someurl.php', addParam: '&action=edit'}

    } ], 
    rowNum:10, 
    rowList:[10,20,30], 
    pager: '#pager', 
    sortname: 'id', 
    viewrecords: true, 
    sortorder: "desc", 
    editurl: "server.php", 
    caption: "Using navigator"
});
jQuery("#books").jqGrid('navGrid',"#pager",{
    edit:false,
    add:false,
    del:false
});
jQuery("#books").jqGrid('inlineNav',"#pager");



