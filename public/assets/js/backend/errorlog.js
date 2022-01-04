define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'errorlog/index' + location.search,
                    add_url: 'errorlog/add',
                    edit_url: 'errorlog/edit',
                    del_url: 'errorlog/del',
                    multi_url: 'errorlog/multi',
                    import_url: 'errorlog/import',
                    table: 'errorlog',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'log_id',
                sortName: 'log_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'log_id', title: __('Log_id')},
                        {field: 'ip', title: __('Ip'), operate: 'LIKE'},
                        {field: 'error', title: __('error'), operate: 'LIKE'},
                        {field: 'ppaccount.ppaccount', title: __('Ppaccount.ppaccount'), operate: 'LIKE'},
                        {field: 'domainmanage.name', title: __('Domainmanage.name'), operate: 'LIKE'},
                        {field: 'createdate', title: __('Createdate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
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
