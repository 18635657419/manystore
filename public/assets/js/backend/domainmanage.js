define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'domainmanage/index' + location.search,
                    add_url: 'domainmanage/add',
                    edit_url: 'domainmanage/edit',
                    del_url: 'domainmanage/del',
                    multi_url: 'domainmanage/multi',
                    import_url: 'domainmanage/import',
                    table: 'domainmanage',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'domain_id',
                sortName: 'domain_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'domain_id', title: __('Domain_id')},
                        {field: 'manystore.nickname', title: __('商户昵称'), operate: 'LIKE'},
                        
                        {field: 'name', title: __('Name'), operate: 'LIKE',formatter:function (value,data) {
                            var html = '<a class="domain_id" data-domain_id='+data.domain_id+' style="clore:red;cursor:pointer">'+value+'</a>'+''
                            
                            return html;
                        }},
                        {field: 'token', title: __('Token'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"on":__('On'),"off":__('Off')}, formatter: Table.api.formatter.status},
                        {field: 'createdate', title: __('Createdate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            $(document).on("click", ".domain_id", function () {
                let domain_id = $(this).data('domain_id')
                // alert(ppaccount);    
                layer.open({
                    area:["90%","90%"],
                    type: 2, 
                    content: '/cnLyKYhOfC.php/ppaccount?domainmanage.domain_id='+domain_id+'&show_type=true' 
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