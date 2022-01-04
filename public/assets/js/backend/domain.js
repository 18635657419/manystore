define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'domain/index' + location.search,
                    add_url: 'domain/add',
                    edit_url: 'domain/edit',
                    del_url: 'domain/del',
                    multi_url: 'domain/multi',
                    import_url: 'domain/import',
                    table: 'domain',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'dmiain_id',
                sortName: 'dmiain_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'dmiain_id', title: __('Dmiain_id')},
                        {field: 'domain', title: __('Domain'), operate: 'LIKE'},
                        {field: 'token', title: __('Token'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"on":__('On'),"off":__('Off')}, formatter: Table.api.formatter.status},
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