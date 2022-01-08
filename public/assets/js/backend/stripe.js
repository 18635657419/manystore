define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stripe/index' + location.search,
                    add_url: 'stripe/add',
                    edit_url: 'stripe/edit',
                    del_url: 'stripe/del',
                    multi_url: 'stripe/multi',
                    import_url: 'stripe/import',
                    table: 'stripe',
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
                        {field: 'id', title: __('Id')},
                        {field: 'email', title: __('Email'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"on":__('Status on'),"pause":__('Status pause'),"off":__('Status off')}, formatter: Table.api.formatter.status},
                        {field: 'account_type', title: __('Account_type'), searchList: {"T7":__('Account_type t7'),"T2":__('Account_type t2')}, formatter: Table.api.formatter.normal},
                        {field: 'createdate', title: __('Createdate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'updatedate', title: __('Updatedate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
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
