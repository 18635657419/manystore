define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ppaccount/index' + location.search,
                    add_url: 'ppaccount/add',
                    edit_url: 'ppaccount/edit',
                    del_url: 'ppaccount/del',
                    multi_url: 'ppaccount/multi',
                    import_url: 'ppaccount/import',
                    table: 'ppaccount',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'pp_id',
                sortName: 'pp_id',
                searchFormVisible:true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'pp_id', title: __('Pp_id')},
                        {field: 'ppaccount', title: __('Ppaccount'), operate: 'LIKE'},
                        {field: 'b_domain', title: __('B站域名'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"on":__('Status on'),"off":__('Status off'),"limited":__('Status limited'),"limited180":__('Status limited180'),"offline":__('Status Offline')}, formatter: Table.api.formatter.status},
                        {field: 'totalorder', title: __('Totalorder'), operate:'BETWEEN'},
                        {field: 'totalamount', title: __('Totalamount'), operate:'BETWEEN'},
                        {field: 'orderbyday', title: __('Orderbyday'), operate:'BETWEEN'},
                        {field: 'amountbyday', title: __('Amountbyday'), operate:'BETWEEN'},
                        {field: 'createdate', title: __('Createdate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'domainmanage.name', title: __('域名'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
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
