define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ppstatistics/index' + location.search,
                    add_url: 'ppstatistics/add',
                    edit_url: 'ppstatistics/edit',
                    del_url: 'ppstatistics/del',
                    multi_url: 'ppstatistics/multi',
                    import_url: 'ppstatistics/import',
                    table: 'ppstatistics',
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
                        {field: 'ppaccount.ppaccount', title: __('pp账号'), operate: 'like'},
                        {field: 'order_qty', title: __('order_qty'), operate: 'BETWEEN'},
                        {field: 'order_total', title: __('order_total'), operate: 'BETWEEN'},
                        {field: 'success_order_qty', title: __('success_order_qty'), operate: 'BETWEEN'},
                        {field: 'success_total', title: __('success_total'), operate: 'BETWEEN'},
                        {field: 'error_order_qty', title: __('error_order_qty'), operate: 'BETWEEN'},
                        {field: 'error_total', title: __('error_total'), operate: 'BETWEEN'},
                        {field: 'unpaid_order_qty', title: __('unpaid_order_qty'), operate: 'BETWEEN'},
                        {field: 'unpaid_total', title: __('unpaid_total'), operate: 'BETWEEN'},
                        {field: 'first_order_no', title: __('first_order_no'), operate: 'BETWEEN'},
                        {field: 'first_order_time', title: __('first_order_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'end_order_no', title: __('end_order_no'), operate: 'BETWEEN'},
                        {field: 'end_order_time', title: __('end_order_time'),operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'ppaccount.status', title: __('Status'), searchList: {"on":__('Status on'),"off":__('Status off'),"limited":__('Status limited'),"limited180":__('Status limited180'),"offline":__('Status Offline')}, formatter: Table.api.formatter.status},
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
