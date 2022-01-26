define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stripeorder/index' + location.search,
                    add_url: 'stripeorder/add',
                    // edit_url: 'stripeorder/edit',
                    // del_url: 'stripeorder/del',
                    multi_url: 'stripeorder/multi',
                    // import_url: 'stripeorder/import',
                    table: 'stripeorder',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'order_id',
                sortName: 'order_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'order_id', title: __('Order_id')},
                        {field: 'stripe_order_id', title: __('Stripe_order_id'), operate: 'LIKE'},
                        {field: 'ordername', title: __('Ordername'), operate: 'LIKE'},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'status', title: __('Status'), searchList: {"plated":__('Status plated'),"cancal":__('Status cancal'),"ing":__('Status ing')}, formatter: Table.api.formatter.status},
                        {field: 'storename', title: __('Storename'), operate: 'LIKE'},
                        {field: 'proxy_order_id', title: __('Proxy_order_id'), operate: 'LIKE'},
                        {field: 'proxyemail', title: __('Proxyemail'), operate: 'LIKE'},
                        {field: 'zip', title: __('Zip'), operate: 'LIKE'},
                        {field: 'country', title: __('Country'), operate: 'LIKE'},
                        {field: 'city', title: __('City'), operate: 'LIKE'},
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'username', title: __('Username'), operate: 'LIKE'},
                        {field: 'phone', title: __('Phone'), operate: 'LIKE'},
                        {field: 'stripe.email', title: __('stripe帐号'), operate: 'LIKE'},
                        {field: 'createdate', title: __('Createdate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},

                        // {field: 'updatedate', title: __('Updatedate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},


                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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