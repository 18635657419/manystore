define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ppblock/index' + location.search,
                    add_url: 'ppblock/add',
                    edit_url: 'ppblock/edit',
                    del_url: 'ppblock/del',
                    multi_url: 'ppblock/multi',
                    import_url: 'ppblock/import',
                    table: 'ppblock',
                }
            });

            var table = $("#table");
          

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                searchFormVisible:true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('id')},
                        {field: 'block_number', title: __('批号'), operate: 'like',formatter:function (value,data) {
                            var html = '<a class="ppaccount" data-block_number='+value+' style="clore:red;cursor:pointer">'+value+'</a>'+''
                            return html;
                        }},
                        {field: 'qty', title: __('导入数量'), operate: 'BETWEEN'},
                        {field: 'domainmanage.name', title: __('域名'),searchable:false,},
                        {field: 'remarks', title: __('备注'), operate: 'like'},
                        {field: 'createdate', title: __('创建时间'), operate:'RANGE',  autocomplete:false,addclass:'datetimerange',},

                     
                    ]
                ]
            });

            $(document).on("click", ".ppaccount", function () {
                let block_number = $(this).data('block_number')
                // alert(ppaccount);
                layer.open({
                    area:["90%","90%"],
                    type: 2, 
                    content: 'http://dev.paymentcc.com/cnLyKYhOfC.php/ppaccount?block_number='+block_number+'&show_type=true' 
                }); 
                
            });
           
           

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
