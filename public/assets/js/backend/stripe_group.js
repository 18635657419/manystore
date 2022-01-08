define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stripe_group/index' + location.search,
                    add_url: 'stripe_group/add',
                    edit_url: 'stripe_group/edit',
                    del_url: 'stripe_group/del',
                    multi_url: 'stripe_group/multi',
                    import_url: 'stripe_group/import',
                    table: 'stripe_group',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'gropu_id',
                sortName: 'gropu_id',
                searchFormVisible:true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'gropu_id', title: __('Gropu_id')},
                        {field: 'name', title: __('Name')},
                        {field: 'status', title: __('Status'), searchList: {"on":__('Status on'),"off":__('Status off')}, formatter: Table.api.formatter.status},
                        {field: 'token', title: __('Token')},
                        {field: 'createdate', title: __('Createdate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'stripe.email', title: __('Stripe.email'), operate: 'LIKE'},
                        {field: 'stripe.account_type', title: __('Stripe.account_type')},
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
